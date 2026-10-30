<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\JugadorController;
use App\Http\Controllers\PartidoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Visible para todos los usuarios autenticados (solo lectura).
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/partidos', [PartidoController::class, 'index'])->name('partidos.index');

    // Administrador y árbitro: cargar resultados.
    Route::middleware('can:cargar-resultados')->group(function () {
        Route::get('/partidos/{partido}/marcador', [PartidoController::class, 'score'])
            ->name('partidos.score');

        Route::patch('/partidos/{partido}/marcador', [PartidoController::class, 'updateScore'])
            ->name('partidos.updateScore');
    });

    // Solo el administrador: gestión completa.
    Route::middleware('can:gestionar')->group(function () {
        Route::resource('equipos', EquipoController::class)
            ->except('show')
            ->parameters(['equipos' => 'equipo']);

        Route::resource('jugadores', JugadorController::class)
            ->except('show')
            ->parameters(['jugadores' => 'jugador']);

        Route::resource('partidos', PartidoController::class)
            ->only(['create', 'store', 'destroy'])
            ->parameters(['partidos' => 'partido']);
    });
});
