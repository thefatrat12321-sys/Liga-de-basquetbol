@extends('layouts.app')

@section('title', 'Equipos')

@section('content')
    <div class="page-header">
        <h1 class="page-title">🛡️ Equipos de la Liga</h1>
        <a href="{{ route('equipos.create') }}" class="btn btn-primary">+ Nuevo Equipo</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Equipo</th>
                        <th>Ciudad</th>
                        <th>Entrenador</th>
                        <th class="text-center">Jugadores</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($equipos as $equipo)
                        <tr>
                            <td><strong>{{ $equipo->nombre }}</strong></td>
                            <td>{{ $equipo->ciudad }}</td>
                            <td>{{ $equipo->entrenador ?: '—' }}</td>
                            <td class="text-center">
                                <a href="{{ route('jugadores.index', ['equipo_id' => $equipo->id]) }}">
                                    {{ $equipo->jugadores_count }}
                                </a>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('equipos.edit', $equipo) }}" class="btn btn-secondary btn-sm">
                                        Editar
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('equipos.destroy', $equipo) }}"
                                        onsubmit="return confirm('¿Eliminar el equipo {{ $equipo->nombre }} y todos sus jugadores?');"
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
                            <td colspan="5" class="empty-state">
                                No hay equipos registrados. Crea el primero para comenzar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
