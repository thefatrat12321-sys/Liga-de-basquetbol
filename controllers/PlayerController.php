<?php
require_once __DIR__ . '/../models/Player.php';
require_once __DIR__ . '/../models/Team.php';

class PlayerController {
    private $playerModel;
    private $teamModel;

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?route=login');
            exit;
        }
        $this->playerModel = new Player();
        $this->teamModel = new Team();
    }

    public function index() {
        $filtro_equipo = $_GET['equipo_id'] ?? null;
        $players = $this->playerModel->getAll($filtro_equipo);
        $teams = $this->teamModel->getAll();

        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        require __DIR__ . '/../views/players/index.php';
    }

    public function create() {
        $teams = $this->teamModel->getAll();
        if (empty($teams)) {
            $_SESSION['error'] = 'Debe crear al menos un equipo antes de registrar jugadores.';
            header('Location: index.php?route=teams/create');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $equipo_id = $_POST['equipo_id'] ?? '';
            $nombre = trim($_POST['nombre'] ?? '');
            $numero = $_POST['numero'] ?? '';
            $posicion = trim($_POST['posicion'] ?? '');
            $edad = $_POST['edad'] ?? null;
            $altura = $_POST['altura'] ?? null;

            if (empty($equipo_id) || empty($nombre) || $numero === '' || empty($posicion)) {
                $_SESSION['error'] = 'Equipo, nombre, número y posición son obligatorios.';
                header('Location: index.php?route=players/create');
                exit;
            }

            try {
                $this->playerModel->create($equipo_id, $nombre, $numero, $posicion, $edad, $altura);
                $_SESSION['success'] = 'Jugador registrado exitosamente.';
                header('Location: index.php?route=players');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al registrar jugador: ' . $e->getMessage();
                header('Location: index.php?route=players/create');
                exit;
            }
        }

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        $default_team = $_GET['equipo_id'] ?? null;
        require __DIR__ . '/../views/players/create.php';
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?route=players');
            exit;
        }

        $player = $this->playerModel->getById($id);
        if (!$player) {
            header('Location: index.php?route=players');
            exit;
        }

        $teams = $this->teamModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $equipo_id = $_POST['equipo_id'] ?? '';
            $nombre = trim($_POST['nombre'] ?? '');
            $numero = $_POST['numero'] ?? '';
            $posicion = trim($_POST['posicion'] ?? '');
            $edad = $_POST['edad'] ?? null;
            $altura = $_POST['altura'] ?? null;

            if (empty($equipo_id) || empty($nombre) || $numero === '' || empty($posicion)) {
                $_SESSION['error'] = 'Equipo, nombre, número y posición son obligatorios.';
                header("Location: index.php?route=players/edit&id=$id");
                exit;
            }

            try {
                $this->playerModel->update($id, $equipo_id, $nombre, $numero, $posicion, $edad, $altura);
                $_SESSION['success'] = 'Jugador actualizado con éxito.';
                header('Location: index.php?route=players');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al actualizar jugador: ' . $e->getMessage();
                header("Location: index.php?route=players/edit&id=$id");
                exit;
            }
        }

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        require __DIR__ . '/../views/players/edit.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->playerModel->delete($id);
            $_SESSION['success'] = 'Jugador eliminado correctamente.';
        }
        header('Location: index.php?route=players');
        exit;
    }
}
