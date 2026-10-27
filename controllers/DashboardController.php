<?php
require_once __DIR__ . '/../models/BasketballMatch.php';
require_once __DIR__ . '/../models/Team.php';
require_once __DIR__ . '/../models/Player.php';

class DashboardController {
    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?route=login');
            exit;
        }
    }

    public function index() {
        $matchModel = new BasketballMatch();
        $standings = $matchModel->getStandings();
        $stats = $matchModel->getGeneralStats();
        $recentMatches = array_slice($matchModel->getAll(), 0, 5);

        require __DIR__ . '/../views/dashboard.php';
    }
}
