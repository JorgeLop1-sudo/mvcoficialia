<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS-MPV - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/mvc_oficialiapartes/css/dashboard/styledashprueba.css">
    <link rel="stylesheet" href="/mvc_oficialiapartes/css/dashboard/stylehome.css">
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
                <a class="nav-link active" href="index.php?action=homedash">
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </a>
            </li>
            
            <?php if ($_SESSION['tipo_usuario'] === 'Administrador'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?action=areasadmin">
                        <i class="fas fa-layer-group"></i>
                        <span>Áreas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?action=usersadmin">
                        <i class="fas fa-users"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link" href="index.php?action=expedientes">
                    <i class="fas fa-folder"></i>
                    <span>Expedientes</span>
                </a>
            </li>

            <li class="nav-item mt-4">
                <a class="nav-link" href="index.php?action=config">
                    <i class="fas fa-cog"></i>
                    <span>Configuración</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="index.php?action=logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </div>

    
    <div class="main-content">
        
        <div class="header">
            <h2 class="mb-0">Dashboard</h2>
            <div class="user-info">
                <div class="user-avatar"><?php echo substr($_SESSION['nombre'], 0, 2); ?></div>
                <div>
                    <div class="fw-bold"><?php echo $_SESSION['nombre']; ?></div>
                    <div class="small text-muted"><?php echo $_SESSION['tipo_usuario']; ?></div>
                </div>
            </div>
        </div>

       
        <h3 class="page-title">Resumen de Oficios</h3>
        <div class="stats-container">

            <?php if ($_SESSION['tipo_usuario'] === 'Administrador'): ?>
            <div class="stat-card pending" onclick="filterOficios('pendiente')">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?php echo $estadisticas['pendientes']; ?></div>
                <div class="stat-title">Pendientes</div>
            </div>
            <?php endif; ?>
            
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
    <!-- Es correcto hacer esto-->
    <script>
        function filterOficios(estado) {
            const tipoUsuario = "<?php echo $_SESSION['tipo_usuario']; ?>";

            if (tipoUsuario === 'Administrador' || tipoUsuario === 'Usuario') {
                window.location.href = 'index.php?action=expedientes&estado=' + estado;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard administrativo cargado');
        });
    </script>
</body>
</html>