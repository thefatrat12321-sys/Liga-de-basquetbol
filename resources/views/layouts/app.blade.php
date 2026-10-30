<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inicio') · Liga de Básquetbol</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

@auth
    <nav class="navbar">
        <div class="nav-brand">
            <a href="{{ route('dashboard') }}">🏀 Liga de Básquet</a>
        </div>

        <ul class="nav-links">
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Estadísticas / Tabla
                </a>
            </li>
            @can('gestionar')
                <li>
                    <a href="{{ route('equipos.index') }}" class="{{ request()->routeIs('equipos.*') ? 'active' : '' }}">
                        Equipos
                    </a>
                </li>
                <li>
                    <a href="{{ route('jugadores.index') }}" class="{{ request()->routeIs('jugadores.*') ? 'active' : '' }}">
                        Jugadores
                    </a>
                </li>
            @endcan
            <li>
                <a href="{{ route('partidos.index') }}" class="{{ request()->routeIs('partidos.*') ? 'active' : '' }}">
                    Partidos
                </a>
            </li>
        </ul>

        <div class="nav-user">
            @cannot('cargar-resultados')
                <span class="user-badge user-badge-readonly">Solo lectura</span>
            @endcannot

            <span class="user-badge">
                {{ auth()->user()->name }} ({{ auth()->user()->rolEtiqueta() }})
            </span>

            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('¿Desea cerrar sesión?');">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm">Cerrar Sesión</button>
            </form>
        </div>
    </nav>
@endauth

<main class="container">
    @include('partials.alerts')

    @yield('content')
</main>

<footer class="app-footer">
    Sistema de Gestión de Liga de Básquetbol · Laravel {{ Illuminate\Foundation\Application::VERSION }}
</footer>

</body>
</html>
