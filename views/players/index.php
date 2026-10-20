<?php 
$pageTitle = 'Gestión de Jugadores';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h1 class="page-title">🏃 Jugadores</h1>
    <div>
        <a href="index.php?route=players/create<?= !empty($filtro_equipo) ? '&equipo_id=' . $filtro_equipo : '' ?>" class="btn btn-primary">+ Nuevo Jugador</a>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Barra de Filtro por Equipo -->
<div class="card" style="padding: 16px;">
    <form action="index.php" method="GET" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <input type="hidden" name="route" value="players">
        <label for="filtro_equipo" style="font-weight: 600; font-size: 0.95rem;">Filtrar por Equipo:</label>
        <select name="equipo_id" id="filtro_equipo" class="form-control" style="width: auto; min-width: 220px;" onchange="this.form.submit()">
            <option value="">-- Todos los Equipos --</option>
            <?php foreach ($teams as $t): ?>
                <option value="<?= $t['id'] ?>" <?= ($filtro_equipo == $t['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($filtro_equipo)): ?>
            <a href="index.php?route=players" class="btn btn-secondary btn-sm">Limpiar Filtro</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">N°</th>
                    <th>Nombre del Jugador</th>
                    <th>Equipo</th>
                    <th>Posición</th>
                    <th style="text-align: center;">Edad</th>
                    <th style="text-align: center;">Estatura</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($players)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 24px; color: #64748b;">
                            No se encontraron jugadores registrados para este criterio.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($players as $p): ?>
                        <tr>
                            <td><strong style="color: var(--primary);">#<?= $p['numero'] ?></strong></td>
                            <td><strong><?= htmlspecialchars($p['nombre']) ?></strong></td>
                            <td><?= htmlspecialchars($p['equipo_nombre']) ?></td>
                            <td><span style="background: #f1f5f9; padding: 3px 8px; border-radius: 4px; font-size: 0.85rem;"><?= htmlspecialchars($p['posicion']) ?></span></td>
                            <td style="text-align: center;"><?= $p['edad'] ? $p['edad'] . ' años' : '-' ?></td>
                            <td style="text-align: center;"><?= $p['altura'] ? number_format($p['altura'], 2) . ' m' : '-' ?></td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="index.php?route=players/edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-secondary">Editar</a>
                                <a href="index.php?route=players/delete&id=<?= $p['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('¿Seguro que deseas eliminar este jugador?');">
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
