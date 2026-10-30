<div class="card filter-card">
    <div class="filter-form">
        <span class="filter-label">Filtrar por estado:</span>

        <a href="{{ $urls[null] }}"
           class="btn btn-sm {{ is_null($estado) ? 'btn-primary' : 'btn-secondary' }}">
            Todos
        </a>

        @foreach (\App\Models\Partido::ESTADOS as $clave => $etiqueta)
            <a href="{{ $urls[$clave] }}"
               class="btn btn-sm {{ $estado === $clave ? 'btn-primary' : 'btn-secondary' }}">
                {{ $etiqueta }}
            </a>
        @endforeach
    </div>
</div>
