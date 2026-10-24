<?php 
$pageTitle = 'Partidos';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h1 class="page-title">📅 Partidos de la Liga</h1>
    <div>
        <a href="index.php?route=matches/create" class="btn btn-primary">+ Programar Partido</a>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Filtros de Estado -->
<div class="card" style="padding: 14px 20px;">
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <span style="font-weight: 600; font-size: 0.9rem;">Filtrar por:</span>
        <a href="index.php?route=matches" class="btn btn-sm <?= empty($filtro) ? 'btn-primary' : 'btn-secondary' ?>">Todos</a>
        <a href="index.php?route=matches&estado=programado" class="btn btn-sm <?= ($filtro === 'programado') ? 'btn-primary' : 'btn-secondary' ?>">Programados</a>
        <a href="index.php?route=matches&estado=en_juego" class="btn btn-sm <?= ($filtro === 'en_juego') ? 'btn-primary' : 'btn-secondary' ?>">En Juego</a>
        <a href="index.php?route=matches&estado=finalizado" class="btn btn-sm <?= ($filtro === 'finalizado') ? 'btn-primary' : 'btn-secondary' ?>">Finalizados</a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Fecha / Hora</th>
                    <th style="text-align: right;">Equipo Local</th>
                    <th style="text-align: center;">Marcador</th>
                    <th>Equipo Visitante</th>
                    <th>Lugar / Sede</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($matches)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 24px; color: #64748b;">
                            No hay partidos registrados con este estado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($matches as $m): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($m['fecha']) ?></strong><br>
                                <span style="font-size: 0.85rem; color: #64748b;"><?= substr($m['hora'], 0, 5) ?> hrs</span>
                            </td>
                            <td style="text-align: right; font-size: 1.05rem;">
                                <strong><?= htmlspecialchars($m['local_nombre']) ?></strong>
                            </td>
                            <td style="text-align: center; font-size: 1.2rem; font-weight: 700; white-space: nowrap; color: var(--primary);">
                                <?php if ($m['estado'] === 'programado'): ?>
                                    <span style="color: #94a3b8; font-size: 0.9rem; font-weight: normal;">VS</span>
                                <?php else: ?>
                                    <?= $m['puntos_local'] ?> - <?= $m['puntos_visitante'] ?>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 1.05rem;">
                                <strong><?= htmlspecialchars($m['visitante_nombre']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($m['lugar']) ?></td>
                            <td>
                                <span class="badge badge-<?= $m['estado'] ?>">
                                    <?= str_replace('_', ' ', $m['estado']) ?>
                                </span>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="index.php?route=matches/score&id=<?= $m['id'] ?>" class="btn btn-sm btn-primary" title="Cargar Marcador">
                                    ⏱️ Marcador
                                </a>
                                <a href="index.php?route=matches/delete&id=<?= $m['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('¿Seguro que deseas eliminar este partido?');">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
