<?php 
$pageTitle = 'Registro de Usuario';
require __DIR__ . '/../layouts/header.php'; 
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>🏀 Registro de Usuario</h2>
        <p class="subtitle">Crea tu cuenta para administrar la liga</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="index.php?route=register-post" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Juan Pérez" required>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmar Contraseña</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="Repite la contraseña" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Crear Cuenta</button>
        </form>

        <div class="auth-footer">
            ¿Ya tienes una cuenta? <a href="index.php?route=login">Inicia Sesión</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
