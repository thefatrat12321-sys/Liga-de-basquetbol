<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabla de Equipos
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('ciudad', 100);
            $table->string('entrenador', 100)->nullable();
            $table->timestamps();
        });

        // 2. Tabla de Jugadores
        Schema::create('jugadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained('equipos')->cascadeOnDelete();
            $table->string('nombre', 100);
            $table->unsignedInteger('numero'); // Dorsal 0-99
            $table->string('posicion', 50);
            $table->unsignedInteger('edad')->nullable();
            $table->decimal('altura', 3, 2)->nullable(); // Metros: 2.08
            $table->timestamps();

            $table->unique(['equipo_id', 'numero']);
            $table->index('equipo_id');
        });

        // 3. Tabla de Partidos
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_local_id')->constrained('equipos')->cascadeOnDelete();
            $table->foreignId('equipo_visitante_id')->constrained('equipos')->cascadeOnDelete();
            $table->date('fecha');
            $table->time('hora');
            $table->string('lugar', 150);
            $table->unsignedInteger('puntos_local')->default(0);
            $table->unsignedInteger('puntos_visitante')->default(0);
            $table->enum('estado', ['programado', 'en_juego', 'finalizado', 'cancelado'])->default('programado');
            $table->timestamps();

            $table->index('estado');
            $table->index('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidos');
        Schema::dropIfExists('jugadores');
        Schema::dropIfExists('equipos');
    }
};
