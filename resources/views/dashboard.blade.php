@extends('layouts.app')

@section('title', 'Estadísticas')

@section('content')
    <div class="page-header">
        <h1 class="page-title">📊 Estadísticas de la Liga</h1>
        @can('gestionar')
            <a href="{{ route('partidos.create') }}" class="btn btn-primary">+ Programar Partido</a>
        @endcan
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

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Equipo</th>
                        <th class="text-center">PJ</th>
                        <th class="text-center">PG</th>
                        <th class="text-center">PP</th>
                        <th class="text-center">PF</th>
                        <th class="text-center">PC</th>
                        <th class="text-center">DIF</th>
                        <th class="text-center">Pts</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tabla as $fila)
                        <tr class="{{ $fila['posicion'] === 1 ? 'leader-row' : '' }}">
                            <td>{{ $fila['posicion'] }}</td>
                            <td>
                                <strong>{{ $fila['nombre'] }}</strong>
                                @if ($fila['posicion'] === 1)
                                    🥇
                                @endif
                            </td>
                            <td class="text-center">{{ $fila['pj'] }}</td>
                            <td class="text-center">{{ $fila['pg'] }}</td>
                            <td class="text-center">{{ $fila['pp'] }}</td>
                            <td class="text-center">{{ $fila['pf'] }}</td>
                            <td class="text-center">{{ $fila['pc'] }}</td>
                            <td class="text-center">{{ $fila['dif'] > 0 ? '+' . $fila['dif'] : $fila['dif'] }}</td>
                            <td class="text-center"><strong>{{ $fila['puntos'] }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty-state">
                                Todavía no hay equipos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h2 class="card-title">📅 Últimos Partidos</h2>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th class="text-right">Local</th>
                        <th class="text-center">Marcador</th>
                        <th>Visitante</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($partidosRecientes as $partido)
                        <tr>
                            <td>
                                <strong>{{ $partido->fecha->format('d/m/Y') }}</strong><br>
                                <span class="text-muted">{{ $partido->hora_corta }} hrs</span>
                            </td>
                            <td class="text-right">{{ $partido->equipoLocal->nombre }}</td>
                            <td class="text-center score">
                                {{ $partido->puntos_local }} - {{ $partido->puntos_visitante }}
                            </td>
                            <td>{{ $partido->equipoVisitante->nombre }}</td>
                            <td>
                                <span class="badge badge-{{ $partido->estado }}">
                                    {{ $partido->estado_etiqueta }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                No hay partidos registrados todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
