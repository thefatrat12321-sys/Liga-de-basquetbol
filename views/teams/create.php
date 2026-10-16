<?php 
$pageTitle = 'Nuevo Equipo';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h1 class="page-title">🛡️ Registrar Nuevo Equipo</h1>
    <a href="index.php?route=teams" class="btn btn-secondary">&larr; Volver a Equipos</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form action="index.php?route=teams/create" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre del Equipo *</label>
            <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Halcones Dorados" required autofocus>
        </div>

        <div class="form-group">
            <label for="ciudad">Ciudad / Localidad *</label>
            <input type="text" id="ciudad" name="ciudad" class="form-control" placeholder="Ej: Lima, Madrid, etc." required>
        </div>

        <div class="form-group">
            <label for="entrenador">Nombre del Entrenador (Coach)</label>
            <input type="text" id="entrenador" name="entrenador" class="form-control" placeholder="Ej: Carlos Silva">
        </div>

        <div style="margin-top: 24px; display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Guardar Equipo</button>
            <a href="index.php?route=teams" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
