<?php
require_once __DIR__ . '/../models/BasketballMatch.php';
require_once __DIR__ . '/../models/Team.php';

class MatchController {
    private $matchModel;
    private $teamModel;

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?route=login');
            exit;
        }
        $this->matchModel = new BasketballMatch();
        $this->teamModel = new Team();
    }

    public function index() {
        $filtro = $_GET['estado'] ?? null;
        $matches = $this->matchModel->getAll($filtro);
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        require __DIR__ . '/../views/matches/index.php';
    }

    public function create() {
        $teams = $this->teamModel->getAll();
        if (count($teams) < 2) {
            $_SESSION['error'] = 'Se necesitan al menos 2 equipos para programar un partido.';
            header('Location: index.php?route=matches');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $equipo_local_id = $_POST['equipo_local_id'] ?? '';
            $equipo_visitante_id = $_POST['equipo_visitante_id'] ?? '';
            $fecha = $_POST['fecha'] ?? '';
            $hora = $_POST['hora'] ?? '';
            $lugar = trim($_POST['lugar'] ?? '');

            if (empty($equipo_local_id) || empty($equipo_visitante_id) || empty($fecha) || empty($hora) || empty($lugar)) {
                $_SESSION['error'] = 'Todos los campos son obligatorios.';
                header('Location: index.php?route=matches/create');
                exit;
            }

            if ($equipo_local_id == $equipo_visitante_id) {
                $_SESSION['error'] = 'El equipo local y el equipo visitante no pueden ser el mismo.';
                header('Location: index.php?route=matches/create');
                exit;
            }

            try {
                $this->matchModel->create($equipo_local_id, $equipo_visitante_id, $fecha, $hora, $lugar);
                $_SESSION['success'] = 'Partido programado con éxito.';
                header('Location: index.php?route=matches');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al programar el partido: ' . $e->getMessage();
                header('Location: index.php?route=matches/create');
                exit;
            }
        }

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        require __DIR__ . '/../views/matches/create.php';
    }

    public function editScore() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?route=matches');
            exit;
        }

        $match = $this->matchModel->getById($id);
        if (!$match) {
            header('Location: index.php?route=matches');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $puntos_local = (int)($_POST['puntos_local'] ?? 0);
            $puntos_visitante = (int)($_POST['puntos_visitante'] ?? 0);
            $estado = $_POST['estado'] ?? 'programado';

            try {
                $this->matchModel->updateScoreAndStatus($id, $puntos_local, $puntos_visitante, $estado);
                $_SESSION['success'] = 'Resultado y estado del partido actualizados correctamente.';
                header('Location: index.php?route=matches');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al actualizar resultado: ' . $e->getMessage();
                header("Location: index.php?route=matches/score&id=$id");
                exit;
            }
        }

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        require __DIR__ . '/../views/matches/score.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->matchModel->delete($id);
            $_SESSION['success'] = 'Partido eliminado exitosamente.';
        }
        header('Location: index.php?route=matches');
        exit;
    }
}
