<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jugador extends Model
{
    use HasFactory;

    public const POSICIONES = [
        'Base',
        'Escolta',
        'Alero',
        'Ala-Pívot',
        'Pívot',
    ];

    protected $table = 'jugadores';

    protected $fillable = [
        'equipo_id',
        'nombre',
        'numero',
        'posicion',
        'edad',
        'altura',
    ];

    protected function casts(): array
    {
        return [
            'numero' => 'integer',
            'edad' => 'integer',
            'altura' => 'decimal:2',
        ];
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }
}
