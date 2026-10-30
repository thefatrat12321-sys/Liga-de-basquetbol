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
