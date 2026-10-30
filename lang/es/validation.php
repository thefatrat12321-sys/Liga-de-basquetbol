<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mensajes de validación
    |--------------------------------------------------------------------------
    */

    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser texto.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'email' => 'El campo :attribute debe ser un correo electrónico válido.',
    'unique' => 'El valor de :attribute ya está registrado.',
    'exists' => 'El valor seleccionado en :attribute no existe.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'different' => 'Los campos :attribute deben ser diferentes.',
    'in' => 'El valor seleccionado en :attribute no es válido.',
    'date' => 'El campo :attribute no es una fecha válida.',
    'date_format' => 'El campo :attribute no tiene el formato :format.',
    'max' => [
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string' => 'El campo :attribute no debe tener más de :max caracteres.',
    ],
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'between' => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Nombres legibles de los campos
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => 'nombre',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'nombre' => 'nombre',
        'ciudad' => 'ciudad',
        'entrenador' => 'entrenador',
        'equipo_id' => 'equipo',
        'numero' => 'dorsal',
        'posicion' => 'posición',
        'edad' => 'edad',
        'altura' => 'altura',
        'equipo_local_id' => 'equipo local',
        'equipo_visitante_id' => 'equipo visitante',
        'fecha' => 'fecha',
        'hora' => 'hora',
        'lugar' => 'lugar',
        'puntos_local' => 'puntos del equipo local',
        'puntos_visitante' => 'puntos del equipo visitante',
        'estado' => 'estado',
    ],

];
