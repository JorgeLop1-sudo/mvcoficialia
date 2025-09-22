<?php
require_once __DIR__ . '/../models/Expediente.php';
require_once __DIR__ . '/../models/Area.php';
require_once __DIR__ . '/../models/User.php';

class ExpedientesUserController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'user') {
            header("Location: index.php?action=login");
            exit();
        }

        $database = new Database();
        $conn = $database->connect();
        $expedienteModel = new Expediente($conn);
        $areaModel = new Area($conn);
        $userModel = new User($conn);

        $mensaje = "";
        $error = "";
        $filtros = [];

        // Manejar solicitud AJAX para usuarios por área
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'usuarios_por_area' && isset($_GET['area_id'])) {
            $area_id = intval($_GET['area_id']);
            $usuarios_filtrados = $expedienteModel->obtenerUsuariosPorArea($area_id);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'usuarios' => $usuarios_filtrados]);
            exit();
        }

        // Obtener áreas y usuarios para los modales
        $areas = $areaModel->obtenerTodasActivas();
        $usuarios = $userModel->obtenerTodos();

        // Procesar filtros de búsqueda
        if (isset($_GET['numero'])) {
            $filtros['numero'] = $_GET['numero'];
        }
        if (isset($_GET['estado'])) {
            $filtros['estado'] = $_GET['estado'];
        }

        // Obtener expedientes con filtros
        $expedientes = $expedienteModel->obtenerTodos($filtros);

        // Procesar eliminación de oficio
        if (isset($_GET['eliminar'])) {
            $mensaje = $expedienteModel->eliminar($_GET['eliminar']);
            header("Location: index.php?action=expedientesuser&mensaje=" . urlencode($mensaje));
            exit();
        }

        // Procesar derivación de oficio
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['derivar_oficio'])) {
            $mensaje = $expedienteModel->derivar(
                $_POST['oficio_id'],
                $_POST['area_derivada'],
                $_POST['usuario_derivado'],
                $_POST['respuesta']
            );
            
            if (strpos($mensaje, 'Error') === false) {
                header("Location: index.php?action=expedientesuser&mensaje=" . urlencode($mensaje));
                exit();
            } else {
                $error = $mensaje;
            }
        }

        mysqli_close($conn);

        // Pasar variables a la vista
        $view_data = compact('expedientes', 'areas', 'usuarios', 'mensaje', 'error', 'filtros');
        extract($view_data);

        include __DIR__ . '/../views/expedientesuser.php';
    }
}
?>