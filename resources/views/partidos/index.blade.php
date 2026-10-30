@extends('layouts.app')

@section('title', 'Partidos')

@section('content')
    @php($puedeAccionar = auth()->user()->can('gestionar') || auth()->user()->can('cargar-resultados'))

    <div class="page-header">
        <h1 class="page-title">📅 Partidos de la Liga</h1>
        @can('gestionar')
            <a href="{{ route('partidos.create') }}" class="btn btn-primary">+ Programar Partido</a>
        @endcan
    </div>

    <div class="card filter-card">
        <div class="filter-form">
            <span class="filter-label">Filtrar por estado:</span>

            <a href="{{ route('partidos.index') }}"
               class="btn btn-sm {{ is_null($estado) ? 'btn-primary' : 'btn-secondary' }}">
                Todos
            </a>

            @foreach (\App\Models\Partido::ESTADOS as $clave => $etiqueta)
                <a href="{{ route('partidos.index', ['estado' => $clave]) }}"
                   class="btn btn-sm {{ $estado === $clave ? 'btn-primary' : 'btn-secondary' }}">
                    {{ $etiqueta }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Fecha / Hora</th>
                        <th class="text-right">Equipo Local</th>
                        <th class="text-center">Marcador</th>
                        <th>Equipo Visitante</th>
                        <th>Lugar / Sede</th>
                        <th>Estado</th>
                        @if ($puedeAccionar)
                            <th class="text-right">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($partidos as $partido)
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
                            <td>{{ $partido->lugar }}</td>
                            <td>
                                <span class="badge badge-{{ $partido->estado }}">
                                    {{ $partido->estado_etiqueta }}
                                </span>
                            </td>
                            @if ($puedeAccionar)
                                <td>
                                    <div class="table-actions">
                                        @can('cargar-resultados')
                                            <a href="{{ route('partidos.score', $partido) }}" class="btn btn-success btn-sm">
                                                Resultado
                                            </a>
                                        @endcan

                                        @can('gestionar')
                                            <form
                                                method="POST"
                                                action="{{ route('partidos.destroy', $partido) }}"
                                                onsubmit="return confirm('¿Eliminar este partido?');"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $puedeAccionar ? 7 : 6 }}" class="empty-state">
                                No hay partidos registrados con este estado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
