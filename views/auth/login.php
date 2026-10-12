<?php 
$pageTitle = 'Iniciar Sesión';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>🏀 Iniciar Sesión</h2>
        <p class="subtitle">Ingresa a la plataforma de gestión de la liga</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="index.php?route=login-post" method="POST">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="ejemplo@liga.com" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Ingresar</button>
        </form>

        <div class="auth-footer">
            ¿No tienes una cuenta? <a href="index.php?route=register">Regístrate aquí</a>
            <div style="margin-top: 14px; font-size: 0.8rem; background: #f1f5f9; padding: 8px; border-radius: 4px;">
                <strong>Demo Admin:</strong> admin@liga.com / admin123
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
