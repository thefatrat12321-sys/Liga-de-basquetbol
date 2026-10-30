<?php

namespace App\Services;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Partido;
use Illuminate\Support\Collection;

/**
 * Calcula la tabla de posiciones y las estadísticas generales de la liga.
 *
 * Criterio FIBA: 2 puntos por victoria, 1 punto por derrota.
 * Desempate: diferencia de puntos y luego puntos a favor.
 */
class StandingsService
{
    /**
     * @return Collection<int, array<string, int|string>>
     */
    public function tabla(): Collection
    {
        /** @var array<int, array<string, int|string>> $filas */
        $filas = Equipo::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre'])
            ->mapWithKeys(fn (Equipo $equipo) => [
                $equipo->id => [
                    'id' => $equipo->id,
                    'nombre' => $equipo->nombre,
                    'pj' => 0,
                    'pg' => 0,
                    'pp' => 0,
                    'pf' => 0,
                    'pc' => 0,
                    'dif' => 0,
                    'puntos' => 0,
                ],
            ])
            ->all();

        $partidos = Partido::query()
            ->finalizados()
            ->get(['equipo_local_id', 'equipo_visitante_id', 'puntos_local', 'puntos_visitante']);

        foreach ($partidos as $partido) {
            $local = $partido->equipo_local_id;
            $visitante = $partido->equipo_visitante_id;

            if (! isset($filas[$local], $filas[$visitante])) {
                continue;
            }

            $filas[$local]['pj']++;
            $filas[$visitante]['pj']++;
            $filas[$local]['pf'] += $partido->puntos_local;
            $filas[$local]['pc'] += $partido->puntos_visitante;
            $filas[$visitante]['pf'] += $partido->puntos_visitante;
            $filas[$visitante]['pc'] += $partido->puntos_local;

            if ($partido->puntos_local > $partido->puntos_visitante) {
                $filas[$local]['pg']++;
                $filas[$local]['puntos'] += 2;
                $filas[$visitante]['pp']++;
                $filas[$visitante]['puntos'] += 1;
            } else {
                $filas[$visitante]['pg']++;
                $filas[$visitante]['puntos'] += 2;
                $filas[$local]['pp']++;
                $filas[$local]['puntos'] += 1;
            }
        }

        return collect($filas)
            ->map(function (array $fila) {
                $fila['dif'] = $fila['pf'] - $fila['pc'];

                return $fila;
            })
            ->sortBy([
                ['puntos', 'desc'],
                ['dif', 'desc'],
                ['pf', 'desc'],
                ['nombre', 'asc'],
            ])
            ->values()
            ->map(function (array $fila, int $indice) {
                $fila['posicion'] = $indice + 1;

                return $fila;
            });
    }

    /**
     * @return array<string, int|float>
     */
    public function estadisticas(): array
    {
        $totalPartidos = Partido::count();
        $finalizados = Partido::finalizados()->count();
        $programados = Partido::where('estado', 'programado')->count();

        $promedioPuntos = 0.0;

        if ($finalizados > 0) {
            $totales = Partido::finalizados()
                ->selectRaw('SUM(puntos_local) as puntos_local, SUM(puntos_visitante) as puntos_visitante')
                ->first();

            $suma = (int) $totales->puntos_local + (int) $totales->puntos_visitante;
            $promedioPuntos = round($suma / $finalizados, 1);
        }

        return [
            'total_partidos' => $totalPartidos,
            'finalizados' => $finalizados,
            'programados' => $programados,
            'total_equipos' => Equipo::count(),
            'total_jugadores' => Jugador::count(),
            'promedio_puntos' => $promedioPuntos,
        ];
    }
}
