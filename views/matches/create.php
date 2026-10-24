<?php 
$pageTitle = 'Programar Partido';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h1 class="page-title">📅 Programar Nuevo Partido</h1>
    <a href="index.php?route=matches" class="btn btn-secondary">&larr; Volver a Partidos</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card" style="max-width: 650px; margin: 0 auto;">
    <form action="index.php?route=matches/create" method="POST">
        <div class="form-row">
            <div class="form-group">
                <label for="equipo_local_id">Equipo Local *</label>
                <select name="equipo_local_id" id="equipo_local_id" class="form-control" required>
                    <option value="">-- Seleccionar Local --</option>
                    <?php foreach ($teams as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="equipo_visitante_id">Equipo Visitante *</label>
                <select name="equipo_visitante_id" id="equipo_visitante_id" class="form-control" required>
                    <option value="">-- Seleccionar Visitante --</option>
                    <?php foreach ($teams as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="fecha">Fecha del Encuentro *</label>
                <input type="date" id="fecha" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label for="hora">Hora *</label>
                <input type="time" id="hora" name="hora" class="form-control" value="18:00" required>
            </div>
        </div>

        <div class="form-group">
            <label for="lugar">Lugar / Cancha / Coliseo *</label>
            <input type="text" id="lugar" name="lugar" class="form-control" placeholder="Ej: Gimnasio Municipal Central" required>
        </div>

        <div style="margin-top: 24px; display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Programar Partido</button>
            <a href="index.php?route=matches" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
