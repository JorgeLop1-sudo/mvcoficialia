<?php
require_once __DIR__ . '/../models/Usuario.php';

class LoginController {
    private $usuarioModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->usuarioModel = new Usuario();
    }

    public function login() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identificador = $_POST['identificador'] ?? '';
            $password = $_POST['password'] ?? '';
            $login_type = $_POST['login_type'] ?? 'users';

            $res = $this->usuarioModel->login($identificador, $password, $login_type);

            if ($res['success']) {
                $user = $res['user'];
                $_SESSION['id'] = $user['id'];
                $_SESSION['usuario'] = $user['usuario'];
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['tipo_usuario'] = $user['tipo_usuario'];

                header("Location: index.php?action=homeadmin");
                exit;
            } else {
                $error = ($res['reason'] === 'not_found') ? (($login_type === 'email') ? "Correo no encontrado" : "Usuario no encontrado") : "Contraseña incorrecta";
            }
        }
        include __DIR__ . '/../views/login.php';
    }

    public function dashboard() {
        if (!isset($_SESSION['id'])) {
            header("Location: index.php?action=login");
            exit;
        }
        $usuarioData = $this->usuarioModel->getByUsuario($_SESSION['usuario']);
        include __DIR__ . '/../views/homeadmin.php';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }
}
