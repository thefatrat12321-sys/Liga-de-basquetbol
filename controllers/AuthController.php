<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function showLogin() {
        if (isset($_SESSION['user'])) {
            header('Location: index.php?route=dashboard');
            exit;
        }
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);

        require __DIR__ . '/../views/auth/login.php';
    }

    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Por favor complete todos los campos.';
            header('Location: index.php?route=login');
            exit;
        }

        $user = $this->userModel->authenticate($email, $password);
        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nombre' => $user['nombre'],
                'email' => $user['email'],
                'rol' => $user['rol']
            ];
            header('Location: index.php?route=dashboard');
            exit;
        } else {
            $_SESSION['error'] = 'Credenciales inválidas. Verifique correo o contraseña.';
            header('Location: index.php?route=login');
            exit;
        }
    }

    public function showRegister() {
        if (isset($_SESSION['user'])) {
            header('Location: index.php?route=dashboard');
            exit;
        }
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        require __DIR__ . '/../views/auth/register.php';
    }

    public function register() {
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if (empty($nombre) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: index.php?route=register');
            exit;
        }

        if ($password !== $confirm) {
            $_SESSION['error'] = 'Las contraseñas no coinciden.';
            header('Location: index.php?route=register');
            exit;
        }

        if ($this->userModel->findByEmail($email)) {
            $_SESSION['error'] = 'El correo electrónico ya está registrado.';
            header('Location: index.php?route=register');
            exit;
        }

        if ($this->userModel->register($nombre, $email, $password)) {
            $_SESSION['success'] = '¡Registro exitoso! Ya puedes iniciar sesión con tu cuenta.';
            header('Location: index.php?route=login');
            exit;
        } else {
            $_SESSION['error'] = 'Ocurrió un error al registrar el usuario.';
            header('Location: index.php?route=register');
            exit;
        }
    }

    public function logout() {
        unset($_SESSION['user']);
        session_destroy();
        header('Location: index.php?route=login');
        exit;
    }
}
