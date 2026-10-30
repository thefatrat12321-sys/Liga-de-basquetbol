<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Partido;
use App\Services\StandingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StandingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_standings_are_calculated_with_fiba_scoring(): void
    {
        $a = Equipo::create(['nombre' => 'Halcones Dorados', 'ciudad' => 'Ciudad Central']);
        $b = Equipo::create(['nombre' => 'Toros Salvajes', 'ciudad' => 'Valle Norte']);
        $c = Equipo::create(['nombre' => 'Celtas del Sur', 'ciudad' => 'Metrópolis']);

        Partido::create([
            'equipo_local_id' => $a->id,
            'equipo_visitante_id' => $b->id,
            'fecha' => '2026-10-15',
            'hora' => '18:00',
            'lugar' => 'Coliseo Central',
            'puntos_local' => 85,
            'puntos_visitante' => 78,
            'estado' => 'finalizado',
        ]);

        Partido::create([
            'equipo_local_id' => $b->id,
            'equipo_visitante_id' => $c->id,
            'fecha' => '2026-10-16',
            'hora' => '19:00',
            'lugar' => 'Polideportivo Norte',
            'puntos_local' => 70,
            'puntos_visitante' => 60,
            'estado' => 'finalizado',
        ]);

        $tabla = (new StandingsService)->tabla();

        $this->assertSame('Toros Salvajes', $tabla[0]['nombre']);
        $this->assertSame(3, $tabla[0]['puntos']);
        $this->assertSame(1, $tabla[0]['posicion']);

        $this->assertSame('Halcones Dorados', $tabla[1]['nombre']);
        $this->assertSame(2, $tabla[1]['puntos']);
        $this->assertSame(1, $tabla[1]['pj']);
        $this->assertSame(7, $tabla[1]['dif']);

        $this->assertSame('Celtas del Sur', $tabla[2]['nombre']);
        $this->assertSame(1, $tabla[2]['puntos']);
    }

    public function test_general_statistics_are_calculated(): void
    {
        $a = Equipo::create(['nombre' => 'Halcones Dorados', 'ciudad' => 'Ciudad Central']);
        $b = Equipo::create(['nombre' => 'Toros Salvajes', 'ciudad' => 'Valle Norte']);

        Jugador::create(['equipo_id' => $a->id, 'nombre' => 'Javier Pérez', 'numero' => 23, 'posicion' => 'Escolta']);

        Partido::create([
            'equipo_local_id' => $a->id,
            'equipo_visitante_id' => $b->id,
            'fecha' => '2026-10-15',
            'hora' => '18:00',
            'lugar' => 'Coliseo Central',
            'puntos_local' => 80,
            'puntos_visitante' => 70,
            'estado' => 'finalizado',
        ]);

        Partido::create([
            'equipo_local_id' => $a->id,
            'equipo_visitante_id' => $b->id,
            'fecha' => '2026-11-05',
            'hora' => '18:00',
            'lugar' => 'Coliseo Central',
            'estado' => 'programado',
        ]);

        $stats = (new StandingsService)->estadisticas();

        $this->assertSame(2, $stats['total_partidos']);
        $this->assertSame(1, $stats['finalizados']);
        $this->assertSame(1, $stats['programados']);
        $this->assertSame(2, $stats['total_equipos']);
        $this->assertSame(1, $stats['total_jugadores']);
        $this->assertEquals(150.0, $stats['promedio_puntos']);
    }
}
