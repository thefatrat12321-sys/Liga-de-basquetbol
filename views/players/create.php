<?php 
$pageTitle = 'Nuevo Jugador';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h1 class="page-title">🏃 Registrar Jugador</h1>
    <a href="index.php?route=players" class="btn btn-secondary">&larr; Volver a Jugadores</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card" style="max-width: 650px; margin: 0 auto;">
    <form action="index.php?route=players/create" method="POST">
        <div class="form-group">
            <label for="equipo_id">Equipo Perteneciente *</label>
            <select name="equipo_id" id="equipo_id" class="form-control" required>
                <option value="">-- Seleccionar Equipo --</option>
                <?php foreach ($teams as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= (isset($default_team) && $default_team == $t['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['nombre']) ?> (<?= htmlspecialchars($t['ciudad']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre Completo del Jugador *</label>
            <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Michael Jordan" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="numero">Dorsal / Camiseta N° *</label>
                <input type="number" id="numero" name="numero" class="form-control" min="0" max="99" placeholder="23" required>
            </div>

            <div class="form-group">
                <label for="posicion">Posición en Cancha *</label>
                <select name="posicion" id="posicion" class="form-control" required>
                    <option value="">-- Seleccionar Posición --</option>
                    <option value="Base">Base (Point Guard)</option>
                    <option value="Escolta">Escolta (Shooting Guard)</option>
                    <option value="Alero">Alero (Small Forward)</option>
                    <option value="Ala-Pívot">Ala-Pívot (Power Forward)</option>
                    <option value="Pívot">Pívot (Center)</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="edad">Edad (años)</label>
                <input type="number" id="edad" name="edad" class="form-control" min="14" max="55" placeholder="Ej: 24">
            </div>

            <div class="form-group">
                <label for="altura">Estatura (metros)</label>
                <input type="number" id="altura" name="altura" class="form-control" step="0.01" min="1.40" max="2.40" placeholder="Ej: 1.98">
            </div>
        </div>

        <div style="margin-top: 24px; display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Registrar Jugador</button>
            <a href="index.php?route=players" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
