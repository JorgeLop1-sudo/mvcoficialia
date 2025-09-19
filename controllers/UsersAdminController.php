<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Area.php';
require_once __DIR__ . '/../config/database.php';

class UsersAdminController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
            header("Location: index.php?action=login");
            exit();
        }

        $database = new Database();
        $conn = $database->connect();
        $userModel = new User($conn);
        $areaModel = new Area($conn);

        $mensaje = "";
        $error = "";
        $form_data = [];

        // Procesar formularios
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['crear_usuario'])) {
                if ($_POST['password'] !== $_POST['confirm_password']) {
                    $error = "Error: Las contraseñas no coinciden";
                    $form_data = $_POST;
                } else {
                    $mensaje = $userModel->crear(
                        $_POST['usuario'],
                        $_POST['password'],
                        $_POST['nombre'],
                        $_POST['tipo_usuario'],
                        $_POST['area'],
                        $_POST['email']
                    );
                    header("Location: index.php?action=usersadmin&mensaje=" . urlencode($mensaje));
                    exit();
                }
            }

            if (isset($_POST['editar_usuario'])) {
                $password = !empty($_POST['password']) ? $_POST['password'] : null;
                if ($password && $_POST['password'] !== $_POST['confirm_password']) {
                    $error = "Error: Las contraseñas no coinciden";
                } else {
                    $mensaje = $userModel->actualizar(
                        $_POST['id'],
                        $_POST['usuario'],
                        $_POST['nombre'],
                        $_POST['tipo_usuario'],
                        $_POST['area'],
                        $_POST['email'],
                        $password
                    );
                    header("Location: index.php?action=usersadmin&mensaje=" . urlencode($mensaje));
                    exit();
                }
            }
        }

        // Procesar eliminación
        if (isset($_GET['eliminar'])) {
            if ($_GET['eliminar'] != $_SESSION['id_usuario']) {
                $mensaje = $userModel->eliminar($_GET['eliminar']);
                header("Location: index.php?action=usersadmin&mensaje=" . urlencode($mensaje));
                exit();
            } else {
                $error = "No puedes eliminarte a ti mismo";
            }
        }

        // Obtener usuario para editar
        $usuario_editar = null;
        if (isset($_GET['editar'])) {
            $usuario_editar = $userModel->obtenerPorId($_GET['editar']);
        }

        // Obtener datos
        $usuarios = $userModel->obtenerTodos();
        $areas_disponibles = $areaModel->obtenerTodasActivas();

        // DEBUG: Verificar si se están obteniendo las áreas
        error_log("Áreas obtenidas: " . print_r($areas_disponibles, true));

        

        // Pasar variables a la vista
        $view_data = compact('usuarios', 'areas_disponibles', 'mensaje', 'error', 'form_data', 'usuario_editar');
        
        // DEBUG: Verificar qué variables se pasan a la vista
        error_log("Variables pasadas a la vista: " . print_r(array_keys($view_data), true));
        
        extract($view_data);

        // DEBUG: Verificar si $areas_disponibles existe después del extract
        error_log("Variable areas_disponibles existe: " . (isset($areas_disponibles) ? 'Sí' : 'No'));
        if (isset($areas_disponibles)) {
            error_log("Número de áreas: " . count($areas_disponibles));
        }

        mysqli_close($conn);

        include __DIR__ . '/../views/usersadmin.php';
    }
}
?>