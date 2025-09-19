<?php
require_once __DIR__ . '/../models/Oficio.php';
require_once __DIR__ . '/../config/database.php';


class HomeAdminController {
    public function admin() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Solo admin puede entrar
        if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
            header("Location: index.php?action=login");
            exit();
        }

        // Conexión BD
        $database = new Database();
        $conn = $database->connect();

        $oficioModel = new Oficio($conn);
        $estadisticas = $oficioModel->getEstadisticas();
        $actividad_reciente = $oficioModel->getActividadReciente();

        mysqli_close($conn);

        // Funciones auxiliares
        function formatFecha($fecha) {
            $fecha_obj = new DateTime($fecha);
            $hoy = new DateTime();
            $ayer = new DateTime('yesterday');
            if ($fecha_obj->format('Y-m-d') === $hoy->format('Y-m-d')) {
                return 'Hoy, ' . $fecha_obj->format('H:i');
            } elseif ($fecha_obj->format('Y-m-d') === $ayer->format('Y-m-d')) {
                return 'Ayer, ' . $fecha_obj->format('H:i');
            } else {
                return $fecha_obj->format('d/m/Y H:i');
            }
        }

        function getActivityIcon($estado) {
            switch ($estado) {
                case 'pendiente': return 'fas fa-clock text-warning';
                case 'tramite': return 'fas fa-tasks text-primary';
                case 'completado': return 'fas fa-check-circle text-success';
                case 'denegado': return 'fas fa-times-circle text-danger';
                default: return 'fas fa-file-alt text-info';
            }
        }

        include __DIR__ . '/../views/homeadmin.php';
    }
}
