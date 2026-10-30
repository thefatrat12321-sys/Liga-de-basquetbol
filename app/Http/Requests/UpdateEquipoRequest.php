<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEquipoRequest extends FormRequest
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
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('equipos', 'nombre')->ignore($this->route('equipo')),
            ],
            'ciudad' => ['required', 'string', 'max:100'],
            'entrenador' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nombre' => 'nombre del equipo',
            'ciudad' => 'ciudad',
            'entrenador' => 'entrenador',
        ];
    }
}
