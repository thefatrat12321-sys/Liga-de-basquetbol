<?php
require_once __DIR__ . '/../models/Team.php';

class TeamController {
    private $teamModel;

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?route=login');
            exit;
        }
        $this->teamModel = new Team();
    }

    public function index() {
        $teams = $this->teamModel->getAll();
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        require __DIR__ . '/../views/teams/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $ciudad = trim($_POST['ciudad'] ?? '');
            $entrenador = trim($_POST['entrenador'] ?? '');

            if (empty($nombre) || empty($ciudad)) {
                $_SESSION['error'] = 'El nombre y la ciudad son obligatorios.';
                header('Location: index.php?route=teams/create');
                exit;
            }

            try {
                $this->teamModel->create($nombre, $ciudad, $entrenador);
                $_SESSION['success'] = 'Equipo registrado correctamente.';
                header('Location: index.php?route=teams');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al registrar el equipo: posiblemente el nombre ya exista.';
                header('Location: index.php?route=teams/create');
                exit;
            }
        }

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        require __DIR__ . '/../views/teams/create.php';
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?route=teams');
            exit;
        }

        $team = $this->teamModel->getById($id);
        if (!$team) {
            header('Location: index.php?route=teams');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $ciudad = trim($_POST['ciudad'] ?? '');
            $entrenador = trim($_POST['entrenador'] ?? '');

            if (empty($nombre) || empty($ciudad)) {
                $_SESSION['error'] = 'El nombre y la ciudad son obligatorios.';
                header("Location: index.php?route=teams/edit&id=$id");
                exit;
            }

            try {
                $this->teamModel->update($id, $nombre, $ciudad, $entrenador);
                $_SESSION['success'] = 'Equipo actualizado con éxito.';
                header('Location: index.php?route=teams');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al actualizar el equipo: ' . $e->getMessage();
                header("Location: index.php?route=teams/edit&id=$id");
                exit;
            }
        }

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        require __DIR__ . '/../views/teams/edit.php';
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->teamModel->delete($id);
            $_SESSION['success'] = 'Equipo y sus jugadores asociados eliminados correctamente.';
        }
        header('Location: index.php?route=teams');
        exit;
    }
}
