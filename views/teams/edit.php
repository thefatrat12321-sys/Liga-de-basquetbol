<?php 
$pageTitle = 'Editar Equipo';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h1 class="page-title">✏️ Editar Equipo</h1>
    <a href="index.php?route=teams" class="btn btn-secondary">&larr; Volver a Equipos</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form action="index.php?route=teams/edit&id=<?= $team['id'] ?>" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre del Equipo *</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($team['nombre']) ?>" required autofocus>
        </div>

        <div class="form-group">
            <label for="ciudad">Ciudad / Localidad *</label>
            <input type="text" id="ciudad" name="ciudad" class="form-control" value="<?= htmlspecialchars($team['ciudad']) ?>" required>
        </div>

        <div class="form-group">
            <label for="entrenador">Nombre del Entrenador (Coach)</label>
            <input type="text" id="entrenador" name="entrenador" class="form-control" value="<?= htmlspecialchars($team['entrenador'] ?? '') ?>">
        </div>

        <div style="margin-top: 24px; display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Actualizar Datos</button>
            <a href="index.php?route=teams" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
