@extends('preview.layout')

@section('title', 'Estadísticas')

@section('content')
    <div class="page-header">
        <h1 class="page-title">📊 Estadísticas de la Liga</h1>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="label">Partidos totales</span>
            <span class="value">{{ $stats['total_partidos'] }}</span>
        </div>
        <div class="stat-card">
            <span class="label">Finalizados</span>
            <span class="value">{{ $stats['finalizados'] }}</span>
        </div>
        <div class="stat-card">
            <span class="label">Programados</span>
            <span class="value">{{ $stats['programados'] }}</span>
        </div>
        <div class="stat-card">
            <span class="label">Equipos</span>
            <span class="value">{{ $stats['total_equipos'] }}</span>
        </div>
        <div class="stat-card">
            <span class="label">Jugadores</span>
            <span class="value">{{ $stats['total_jugadores'] }}</span>
        </div>
        <div class="stat-card">
            <span class="label">Promedio de puntos</span>
            <span class="value">{{ number_format($stats['promedio_puntos'], 1) }}</span>
        </div>
    </div>

    <div class="card">
        <h2 class="card-title">🏆 Tabla de Posiciones</h2>
        <p class="card-subtitle">2 puntos por victoria, 1 punto por derrota (criterio FIBA).</p>

        @include('partials.tabla-posiciones')
    </div>

    <div class="card">
        <h2 class="card-title">📅 Últimos Partidos</h2>

        @include('partials.tabla-ultimos-partidos')
    </div>
@endsection
