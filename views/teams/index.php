<?php 
$pageTitle = 'Gestión de Equipos';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h1 class="page-title">🛡️ Equipos de la Liga</h1>
    <div>
        <a href="index.php?route=teams/create" class="btn btn-primary">+ Nuevo Equipo</a>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Equipo</th>
                    <th>Ciudad</th>
                    <th>Entrenador</th>
                    <th style="text-align: center;">N° Jugadores</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($teams)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 24px; color: #64748b;">
                            No hay equipos registrados todavía. ¡Crea el primero!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($teams as $t): ?>
                        <tr>
                            <td>#<?= $t['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($t['nombre']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($t['ciudad']) ?></td>
                            <td><?= htmlspecialchars($t['entrenador'] ?: 'No asignado') ?></td>
                            <td style="text-align: center;">
                                <a href="index.php?route=players&equipo_id=<?= $t['id'] ?>" class="btn btn-secondary btn-sm">
                                    👥 <?= $t['total_jugadores'] ?> Jugadores
                                </a>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="index.php?route=players/create&equipo_id=<?= $t['id'] ?>" class="btn btn-sm btn-secondary" title="Agregar Jugador">+ Jugador</a>
                                <a href="index.php?route=teams/edit&id=<?= $t['id'] ?>" class="btn btn-sm btn-secondary">Editar</a>
                                <a href="index.php?route=teams/delete&id=<?= $t['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('¿Seguro que deseas eliminar este equipo? Se eliminarán también sus jugadores y partidos vinculados.');">
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
