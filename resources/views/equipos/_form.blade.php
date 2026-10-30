@php($equipo = $equipo ?? null)

<div class="form-group">
    <label for="nombre">Nombre del equipo *</label>
    <input
        type="text"
        id="nombre"
        name="nombre"
        class="form-control"
        value="{{ old('nombre', $equipo?->nombre) }}"
        placeholder="Ej: Halcones Dorados"
        required
        autofocus
    >
</div>

<div class="form-group">
    <label for="ciudad">Ciudad / Localidad *</label>
    <input
        type="text"
        id="ciudad"
        name="ciudad"
        class="form-control"
        value="{{ old('ciudad', $equipo?->ciudad) }}"
        placeholder="Ej: Lima, Madrid, etc."
        required
    >
</div>

<div class="form-group">
    <label for="entrenador">Nombre del entrenador</label>
    <input
        type="text"
        id="entrenador"
        name="entrenador"
        class="form-control"
        value="{{ old('entrenador', $equipo?->entrenador) }}"
        placeholder="Ej: Carlos Silva"
    >
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">
        {{ $equipo ? 'Actualizar Equipo' : 'Guardar Equipo' }}
    </button>
    <a href="{{ route('equipos.index') }}" class="btn btn-secondary">Cancelar</a>
</div>
