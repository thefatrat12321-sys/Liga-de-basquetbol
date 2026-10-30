<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipoTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_manage_equipos(): void
    {
        $this->get('/equipos')->assertRedirect('/login');
    }

    public function test_user_can_create_an_equipo(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/equipos', [
                'nombre' => 'Halcones Dorados',
                'ciudad' => 'Ciudad Central',
                'entrenador' => 'Carlos Martínez',
            ])
            ->assertRedirect(route('equipos.index'));

        $this->assertDatabaseHas('equipos', [
            'nombre' => 'Halcones Dorados',
            'ciudad' => 'Ciudad Central',
        ]);
    }

    public function test_equipo_name_must_be_unique(): void
    {
        Equipo::create([
            'nombre' => 'Halcones Dorados',
            'ciudad' => 'Ciudad Central',
        ]);

        $this->actingAs(User::factory()->create())
            ->post('/equipos', [
                'nombre' => 'Halcones Dorados',
                'ciudad' => 'Otra Ciudad',
            ])
            ->assertSessionHasErrors('nombre');
    }

    public function test_user_can_update_an_equipo(): void
    {
        $equipo = Equipo::create([
            'nombre' => 'Halcones Dorados',
            'ciudad' => 'Ciudad Central',
        ]);

        $this->actingAs(User::factory()->create())
            ->put(route('equipos.update', $equipo), [
                'nombre' => 'Halcones Dorados',
                'ciudad' => 'Ciudad Nueva',
                'entrenador' => 'Nuevo Coach',
            ])
            ->assertRedirect(route('equipos.index'));

        $this->assertDatabaseHas('equipos', [
            'id' => $equipo->id,
            'ciudad' => 'Ciudad Nueva',
            'entrenador' => 'Nuevo Coach',
        ]);
    }

    public function test_user_can_delete_an_equipo(): void
    {
        $equipo = Equipo::create([
            'nombre' => 'Halcones Dorados',
            'ciudad' => 'Ciudad Central',
        ]);

        $this->actingAs(User::factory()->create())
            ->delete(route('equipos.destroy', $equipo))
            ->assertRedirect(route('equipos.index'));

        $this->assertDatabaseMissing('equipos', ['id' => $equipo->id]);
    }
}
