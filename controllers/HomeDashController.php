<?php
require_once __DIR__ . '/../models/Oficio.php';
require_once __DIR__ . '/../models/OficioUser.php';
require_once __DIR__ . '/../config/database.php';


class HomeDashController {
    public function dash() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Solo admin puede entrar
        /*if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] === 'admin' || $_SESSION['tipo_usuario'] === 'user') {
            header("Location: index.php?action=login");
            exit();
        }*/

        // Conexión BD
        $database = new Database();
        $conn = $database->connect();


        if($_SESSION['tipo_usuario'] === 'admin'){
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
        }




        if($_SESSION['tipo_usuario'] === 'user'){
            // Obtener ID del usuario actual
            $usuario_id = $this->getUsuarioId($conn, $_SESSION['usuario']);
            
            if (!$usuario_id) {
                header("Location: index.php?action=login");
                exit();
            }

            // Guardar el ID en la sesión para futuras consultas
            $_SESSION['id'] = $usuario_id;

            $oficioUserModel = new OficioUser($conn);
            $estadisticas = $oficioUserModel->getEstadisticas($usuario_id);
            $actividad_reciente = $oficioUserModel->getActividadReciente($usuario_id);

            mysqli_close($conn);

            // Funciones auxiliares
            function formatFecha($fecha) {
                if (empty($fecha)) return 'Sin fecha';
                
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

        include __DIR__ . '/../views/homedash.php';
    }

    private function getUsuarioId($conn, $usuario) {
        $sql = "SELECT id FROM login WHERE usuario = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $usuario);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['id'];
        }
        return false;
    }

}
