<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePartidoRequest extends FormRequest
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
            'equipo_local_id' => [
                'required',
                'integer',
                'exists:equipos,id',
                'different:equipo_visitante_id',
            ],
            'equipo_visitante_id' => ['required', 'integer', 'exists:equipos,id'],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'lugar' => ['required', 'string', 'max:150'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'equipo_local_id' => 'equipo local',
            'equipo_visitante_id' => 'equipo visitante',
            'fecha' => 'fecha',
            'hora' => 'hora',
            'lugar' => 'lugar',
        ];
    }
}
