<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JugadorTest extends TestCase
{
    use RefreshDatabase;

    private function equipo(string $nombre = 'Halcones Dorados'): Equipo
    {
        return Equipo::create([
            'nombre' => $nombre,
            'ciudad' => 'Ciudad Central',
        ]);
    }

    public function test_user_can_create_a_jugador(): void
    {
        $equipo = $this->equipo();

        $this->actingAs(User::factory()->create())
            ->post('/jugadores', [
                'equipo_id' => $equipo->id,
                'nombre' => 'Javier Pérez',
                'numero' => 23,
                'posicion' => 'Escolta',
                'edad' => 24,
                'altura' => 1.95,
            ])
            ->assertRedirect(route('jugadores.index'));

        $this->assertDatabaseHas('jugadores', [
            'equipo_id' => $equipo->id,
            'nombre' => 'Javier Pérez',
            'numero' => 23,
        ]);
    }

    public function test_dorsal_must_be_unique_within_the_same_equipo(): void
    {
        $equipo = $this->equipo();

        Jugador::create([
            'equipo_id' => $equipo->id,
            'nombre' => 'Javier Pérez',
            'numero' => 23,
            'posicion' => 'Escolta',
        ]);

        $this->actingAs(User::factory()->create())
            ->post('/jugadores', [
                'equipo_id' => $equipo->id,
                'nombre' => 'Otro Jugador',
                'numero' => 23,
                'posicion' => 'Base',
            ])
            ->assertSessionHasErrors('numero');
    }

    public function test_same_dorsal_is_allowed_in_another_equipo(): void
    {
        $equipoA = $this->equipo('Halcones Dorados');
        $equipoB = $this->equipo('Toros Salvajes');

        Jugador::create([
            'equipo_id' => $equipoA->id,
            'nombre' => 'Javier Pérez',
            'numero' => 23,
            'posicion' => 'Escolta',
        ]);

        $this->actingAs(User::factory()->create())
            ->post('/jugadores', [
                'equipo_id' => $equipoB->id,
                'nombre' => 'Luis Morales',
                'numero' => 23,
                'posicion' => 'Alero',
            ])
            ->assertRedirect(route('jugadores.index'));

        $this->assertDatabaseHas('jugadores', [
            'equipo_id' => $equipoB->id,
            'numero' => 23,
        ]);
    }

    public function test_jugadores_can_be_filtered_by_equipo(): void
    {
        $equipoA = $this->equipo('Halcones Dorados');
        $equipoB = $this->equipo('Toros Salvajes');

        Jugador::create(['equipo_id' => $equipoA->id, 'nombre' => 'Javier Pérez', 'numero' => 23, 'posicion' => 'Escolta']);
        Jugador::create(['equipo_id' => $equipoB->id, 'nombre' => 'Luis Morales', 'numero' => 10, 'posicion' => 'Alero']);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('jugadores.index', ['equipo_id' => $equipoB->id]));

        $response->assertOk();
        $response->assertSee('Luis Morales');
        $response->assertDontSee('Javier Pérez');
    }
}
