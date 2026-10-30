<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartidoTest extends TestCase
{
    use RefreshDatabase;

    private function equipos(): array
    {
        return [
            Equipo::create(['nombre' => 'Halcones Dorados', 'ciudad' => 'Ciudad Central']),
            Equipo::create(['nombre' => 'Toros Salvajes', 'ciudad' => 'Valle Norte']),
        ];
    }

    public function test_user_can_schedule_a_partido(): void
    {
        [$local, $visitante] = $this->equipos();

        $this->actingAs(User::factory()->create())
            ->post('/partidos', [
                'equipo_local_id' => $local->id,
                'equipo_visitante_id' => $visitante->id,
                'fecha' => '2026-11-05',
                'hora' => '18:30',
                'lugar' => 'Coliseo Municipal Central',
            ])
            ->assertRedirect(route('partidos.index'));

        $this->assertDatabaseHas('partidos', [
            'equipo_local_id' => $local->id,
            'equipo_visitante_id' => $visitante->id,
            'estado' => 'programado',
        ]);
    }

    public function test_local_and_visitante_must_be_different(): void
    {
        [$equipo] = $this->equipos();

        $this->actingAs(User::factory()->create())
            ->post('/partidos', [
                'equipo_local_id' => $equipo->id,
                'equipo_visitante_id' => $equipo->id,
                'fecha' => '2026-11-05',
                'hora' => '18:30',
                'lugar' => 'Coliseo Municipal Central',
            ])
            ->assertSessionHasErrors('equipo_local_id');
    }

    public function test_user_can_update_the_score(): void
    {
        [$local, $visitante] = $this->equipos();

        $partido = Partido::create([
            'equipo_local_id' => $local->id,
            'equipo_visitante_id' => $visitante->id,
            'fecha' => '2026-11-05',
            'hora' => '18:30',
            'lugar' => 'Coliseo Municipal Central',
        ]);

        $this->actingAs(User::factory()->create())
            ->patch(route('partidos.updateScore', $partido), [
                'puntos_local' => 88,
                'puntos_visitante' => 80,
                'estado' => 'finalizado',
            ])
            ->assertRedirect(route('partidos.index'));

        $this->assertDatabaseHas('partidos', [
            'id' => $partido->id,
            'puntos_local' => 88,
            'puntos_visitante' => 80,
            'estado' => 'finalizado',
        ]);
    }

    public function test_a_finished_partido_cannot_end_in_a_tie(): void
    {
        [$local, $visitante] = $this->equipos();

        $partido = Partido::create([
            'equipo_local_id' => $local->id,
            'equipo_visitante_id' => $visitante->id,
            'fecha' => '2026-11-05',
            'hora' => '18:30',
            'lugar' => 'Coliseo Municipal Central',
        ]);

        $this->actingAs(User::factory()->create())
            ->patch(route('partidos.updateScore', $partido), [
                'puntos_local' => 80,
                'puntos_visitante' => 80,
                'estado' => 'finalizado',
            ])
            ->assertSessionHasErrors('puntos_local');
    }
}
