<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Partido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function usuario(string $rol): User
    {
        return User::factory()->create(['rol' => $rol]);
    }

    private function partido(): Partido
    {
        $local = Equipo::create(['nombre' => 'Halcones Dorados', 'ciudad' => 'Ciudad Central']);
        $visitante = Equipo::create(['nombre' => 'Toros Salvajes', 'ciudad' => 'Valle Norte']);

        return Partido::create([
            'equipo_local_id' => $local->id,
            'equipo_visitante_id' => $visitante->id,
            'fecha' => '2026-11-05',
            'hora' => '18:30',
            'lugar' => 'Coliseo Municipal Central',
        ]);
    }

    public function test_usuario_puede_ver_el_dashboard_y_los_partidos(): void
    {
        $usuario = $this->usuario('usuario');

        $this->actingAs($usuario)->get('/dashboard')->assertOk();
        $this->actingAs($usuario)->get('/partidos')->assertOk();
    }

    public function test_usuario_no_puede_gestionar_equipos(): void
    {
        $usuario = $this->usuario('usuario');

        $this->actingAs($usuario)->get('/equipos')->assertForbidden();
        $this->actingAs($usuario)->get('/equipos/create')->assertForbidden();
        $this->actingAs($usuario)->post('/equipos', [
            'nombre' => 'Equipo Pirata',
            'ciudad' => 'Ninguna',
        ])->assertForbidden();
    }

    public function test_usuario_no_puede_gestionar_jugadores_ni_partidos(): void
    {
        $usuario = $this->usuario('usuario');
        $partido = $this->partido();

        $this->actingAs($usuario)->get('/jugadores')->assertForbidden();
        $this->actingAs($usuario)->get('/partidos/create')->assertForbidden();
        $this->actingAs($usuario)->post('/partidos', [])->assertForbidden();
        $this->actingAs($usuario)->get(route('partidos.score', $partido))->assertForbidden();
        $this->actingAs($usuario)
            ->patch(route('partidos.updateScore', $partido), [
                'puntos_local' => 10,
                'puntos_visitante' => 5,
                'estado' => 'finalizado',
            ])
            ->assertForbidden();
    }

    public function test_arbitro_puede_cargar_resultados_pero_no_gestionar_equipos(): void
    {
        $arbitro = $this->usuario('arbitro');
        $partido = $this->partido();

        $this->actingAs($arbitro)
            ->patch(route('partidos.updateScore', $partido), [
                'puntos_local' => 88,
                'puntos_visitante' => 80,
                'estado' => 'finalizado',
            ])
            ->assertRedirect(route('partidos.index'));

        $this->actingAs($arbitro)->get('/equipos')->assertForbidden();
        $this->actingAs($arbitro)->post('/partidos', [])->assertForbidden();
    }

    public function test_admin_puede_gestionar_todo(): void
    {
        $admin = $this->usuario('admin');

        Equipo::create(['nombre' => 'Halcones Dorados', 'ciudad' => 'Ciudad Central']);
        Equipo::create(['nombre' => 'Toros Salvajes', 'ciudad' => 'Valle Norte']);

        $this->actingAs($admin)->get('/equipos')->assertOk();
        $this->actingAs($admin)->get('/jugadores')->assertOk();
        $this->actingAs($admin)->get('/partidos')->assertOk();
        $this->actingAs($admin)->get('/partidos/create')->assertOk();
    }
}
