<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/database.php';

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

                // Redirigir según el tipo de usuario
                if ($user['tipo_usuario'] === 'admin') {
                    header("Location: index.php?action=homeadmin");
                } else {
                    header("Location: index.php?action=homeuser");
                }
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
        
        // Redirigir según el tipo de usuario
        if ($_SESSION['tipo_usuario'] === 'admin') {
            header("Location: index.php?action=homeadmin");
        } else {
            header("Location: index.php?action=homeuser");
        }
        exit;
    }

    public function logout() {
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Limpiar todas las variables de sesión
        $_SESSION = array();
        
        // Si se desea destruir la cookie de sesión completamente,
        // borra también la cookie de sesión.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destruir la sesión
        session_destroy();
        
        // Redirigir al login
        header("Location: index.php?action=login");
        exit();
    }
}