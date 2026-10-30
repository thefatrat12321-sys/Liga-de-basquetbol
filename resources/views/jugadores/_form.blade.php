@php
    $jugador = $jugador ?? null;
    $equipoActual = old('equipo_id', $jugador?->equipo_id ?? $equipoSeleccionado ?? null);
@endphp

<div class="form-row">
    <div class="form-group">
        <label for="equipo_id">Equipo *</label>
        <select name="equipo_id" id="equipo_id" class="form-control" required>
            <option value="">Seleccione un equipo</option>
            @foreach ($equipos as $equipo)
                <option value="{{ $equipo->id }}" @selected((int) $equipoActual === $equipo->id)>
                    {{ $equipo->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="numero">Dorsal (0-99) *</label>
        <input
            type="number"
            id="numero"
            name="numero"
            class="form-control"
            value="{{ old('numero', $jugador?->numero) }}"
            min="0"
            max="99"
            required
        >
    </div>
</div>

<div class="form-group">
    <label for="nombre">Nombre del jugador *</label>
    <input
        type="text"
        id="nombre"
        name="nombre"
        class="form-control"
        value="{{ old('nombre', $jugador?->nombre) }}"
        placeholder="Ej: Javier Pérez"
        required
        autofocus
    >
</div>

<div class="form-group">
    <label for="posicion">Posición *</label>
    <select name="posicion" id="posicion" class="form-control" required>
        <option value="">Seleccione una posición</option>
        @foreach (\App\Models\Jugador::POSICIONES as $posicion)
            <option value="{{ $posicion }}" @selected(old('posicion', $jugador?->posicion) === $posicion)>
                {{ $posicion }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="edad">Edad</label>
        <input
            type="number"
            id="edad"
            name="edad"
            class="form-control"
            value="{{ old('edad', $jugador?->edad) }}"
            min="15"
            max="60"
            placeholder="Ej: 24"
        >
    </div>

    <div class="form-group">
        <label for="altura">Altura (metros)</label>
        <input
            type="number"
            id="altura"
            name="altura"
            class="form-control"
            value="{{ old('altura', $jugador?->altura) }}"
            min="1"
            max="2.5"
            step="0.01"
            placeholder="Ej: 1.95"
        >
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">
        {{ $jugador ? 'Actualizar Jugador' : 'Guardar Jugador' }}
    </button>
    <a href="{{ route('jugadores.index') }}" class="btn btn-secondary">Cancelar</a>
</div>
