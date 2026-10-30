@extends('layouts.app')

@section('title', 'Programar Partido')

@section('content')
    <div class="page-header">
        <h1 class="page-title">📅 Programar Nuevo Partido</h1>
        <a href="{{ route('partidos.index') }}" class="btn btn-secondary">&larr; Volver a Partidos</a>
    </div>

    <div class="card form-card">
        <form method="POST" action="{{ route('partidos.store') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="equipo_local_id">Equipo local *</label>
                    <select name="equipo_local_id" id="equipo_local_id" class="form-control" required>
                        <option value="">Seleccione un equipo</option>
                        @foreach ($equipos as $equipo)
                            <option value="{{ $equipo->id }}" @selected((int) old('equipo_local_id') === $equipo->id)>
                                {{ $equipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="equipo_visitante_id">Equipo visitante *</label>
                    <select name="equipo_visitante_id" id="equipo_visitante_id" class="form-control" required>
                        <option value="">Seleccione un equipo</option>
                        @foreach ($equipos as $equipo)
                            <option value="{{ $equipo->id }}" @selected((int) old('equipo_visitante_id') === $equipo->id)>
                                {{ $equipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="fecha">Fecha *</label>
                    <input
                        type="date"
                        id="fecha"
                        name="fecha"
                        class="form-control"
                        value="{{ old('fecha', now()->format('Y-m-d')) }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="hora">Hora *</label>
                    <input
                        type="time"
                        id="hora"
                        name="hora"
                        class="form-control"
                        value="{{ old('hora', '18:00') }}"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="lugar">Lugar / Sede *</label>
                <input
                    type="text"
                    id="lugar"
                    name="lugar"
                    class="form-control"
                    value="{{ old('lugar') }}"
                    placeholder="Ej: Coliseo Municipal Central"
                    required
                >
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Programar Partido</button>
                <a href="{{ route('partidos.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
