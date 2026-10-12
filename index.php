<?php
/**
 * Punto de entrada principal y Enrutador del Sistema de Gestión de Liga de Básquetbol.
 */
session_start();

// Autocarga de controladores según la ruta
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/TeamController.php';
require_once __DIR__ . '/controllers/PlayerController.php';
require_once __DIR__ . '/controllers/MatchController.php';

$route = $_GET['route'] ?? 'dashboard';

switch ($route) {
    // Autenticación
    case 'login':
        (new AuthController())->showLogin();
        break;
    case 'login-post':
        (new AuthController())->login();
        break;
    case 'register':
        (new AuthController())->showRegister();
        break;
    case 'register-post':
        (new AuthController())->register();
        break;
    case 'logout':
        (new AuthController())->logout();
        break;

    // Panel Principal y Estadísticas
    case 'dashboard':
        (new DashboardController())->index();
        break;

    // Equipos
    case 'teams':
        (new TeamController())->index();
        break;
    case 'teams/create':
        (new TeamController())->create();
        break;
    case 'teams/edit':
        (new TeamController())->edit();
        break;
    case 'teams/delete':
        (new TeamController())->delete();
        break;

    // Jugadores
    case 'players':
        (new PlayerController())->index();
        break;
    case 'players/create':
        (new PlayerController())->create();
        break;
    case 'players/edit':
        (new PlayerController())->edit();
        break;
    case 'players/delete':
        (new PlayerController())->delete();
        break;

    // Partidos
    case 'matches':
        (new MatchController())->index();
        break;
    case 'matches/create':
        (new MatchController())->create();
        break;
    case 'matches/score':
        (new MatchController())->editScore();
        break;
    case 'matches/delete':
        (new MatchController())->delete();
        break;

    default:
        // Si no existe la ruta, redirige a dashboard
        header('Location: index.php?route=dashboard');
        break;
}
