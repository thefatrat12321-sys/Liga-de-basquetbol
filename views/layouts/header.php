<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>Liga de Básquetbol</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<?php if (isset($_SESSION['user'])): ?>
<nav class="navbar">
    <div class="nav-brand">
        <a href="index.php?route=dashboard">
            🏀 Liga de Básquet
        </a>
    </div>
    <ul class="nav-links">
        <li><a href="index.php?route=dashboard" class="<?= ($_GET['route'] ?? '') === 'dashboard' ? 'active' : '' ?>">Estadísticas / Tabla</a></li>
        <li><a href="index.php?route=teams" class="<?= strpos($_GET['route'] ?? '', 'teams') === 0 ? 'active' : '' ?>">Equipos</a></li>
        <li><a href="index.php?route=players" class="<?= strpos($_GET['route'] ?? '', 'players') === 0 ? 'active' : '' ?>">Jugadores</a></li>
        <li><a href="index.php?route=matches" class="<?= strpos($_GET['route'] ?? '', 'matches') === 0 ? 'active' : '' ?>">Partidos</a></li>
    </ul>
    <div class="nav-user">
        <span class="user-badge"><?= htmlspecialchars($_SESSION['user']['nombre']) ?> (<?= htmlspecialchars($_SESSION['user']['rol']) ?>)</span>
        <a href="index.php?route=logout" class="btn btn-secondary btn-sm" onclick="return confirm('¿Desea cerrar sesión?');">Cerrar Sesión</a>
    </div>
</nav>
<?php endif; ?>

<main class="container">
