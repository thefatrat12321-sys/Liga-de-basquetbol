<?php 
$pageTitle = 'Actualizar Marcador y Estado';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="page-header">
    <h1 class="page-title">⏱️ Marcador y Control de Partido</h1>
    <a href="index.php?route=matches" class="btn btn-secondary">&larr; Volver a Partidos</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card" style="max-width: 650px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
        <p style="color: #64748b; font-size: 0.9rem;">
            <?= htmlspecialchars($match['fecha']) ?> &bull; <?= substr($match['hora'], 0, 5) ?> hrs &bull; <?= htmlspecialchars($match['lugar']) ?>
        </p>
        <div style="display: flex; justify-content: space-around; align-items: center; margin-top: 14px;">
            <div style="font-size: 1.25rem; font-weight: 700; width: 40%;">
                <?= htmlspecialchars($match['local_nombre']) ?>
                <div style="font-size: 0.8rem; color: #64748b; font-weight: normal;">(Local)</div>
            </div>
            <span style="font-size: 1.3rem; font-weight: 800; color: #94a3b8;">VS</span>
            <div style="font-size: 1.25rem; font-weight: 700; width: 40%;">
                <?= htmlspecialchars($match['visitante_nombre']) ?>
                <div style="font-size: 0.8rem; color: #64748b; font-weight: normal;">(Visitante)</div>
            </div>
        </div>
    </div>

    <form action="index.php?route=matches/score&id=<?= $match['id'] ?>" method="POST">
        <div class="form-row" style="margin-bottom: 24px;">
            <div class="form-group" style="text-align: center;">
                <label for="puntos_local" style="font-size: 1rem;">Puntos <?= htmlspecialchars($match['local_nombre']) ?></label>
                <input type="number" id="puntos_local" name="puntos_local" class="form-control" 
                       value="<?= htmlspecialchars($match['puntos_local']) ?>" min="0" max="250" 
                       style="font-size: 2rem; text-align: center; font-weight: bold; height: 60px;" required>
            </div>

            <div class="form-group" style="text-align: center;">
                <label for="puntos_visitante" style="font-size: 1rem;">Puntos <?= htmlspecialchars($match['visitante_nombre']) ?></label>
                <input type="number" id="puntos_visitante" name="puntos_visitante" class="form-control" 
                       value="<?= htmlspecialchars($match['puntos_visitante']) ?>" min="0" max="250" 
                       style="font-size: 2rem; text-align: center; font-weight: bold; height: 60px;" required>
            </div>
        </div>

        <div class="form-group">
            <label for="estado">Estado del Partido *</label>
            <select name="estado" id="estado" class="form-control" required>
                <option value="programado" <?= ($match['estado'] === 'programado') ? 'selected' : '' ?>>Programado (Aún no empieza)</option>
                <option value="en_juego" <?= ($match['estado'] === 'en_juego') ? 'selected' : '' ?>>En Juego (En vivo)</option>
                <option value="finalizado" <?= ($match['estado'] === 'finalizado') ? 'selected' : '' ?>>Finalizado (Cuenta para la tabla de posiciones)</option>
                <option value="cancelado" <?= ($match['estado'] === 'cancelado') ? 'selected' : '' ?>>Cancelado / Suspendido</option>
            </select>
        </div>

        <div style="margin-top: 24px; display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">Guardar Resultado</button>
            <a href="index.php?route=matches" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
