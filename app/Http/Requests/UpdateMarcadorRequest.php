<?php

namespace App\Http\Requests;

use App\Models\Partido;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateMarcadorRequest extends FormRequest
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
            'puntos_local' => ['required', 'integer', 'min:0', 'max:999'],
            'puntos_visitante' => ['required', 'integer', 'min:0', 'max:999'],
            'estado' => ['required', 'string', Rule::in(array_keys(Partido::ESTADOS))],
        ];
    }

    /**
     * En básquetbol no existen los empates en un partido finalizado.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $empate = (int) $this->input('puntos_local') === (int) $this->input('puntos_visitante');

                if ($this->input('estado') === 'finalizado' && $empate) {
                    $validator->errors()->add(
                        'puntos_local',
                        'Un partido finalizado no puede terminar empatado.'
                    );
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'puntos_local' => 'puntos del equipo local',
            'puntos_visitante' => 'puntos del equipo visitante',
            'estado' => 'estado',
        ];
    }
}
