<?php

namespace Database\Seeders;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Partido;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Carga el usuario administrador y los datos de demostración de la liga.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@liga.com'],
            [
                'name' => 'Administrador Liga',
                'password' => 'admin123',
                'rol' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'arbitro@liga.com'],
            [
                'name' => 'Árbitro de Mesa',
                'password' => 'arbitro123',
                'rol' => 'arbitro',
            ]
        );

        User::updateOrCreate(
            ['email' => 'espectador@liga.com'],
            [
                'name' => 'Espectador',
                'password' => 'usuario123',
                'rol' => 'usuario',
            ]
        );

        $equipos = [
            ['nombre' => 'Halcones Dorados', 'ciudad' => 'Ciudad Central', 'entrenador' => 'Carlos Martínez'],
            ['nombre' => 'Toros Salvajes', 'ciudad' => 'Valle Norte', 'entrenador' => 'Roberto Gómez'],
            ['nombre' => 'Celtas del Sur', 'ciudad' => 'Metrópolis', 'entrenador' => 'Alejandro Fernández'],
            ['nombre' => 'Tiburones Azules', 'ciudad' => 'Costa Brava', 'entrenador' => 'Miguel Ángel Soto'],
        ];

        foreach ($equipos as $equipo) {
            Equipo::updateOrCreate(['nombre' => $equipo['nombre']], $equipo);
        }

        $jugadores = [
            ['equipo' => 'Halcones Dorados', 'nombre' => 'Javier Pérez', 'numero' => 23, 'posicion' => 'Escolta', 'edad' => 24, 'altura' => 1.95],
            ['equipo' => 'Halcones Dorados', 'nombre' => 'Andrés Luna', 'numero' => 7, 'posicion' => 'Base', 'edad' => 22, 'altura' => 1.85],
            ['equipo' => 'Halcones Dorados', 'nombre' => 'Mario Silva', 'numero' => 15, 'posicion' => 'Ala-Pívot', 'edad' => 27, 'altura' => 2.02],
            ['equipo' => 'Toros Salvajes', 'nombre' => 'Luis Morales', 'numero' => 10, 'posicion' => 'Alero', 'edad' => 25, 'altura' => 1.98],
            ['equipo' => 'Toros Salvajes', 'nombre' => 'Diego Castro', 'numero' => 32, 'posicion' => 'Pívot', 'edad' => 29, 'altura' => 2.08],
            ['equipo' => 'Celtas del Sur', 'nombre' => 'Esteban Ruiz', 'numero' => 11, 'posicion' => 'Base', 'edad' => 21, 'altura' => 1.82],
            ['equipo' => 'Celtas del Sur', 'nombre' => 'Gabriel Vargas', 'numero' => 8, 'posicion' => 'Escolta', 'edad' => 26, 'altura' => 1.93],
            ['equipo' => 'Tiburones Azules', 'nombre' => 'Fernando Ríos', 'numero' => 5, 'posicion' => 'Alero', 'edad' => 23, 'altura' => 1.96],
        ];

        foreach ($jugadores as $datos) {
            $equipo = Equipo::where('nombre', $datos['equipo'])->firstOrFail();

            Jugador::updateOrCreate(
                ['equipo_id' => $equipo->id, 'numero' => $datos['numero']],
                [
                    'nombre' => $datos['nombre'],
                    'posicion' => $datos['posicion'],
                    'edad' => $datos['edad'],
                    'altura' => $datos['altura'],
                ]
            );
        }

        $partidos = [
            ['local' => 'Halcones Dorados', 'visitante' => 'Toros Salvajes', 'fecha' => '2026-10-15', 'hora' => '18:00', 'lugar' => 'Coliseo Municipal Central', 'puntos_local' => 85, 'puntos_visitante' => 78, 'estado' => 'finalizado'],
            ['local' => 'Celtas del Sur', 'visitante' => 'Tiburones Azules', 'fecha' => '2026-10-16', 'hora' => '20:00', 'lugar' => 'Estadio Bicentenario', 'puntos_local' => 92, 'puntos_visitante' => 88, 'estado' => 'finalizado'],
            ['local' => 'Toros Salvajes', 'visitante' => 'Celtas del Sur', 'fecha' => '2026-10-22', 'hora' => '19:00', 'lugar' => 'Polideportivo Norte', 'puntos_local' => 68, 'puntos_visitante' => 74, 'estado' => 'finalizado'],
            ['local' => 'Halcones Dorados', 'visitante' => 'Tiburones Azules', 'fecha' => '2026-11-05', 'hora' => '18:30', 'lugar' => 'Coliseo Municipal Central', 'puntos_local' => 0, 'puntos_visitante' => 0, 'estado' => 'programado'],
        ];

        foreach ($partidos as $datos) {
            $local = Equipo::where('nombre', $datos['local'])->firstOrFail();
            $visitante = Equipo::where('nombre', $datos['visitante'])->firstOrFail();

            $existente = Partido::query()
                ->where('equipo_local_id', $local->id)
                ->where('equipo_visitante_id', $visitante->id)
                ->whereDate('fecha', $datos['fecha'])
                ->where('hora', $datos['hora'])
                ->first();

            $atributos = [
                'lugar' => $datos['lugar'],
                'puntos_local' => $datos['puntos_local'],
                'puntos_visitante' => $datos['puntos_visitante'],
                'estado' => $datos['estado'],
            ];

            if ($existente) {
                $existente->update($atributos);
            } else {
                Partido::create([
                    'equipo_local_id' => $local->id,
                    'equipo_visitante_id' => $visitante->id,
                    'fecha' => $datos['fecha'],
                    'hora' => $datos['hora'],
                    ...$atributos,
                ]);
            }
        }
    }
}
