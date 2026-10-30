<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Partido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PreviewCommandTest extends TestCase
{
    use RefreshDatabase;

    private string $salida;

    protected function setUp(): void
    {
        parent::setUp();

        $this->salida = storage_path('framework/testing/preview');
        File::deleteDirectory($this->salida);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->salida);

        parent::tearDown();
    }

    public function test_genera_las_paginas_estaticas_con_los_datos_actuales(): void
    {
        $local = Equipo::create(['nombre' => 'Halcones Dorados', 'ciudad' => 'Ciudad Central']);
        $visitante = Equipo::create(['nombre' => 'Toros Salvajes', 'ciudad' => 'Valle Norte']);

        Partido::create([
            'equipo_local_id' => $local->id,
            'equipo_visitante_id' => $visitante->id,
            'fecha' => '2026-11-05',
            'hora' => '18:30',
            'lugar' => 'Coliseo Municipal Central',
            'puntos_local' => 88,
            'puntos_visitante' => 80,
            'estado' => 'finalizado',
        ]);

        $this->artisan('liga:preview', ['--output' => $this->salida])->assertSuccessful();

        foreach (['index.html', 'partidos.html', 'partidos-finalizado.html', 'css/style.css', '.nojekyll'] as $archivo) {
            $this->assertFileExists($this->salida.'/'.$archivo);
        }

        $index = File::get($this->salida.'/index.html');
        $this->assertStringContainsString('Halcones Dorados', $index);
        $this->assertStringContainsString('Tabla de Posiciones', $index);
        $this->assertStringContainsString('Vista previa estática', $index);

        $partidos = File::get($this->salida.'/partidos.html');
        $this->assertStringContainsString('Coliseo Municipal Central', $partidos);
        $this->assertStringNotContainsString('Acciones', $partidos);
        $this->assertStringNotContainsString('Eliminar', $partidos);
    }

    public function test_la_vista_previa_no_expone_formularios_ni_rutas_de_la_aplicacion(): void
    {
        Equipo::create(['nombre' => 'Halcones Dorados', 'ciudad' => 'Ciudad Central']);

        $this->artisan('liga:preview', ['--output' => $this->salida])->assertSuccessful();

        $index = File::get($this->salida.'/index.html');

        $this->assertStringNotContainsString('<form', $index);
        $this->assertStringNotContainsString('/login', $index);
        $this->assertStringNotContainsString('/dashboard', $index);
    }
}
