<?php 
$pageTitle = 'Editar Jugador';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h1 class="page-title">✏️ Editar Jugador</h1>
    <a href="index.php?route=players" class="btn btn-secondary">&larr; Volver a Jugadores</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card" style="max-width: 650px; margin: 0 auto;">
    <form action="index.php?route=players/edit&id=<?= $player['id'] ?>" method="POST">
        <div class="form-group">
            <label for="equipo_id">Equipo Perteneciente *</label>
            <select name="equipo_id" id="equipo_id" class="form-control" required>
                <?php foreach ($teams as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= ($player['equipo_id'] == $t['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['nombre']) ?> (<?= htmlspecialchars($t['ciudad']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre Completo del Jugador *</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($player['nombre']) ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="numero">Dorsal / Camiseta N° *</label>
                <input type="number" id="numero" name="numero" class="form-control" min="0" max="99" value="<?= htmlspecialchars($player['numero']) ?>" required>
            </div>

            <div class="form-group">
                <label for="posicion">Posición en Cancha *</label>
                <select name="posicion" id="posicion" class="form-control" required>
                    <?php 
                    $posiciones = ['Base', 'Escolta', 'Alero', 'Ala-Pívot', 'Pívot'];
                    foreach ($posiciones as $pos): 
                    ?>
                        <option value="<?= $pos ?>" <?= ($player['posicion'] === $pos) ? 'selected' : '' ?>><?= $pos ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="edad">Edad (años)</label>
                <input type="number" id="edad" name="edad" class="form-control" min="14" max="55" value="<?= htmlspecialchars($player['edad'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="altura">Estatura (metros)</label>
                <input type="number" id="altura" name="altura" class="form-control" step="0.01" min="1.40" max="2.40" value="<?= htmlspecialchars($player['altura'] ?? '') ?>">
            </div>
        </div>

        <div style="margin-top: 24px; display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Actualizar Jugador</button>
            <a href="index.php?route=players" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
