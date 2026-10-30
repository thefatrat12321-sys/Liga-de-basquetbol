@extends('layouts.app')

@section('title', 'Cargar Resultado')

@section('content')
    <div class="page-header">
        <h1 class="page-title">🏀 Resultado del Partido</h1>
        <a href="{{ route('partidos.index') }}" class="btn btn-secondary">&larr; Volver a Partidos</a>
    </div>

    <div class="card form-card">
        <div class="scoreboard">
            <div class="scoreboard-team">
                <span class="scoreboard-name">{{ $partido->equipoLocal->nombre }}</span>
                <span class="scoreboard-label">Local</span>
            </div>

            <div class="scoreboard-score">
                {{ $partido->puntos_local }} - {{ $partido->puntos_visitante }}
            </div>

            <div class="scoreboard-team">
                <span class="scoreboard-name">{{ $partido->equipoVisitante->nombre }}</span>
                <span class="scoreboard-label">Visitante</span>
            </div>
        </div>

        <p class="card-subtitle text-center">
            {{ $partido->fecha->format('d/m/Y') }} · {{ $partido->hora_corta }} hrs · {{ $partido->lugar }}
        </p>

        <form method="POST" action="{{ route('partidos.updateScore', $partido) }}">
            @csrf
            @method('PATCH')

            <div class="form-row">
                <div class="form-group">
                    <label for="puntos_local">Puntos {{ $partido->equipoLocal->nombre }} *</label>
                    <input
                        type="number"
                        id="puntos_local"
                        name="puntos_local"
                        class="form-control"
                        value="{{ old('puntos_local', $partido->puntos_local) }}"
                        min="0"
                        max="999"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="puntos_visitante">Puntos {{ $partido->equipoVisitante->nombre }} *</label>
                    <input
                        type="number"
                        id="puntos_visitante"
                        name="puntos_visitante"
                        class="form-control"
                        value="{{ old('puntos_visitante', $partido->puntos_visitante) }}"
                        min="0"
                        max="999"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="estado">Estado del partido *</label>
                <select name="estado" id="estado" class="form-control" required>
                    @foreach (\App\Models\Partido::ESTADOS as $clave => $etiqueta)
                        <option value="{{ $clave }}" @selected(old('estado', $partido->estado) === $clave)>
                            {{ $etiqueta }}
                        </option>
                    @endforeach
                </select>
            </div>

            <p class="card-subtitle">
                Recuerda: un partido <strong>finalizado</strong> no puede terminar empatado.
            </p>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Resultado</button>
                <a href="{{ route('partidos.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
