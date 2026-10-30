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
