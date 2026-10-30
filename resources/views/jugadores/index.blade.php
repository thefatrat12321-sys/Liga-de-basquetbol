@extends('layouts.app')

@section('title', 'Jugadores')

@section('content')
    <div class="page-header">
        <h1 class="page-title">👤 Jugadores de la Liga</h1>
        <a href="{{ route('jugadores.create', $equipoId ? ['equipo_id' => $equipoId] : []) }}" class="btn btn-primary">
            + Nuevo Jugador
        </a>
    </div>

    <div class="card filter-card">
        <form method="GET" action="{{ route('jugadores.index') }}" class="filter-form">
            <label for="equipo_id">Filtrar por equipo:</label>

            <select name="equipo_id" id="equipo_id" class="form-control">
                <option value="">Todos los equipos</option>
                @foreach ($equipos as $equipo)
                    <option value="{{ $equipo->id }}" @selected($equipoId === $equipo->id)>
                        {{ $equipo->nombre }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-secondary">Filtrar</button>

            @if ($equipoId)
                <a href="{{ route('jugadores.index') }}" class="btn btn-secondary">Limpiar</a>
            @endif
        </form>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th class="text-center">Dorsal</th>
                        <th>Jugador</th>
                        <th>Equipo</th>
                        <th>Posición</th>
                        <th class="text-center">Edad</th>
                        <th class="text-center">Altura</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jugadores as $jugador)
                        <tr>
                            <td class="text-center"><strong>#{{ $jugador->numero }}</strong></td>
                            <td>{{ $jugador->nombre }}</td>
                            <td>{{ $jugador->equipo->nombre }}</td>
                            <td>{{ $jugador->posicion }}</td>
                            <td class="text-center">{{ $jugador->edad ?? '—' }}</td>
                            <td class="text-center">
                                {{ $jugador->altura ? number_format((float) $jugador->altura, 2) . ' m' : '—' }}
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('jugadores.edit', $jugador) }}" class="btn btn-secondary btn-sm">
                                        Editar
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('jugadores.destroy', $jugador) }}"
                                        onsubmit="return confirm('¿Eliminar al jugador {{ $jugador->nombre }}?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                No hay jugadores registrados con este filtro.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
