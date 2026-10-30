<?php

namespace App\Http\Requests;

use App\Models\Jugador;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJugadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'equipo_id' => ['required', 'integer', 'exists:equipos,id'],
            'nombre' => ['required', 'string', 'max:100'],
            'numero' => [
                'required',
                'integer',
                'between:0,99',
                Rule::unique('jugadores', 'numero')
                    ->where(fn ($query) => $query->where('equipo_id', $this->input('equipo_id'))),
            ],
            'posicion' => ['required', 'string', 'max:50', Rule::in(Jugador::POSICIONES)],
            'edad' => ['nullable', 'integer', 'between:15,60'],
            'altura' => ['nullable', 'numeric', 'between:1,2.5'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'equipo_id' => 'equipo',
            'nombre' => 'nombre del jugador',
            'numero' => 'dorsal',
            'posicion' => 'posición',
            'edad' => 'edad',
            'altura' => 'altura',
        ];
    }
}
