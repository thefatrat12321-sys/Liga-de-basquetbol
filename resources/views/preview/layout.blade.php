<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <title>@yield('title', 'Inicio') · Liga de Básquetbol</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">
        <a href="index.html">🏀 Liga de Básquet</a>
    </div>

    <ul class="nav-links">
        <li>
            <a href="index.html" class="{{ ($seccion ?? '') === 'dashboard' ? 'active' : '' }}">
                Estadísticas / Tabla
            </a>
        </li>
        <li>
            <a href="partidos.html" class="{{ ($seccion ?? '') === 'partidos' ? 'active' : '' }}">
                Partidos
            </a>
        </li>
    </ul>

    <div class="nav-user">
        <span class="user-badge user-badge-readonly">Solo lectura</span>
        <span class="user-badge">Vista previa</span>
    </div>
</nav>

<main class="container">
    <div class="preview-banner">
        <strong>Vista previa estática.</strong>
        Esta copia se genera desde la base de datos local y no permite iniciar sesión ni editar.
        Para modificar los datos hay que ejecutar la aplicación con XAMPP y MySQL y regenerar la vista previa.
    </div>

    @yield('content')
</main>

<footer class="app-footer">
    Sistema de Gestión de Liga de Básquetbol · Vista previa generada el {{ $generadoEn ?? '' }}
</footer>

</body>
</html>
