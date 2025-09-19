<?php
//session_start();

// Headers para prevenir caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../inicio/index.php");
    exit();
}

// Conexión a la base de datos
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "oficialiap";

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$conn) {
    die("No hay conexión: " . mysqli_connect_error());
}

// Obtener estadísticas de oficios
$estadisticas = [
    'pendientes' => 0,
    'en_tramite' => 0,
    'atendidos' => 0,
    'denegados' => 0
];

// Consulta para contar oficios por estado
$query_estadisticas = "SELECT estado, COUNT(*) as total FROM oficios WHERE activo = 1 GROUP BY estado";
$result_estadisticas = mysqli_query($conn, $query_estadisticas);

if ($result_estadisticas) {
    while ($row = mysqli_fetch_assoc($result_estadisticas)) {
        switch ($row['estado']) {
            case 'pendiente':
                $estadisticas['pendientes'] = $row['total'];
                break;
            case 'tramite':
                $estadisticas['en_tramite'] = $row['total'];
                break;
            case 'completado':
                $estadisticas['atendidos'] = $row['total'];
                break;
            case 'denegado':
                $estadisticas['denegados'] = $row['total'];
                break;
        }
    }
}

// Obtener actividad reciente (últimos 10 oficios registrados)
$actividad_reciente = [];
$query_actividad = "
    SELECT o.*, a.nombre as area_nombre, l.nombre as usuario_nombre 
    FROM oficios o 
    LEFT JOIN areas a ON o.area_id = a.id 
    LEFT JOIN login l ON o.usuario_id = l.id 
    WHERE o.activo = 1 
    ORDER BY o.fecha_registro DESC 
    LIMIT 10
";
$result_actividad = mysqli_query($conn, $query_actividad);

if ($result_actividad) {
    while ($row = mysqli_fetch_assoc($result_actividad)) {
        $actividad_reciente[] = $row;
    }
}

// Cerrar conexión
mysqli_close($conn);

// Función para formatear la fecha
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

// Función para obtener el icono según el tipo de actividad
function getActivityIcon($estado) {
    switch ($estado) {
        case 'pendiente':
            return 'fas fa-clock text-warning';
        case 'tramite':
            return 'fas fa-tasks text-primary';
        case 'completado':
            return 'fas fa-check-circle text-success';
        case 'denegado':
            return 'fas fa-times-circle text-danger';
        default:
            return 'fas fa-file-alt text-info';
    }
}

// Función para obtener el texto descriptivo de la actividad
function getActivityText($oficio) {
    switch ($oficio['estado']) {
        case 'pendiente':
            return "Nuevo oficio registrado: " . $oficio['asunto'];
        case 'tramite':
            return "Oficio en trámite: " . $oficio['asunto'];
        case 'completado':
            return "Oficio completado: " . $oficio['asunto'];
        case 'denegado':
            return "Oficio denegado: " . $oficio['asunto'];
        default:
            return "Oficio actualizado: " . $oficio['asunto'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS-MPV - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/mvc_login/css/dashboard/styledash.css">
    <style>
        
    </style>
</head>
<body>
   
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>SIS-OP</h3>
            <p>Sistema de Oficialia de Partes</p>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="homeadmin.php">
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="areas.php">
                    <i class="fas fa-layer-group"></i>
                    <span>Áreas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="users.php">
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="expedientes.php">
                    <i class="fas fa-folder"></i>
                    <span>Expedientes</span>
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link" href="config.php">
                    <i class="fas fa-cog"></i>
                    <span>Configuración</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../../inicio/logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </div>

    
    <div class="main-content">
        
        <div class="header">
            <h2 class="mb-0">Dashboard Administrador</h2>
            <div class="user-info">
                <div class="user-avatar"><?php echo substr($_SESSION['nombre'], 0, 2); ?></div>
                <div>
                    <div class="fw-bold"><?php echo $_SESSION['nombre']; ?></div>
                    <div class="small text-muted"><?php echo $_SESSION['tipo_usuario']; ?></div>
                </div>
            </div>
        </div>

       
        <h3 class="dashboard-title">Resumen de Oficios</h3>
        <div class="stats-container">
            <div class="stat-card pending" onclick="filterOficios('pendiente')">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?php echo $estadisticas['pendientes']; ?></div>
                <div class="stat-title">Pendientes</div>
            </div>
            
            <div class="stat-card in-process" onclick="filterOficios('tramite')">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-number"><?php echo $estadisticas['en_tramite']; ?></div>
                <div class="stat-title">En Trámite</div>
            </div>
            
            <div class="stat-card completed" onclick="filterOficios('completado')">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number"><?php echo $estadisticas['atendidos']; ?></div>
                <div class="stat-title">Atendidos</div>
            </div>
            
            <div class="stat-card denied" onclick="filterOficios('denegado')">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-number"><?php echo $estadisticas['denegados']; ?></div>
                <div class="stat-title">Denegados</div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="recent-activity">
            <h4 class="activity-title">Actividad Reciente</h4>
            
            <?php if (empty($actividad_reciente)): ?>
                <div class="no-activity">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No hay actividad reciente</p>
                </div>
            <?php else: ?>
                <?php foreach ($actividad_reciente as $oficio): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="<?php echo getActivityIcon($oficio['estado']); ?>"></i>
                        </div>
                        <div class="activity-content">
                            <h5>
                                <?php 
                                switch ($oficio['estado']) {
                                    case 'pendiente':
                                        echo "Nuevo Oficio Registrado";
                                        break;
                                    case 'tramite':
                                        echo "Oficio en Trámite";
                                        break;
                                    case 'completado':
                                        echo "Oficio Completado";
                                        break;
                                    case 'denegado':
                                        echo "Oficio Denegado";
                                        break;
                                    default:
                                        echo "Actualización de Oficio";
                                }
                                ?>
                            </h5>
                            <p><?php echo $oficio['asunto']; ?></p>
                            <div class="activity-time">
                                <?php echo formatFecha($oficio['fecha_registro']); ?> | 
                                Área: <?php echo $oficio['area_nombre']; ?> | 
                                Por: <?php echo $oficio['usuario_nombre']; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterOficios(estado) {
            // Redirigir a la página de expedientes con el filtro aplicado
            window.location.href = 'expedientes.php?estado=' + estado;
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard administrativo cargado');
            // Aquí puedes agregar más funcionalidades JavaScript si es necesario
        });
    </script>
</body>
</html>