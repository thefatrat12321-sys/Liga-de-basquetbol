<?php 
$pageTitle = 'Panel General y Estadísticas';
require __DIR__ . '/layouts/header.php'; 
?>

<div class="page-header">
    <h1 class="page-title">📊 Estadísticas y Tabla General</h1>
    <div>
        <a href="index.php?route=matches/create" class="btn btn-primary">+ Programar Partido</a>
    </div>
</div>

<!-- Tarjetas de Resumen Estadístico -->
<div class="stats-grid">
    <div class="stat-card">
        <span class="label">Total Equipos</span>
        <span class="value"><?= $stats['total_equipos'] ?></span>
    </div>
    <div class="stat-card">
        <span class="label">Total Jugadores</span>
        <span class="value"><?= $stats['total_jugadores'] ?></span>
    </div>
    <div class="stat-card">
        <span class="label">Partidos Finalizados</span>
        <span class="value"><?= $stats['finalizados'] ?> / <?= $stats['total_partidos'] ?></span>
    </div>
    <div class="stat-card">
        <span class="label">Promedio Puntos / Partido</span>
        <span class="value"><?= $stats['promedio_puntos'] ?></span>
    </div>
</div>

<!-- Tabla de Posiciones de la Liga -->
<div class="card">
    <h2 style="margin-bottom: 16px; font-size: 1.3rem; color: #1e293b;">🏆 Tabla de Posiciones</h2>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">Pos</th>
                    <th>Equipo</th>
                    <th style="text-align: center;">PJ</th>
                    <th style="text-align: center;">PG</th>
                    <th style="text-align: center;">PP</th>
                    <th style="text-align: center;">PF</th>
                    <th style="text-align: center;">PC</th>
                    <th style="text-align: center;">DIF</th>
                    <th style="text-align: center; color: var(--primary);">PTS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($standings)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 20px; color: #64748b;">No hay equipos registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($standings as $index => $row): ?>
                        <tr>
                            <td><strong><?= $index + 1 ?></strong></td>
                            <td>
                                <strong><?= htmlspecialchars($row['nombre']) ?></strong>
                            </td>
                            <td style="text-align: center;"><?= $row['pj'] ?></td>
                            <td style="text-align: center; color: #16a34a; font-weight: 600;"><?= $row['pg'] ?></td>
                            <td style="text-align: center; color: #dc2626; font-weight: 600;"><?= $row['pp'] ?></td>
                            <td style="text-align: center;"><?= $row['pf'] ?></td>
                            <td style="text-align: center;"><?= $row['pc'] ?></td>
                            <td style="text-align: center;"><?= ($row['dif'] > 0 ? '+'.$row['dif'] : $row['dif']) ?></td>
                            <td style="text-align: center; font-weight: 700; color: var(--primary); font-size: 1.1rem;"><?= $row['puntos'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <p style="margin-top: 14px; font-size: 0.8rem; color: #64748b;">
        * Criterio FIBA: 2 puntos por victoria, 1 punto por derrota. PJ: Jugados | PG: Ganados | PP: Perdidos | PF: Puntos a favor | PC: Puntos en contra | DIF: Diferencia.
    </p>
</div>

<!-- Últimos Partidos Registrados -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h2 style="font-size: 1.3rem; color: #1e293b;">📅 Partidos Recientes</h2>
        <a href="index.php?route=matches" style="color: var(--primary); text-decoration: none; font-size: 0.9rem; font-weight: 600;">Ver todos &rarr;</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Fecha / Hora</th>
                    <th>Local</th>
                    <th style="text-align: center;">Marcador</th>
                    <th>Visitante</th>
                    <th>Lugar</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentMatches)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #64748b;">No hay partidos registrados recientemente.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentMatches as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['fecha']) ?> <?= substr($m['hora'], 0, 5) ?></td>
                            <td><strong><?= htmlspecialchars($m['local_nombre']) ?></strong></td>
                            <td style="text-align: center; font-weight: 700; font-size: 1.05rem;">
                                <?= $m['estado'] === 'finalizado' ? "{$m['puntos_local']} - {$m['puntos_visitante']}" : 'vs' ?>
                            </td>
                            <td><strong><?= htmlspecialchars($m['visitante_nombre']) ?></strong></td>
                            <td><?= htmlspecialchars($m['lugar']) ?></td>
                            <td><span class="badge badge-<?= $m['estado'] ?>"><?= str_replace('_', ' ', $m['estado']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/layouts/footer.php'; ?>
