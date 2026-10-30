<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Partido extends Model
{
    use HasFactory;

    public const ESTADOS = [
        'programado' => 'Programado',
        'en_juego' => 'En Juego',
        'finalizado' => 'Finalizado',
        'cancelado' => 'Cancelado',
    ];

    protected $table = 'partidos';

    protected $fillable = [
        'equipo_local_id',
        'equipo_visitante_id',
        'fecha',
        'hora',
        'lugar',
        'puntos_local',
        'puntos_visitante',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'puntos_local' => 'integer',
            'puntos_visitante' => 'integer',
        ];
    }

    public function equipoLocal(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_local_id');
    }

    public function equipoVisitante(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_visitante_id');
    }

    public function scopeDeEstado(Builder $query, ?string $estado): Builder
    {
        return $estado ? $query->where('estado', $estado) : $query;
    }

    public function scopeFinalizados(Builder $query): Builder
    {
        return $query->where('estado', 'finalizado');
    }

    public function getHoraCortaAttribute(): string
    {
        return substr((string) $this->hora, 0, 5);
    }

    public function getEstadoEtiquetaAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }
}
