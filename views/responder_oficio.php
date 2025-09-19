<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
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

// Obtener el ID del oficio a responder
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: " . ($_SESSION['tipo_usuario'] === 'admin' ? 'expedientes.php' : 'expedientesuser.php'));
    exit();
}

$oficio_id = mysqli_real_escape_string($conn, $_GET['id']);

// Obtener información del oficio
$query = "
    SELECT o.*, a.nombre as area_nombre, l.nombre as usuario_nombre,
           ad.nombre as area_derivada_nombre, ud.nombre as usuario_derivado_nombre
    FROM oficios o 
    LEFT JOIN areas a ON o.area_id = a.id 
    LEFT JOIN login l ON o.usuario_id = l.id
    LEFT JOIN areas ad ON o.area_derivada_id = ad.id
    LEFT JOIN login ud ON o.usuario_derivado_id = ud.id
    WHERE o.id = '$oficio_id'
";

$result = mysqli_query($conn, $query);
$oficio = mysqli_fetch_assoc($result);

if (!$oficio) {
    header("Location: " . ($_SESSION['tipo_usuario'] === 'admin' ? 'expedientes.php' : 'expedientesuser.php'));
    exit();
}

// Verificar permisos (solo el usuario asignado o admin puede responder)
$puede_responder = false;
if ($_SESSION['tipo_usuario'] === 'admin') {
    $puede_responder = true;
} else {
    // Para usuarios regulares, verificar si es el usuario asignado
    if (isset($_SESSION['id']) && $oficio['usuario_derivado_id'] == $_SESSION['id']) {
        $puede_responder = true;
    } elseif (isset($_SESSION['user_id']) && $oficio['usuario_derivado_id'] == $_SESSION['user_id']) {
        $puede_responder = true;
    } elseif (isset($_SESSION['usuario_id']) && $oficio['usuario_derivado_id'] == $_SESSION['usuario_id']) {
        $puede_responder = true;
    }
}

if (!$puede_responder) {
    header("Location: " . ($_SESSION['tipo_usuario'] === 'admin' ? 'expedientes.php' : 'expedientesuser.php'));
    exit();
}

// Procesar respuesta del oficio
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['completar'])) {
        // Completar el oficio
        $respuesta = mysqli_real_escape_string($conn, $_POST['respuesta']);
        
        $update_query = "UPDATE oficios SET 
                        respuesta = '$respuesta',
                        estado = 'completado',
                        fecha_respuesta = NOW()
                        WHERE id = '$oficio_id'";
        
        if (mysqli_query($conn, $update_query)) {
            header("Location: " . ($_SESSION['tipo_usuario'] === 'admin' ? 'expedientes.php' : 'expedientesuser.php') . "?mensaje=Oficio marcado como completado");
            exit();
        } else {
            $mensaje_error = "Error al completar el oficio: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['denegar'])) {
        // Denegar el oficio
        $respuesta = mysqli_real_escape_string($conn, $_POST['respuesta']);
        
        $update_query = "UPDATE oficios SET 
                        respuesta = '$respuesta',
                        estado = 'denegado',
                        fecha_respuesta = NOW()
                        WHERE id = '$oficio_id'";
        
        if (mysqli_query($conn, $update_query)) {
            header("Location: " . ($_SESSION['tipo_usuario'] === 'admin' ? 'expedientes.php' : 'expedientesuser.php') . "?mensaje=Oficio denegado");
            exit();
        } else {
            $mensaje_error = "Error al denegar el oficio: " . mysqli_error($conn);
        }
    }
}

// Cerrar conexión
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS-OP - Responder Oficio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="..\..\css\dashboard\styledash.css">
    <style>
        .document-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .detail-row {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
        }
        .detail-value {
            color: #212529;
        }
        .file-preview {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
        }
        .action-buttons {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .badge-estado {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .badge-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }
        .badge-proceso {
            background-color: #cce5ff;
            color: #004085;
        }
        .badge-completado {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-denegado {
            background-color: #f8d7da;
            color: #721c24;
        }
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
                <a class="nav-link" href="<?php echo $_SESSION['tipo_usuario'] === 'admin' ? 'homeadmin.php' : 'homeuser.php'; ?>">
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </a>
            </li>
            <?php if ($_SESSION['tipo_usuario'] === 'admin'): ?>
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
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $_SESSION['tipo_usuario'] === 'admin' ? 'expedientes.php' : 'expedientesuser.php'; ?>">
                    <i class="fas fa-folder"></i>
                    <span>Expedientes</span>
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link" href="<?php echo $_SESSION['tipo_usuario'] === 'admin' ? 'config.php' : 'configuser.php'; ?>">
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

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h2 class="mb-0">Dashboard <?php echo $_SESSION['tipo_usuario'] === 'admin' ? 'Administrador' : 'Usuario'; ?></h2>
            <div class="user-info">
                <div class="user-avatar"><?php echo substr($_SESSION['nombre'], 0, 2); ?></div>
                <div>
                    <div class="fw-bold"><?php echo $_SESSION['nombre']; ?></div>
                    <div class="small text-muted"><?php echo $_SESSION['tipo_usuario']; ?></div>
                </div>
            </div>
        </div>

        <!-- Mostrar mensajes de error -->
        <?php if (isset($mensaje_error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $mensaje_error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Page Title -->
        <h3 class="page-title">Responder Oficio</h3>

        <!-- Document Details -->
        <div class="document-details">
            <h5 class="mb-4">Detalles del Oficio</h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-row">
                        <span class="detail-label">ID:</span>
                        <span class="detail-value"><?php echo $oficio['id']; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Fecha de Registro:</span>
                        <span class="detail-value"><?php echo date('d/m/Y H:i', strtotime($oficio['fecha_registro'])); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Remitente:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($oficio['remitente']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Asunto:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($oficio['asunto']); ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-row">
                        <span class="detail-label">Número de Documento:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($oficio['numero_documento'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Estado:</span>
                        <span class="detail-value">
                            <?php 
                            $badge_class = '';
                            switch ($oficio['estado']) {
                                case 'pendiente':
                                    $badge_class = 'badge-pendiente';
                                    $estado_texto = 'Pendiente';
                                    break;
                                case 'tramite':
                                    $badge_class = 'badge-proceso';
                                    $estado_texto = 'En tramite';
                                    break;
                                case 'completado':
                                    $badge_class = 'badge-completado';
                                    $estado_texto = 'Completado';
                                    break;
                                case 'denegado':
                                    $badge_class = 'badge-denegado';
                                    $estado_texto = 'Denegado';
                                    break;
                                default:
                                    $badge_class = 'badge-pendiente';
                                    $estado_texto = 'Pendiente';
                            }
                            ?>
                            <span class="badge-estado <?php echo $badge_class; ?>"><?php echo $estado_texto; ?></span>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Área Origen:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($oficio['area_nombre']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Usuario Origen:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($oficio['usuario_nombre']); ?></span>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($oficio['area_derivada_nombre'])): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="detail-row">
                            <span class="detail-label">Derivado a:</span>
                            <span class="detail-value">
                                <?php echo htmlspecialchars($oficio['area_derivada_nombre']); ?>
                                <?php if (!empty($oficio['usuario_derivado_nombre'])): ?>
                                    (Usuario: <?php echo htmlspecialchars($oficio['usuario_derivado_nombre']); ?>)
                                <?php endif; ?>
                                <?php if (!empty($oficio['fecha_derivacion'])): ?>
                                    - <?php echo date('d/m/Y H:i', strtotime($oficio['fecha_derivacion'])); ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($oficio['respuesta'])): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="detail-row">
                            <span class="detail-label">Respuesta Anterior:</span>
                            <span class="detail-value"><?php echo nl2br(htmlspecialchars($oficio['respuesta'])); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- File Preview -->
        <?php if (!empty($oficio['archivo_ruta'])): ?>
            <div class="file-preview">
                <h5 class="mb-3">Documento Adjunto</h5>
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-pdf me-2 text-danger" style="font-size: 2rem;"></i>
                    <div>
                        <div class="fw-bold">Documento adjunto</div>
                        <div class="text-muted small"><?php echo basename($oficio['archivo_ruta']); ?></div>
                    </div>
                    <div class="ms-auto">
                        <a href="<?php echo $oficio['archivo_ruta']; ?>" target="_blank" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye me-1"></i> Ver documento
                        </a>
                        <a href="<?php echo $oficio['archivo_ruta']; ?>" download class="btn btn-secondary btn-sm">
                            <i class="fas fa-download me-1"></i> Descargar
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Este oficio no tiene documentos adjuntos.
            </div>
        <?php endif; ?>

        <!-- Response Form -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Responder al Oficio</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="respuesta" class="form-label">Respuesta</label>
                        <textarea class="form-control" id="respuesta" name="respuesta" rows="6" placeholder="Escriba aquí su respuesta al oficio..." required><?php echo isset($_POST['respuesta']) ? htmlspecialchars($_POST['respuesta']) : ''; ?></textarea>
                    </div>
                    
                    <div class="action-buttons">
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo $_SESSION['tipo_usuario'] === 'admin' ? 'expedientes.php' : 'expedientesuser.php'; ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                            <div>
                                <button type="submit" name="denegar" class="btn btn-danger me-2">
                                    <i class="fas fa-times-circle me-1"></i> Denegar
                                </button>
                                <button type="submit" name="completar" class="btn btn-success">
                                    <i class="fas fa-check-circle me-1"></i> Completar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>