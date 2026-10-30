<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Solo el administrador puede crear, editar y eliminar registros.
        Gate::define('gestionar', fn (User $user) => $user->isAdmin());

        // El árbitro (y el administrador) pueden cargar resultados de partidos.
        Gate::define(
            'cargar-resultados',
            fn (User $user) => in_array($user->rol, ['admin', 'arbitro'], true)
        );
    }
}
