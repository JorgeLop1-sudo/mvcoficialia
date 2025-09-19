<?php
session_start();

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

// Manejar la solicitud AJAX para obtener usuarios por área
if (isset($_GET['ajax']) && $_GET['ajax'] == 'usuarios_por_area' && isset($_GET['area_id'])) {
    $area_id = intval($_GET['area_id']);
    $usuarios_filtrados = [];
    
    if ($area_id > 0) {
        $query_usuarios_area = "SELECT id, nombre, usuario FROM login WHERE area_id = $area_id ORDER BY nombre";
        $result_usuarios_area = mysqli_query($conn, $query_usuarios_area);
        
        if ($result_usuarios_area) {
            while ($row = mysqli_fetch_assoc($result_usuarios_area)) {
                $usuarios_filtrados[] = $row;
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'usuarios' => $usuarios_filtrados]);
    exit();
}

// Obtener áreas y usuarios para los modales
$areas = [];
$usuarios = [];
$query_areas = "SELECT id, nombre FROM areas WHERE activo = 1 ORDER BY nombre";
$query_usuarios = "SELECT id, nombre, usuario, area_id FROM login ORDER BY nombre";

$result_areas = mysqli_query($conn, $query_areas);
$result_usuarios = mysqli_query($conn, $query_usuarios);

if ($result_areas) {
    while ($row = mysqli_fetch_assoc($result_areas)) {
        $areas[] = $row;
    }
}

if ($result_usuarios) {
    while ($row = mysqli_fetch_assoc($result_usuarios)) {
        $usuarios[] = $row;
    }
}

// Procesar búsqueda y filtros
$filtro_numero = isset($_GET['numero']) ? mysqli_real_escape_string($conn, $_GET['numero']) : '';
$filtro_estado = isset($_GET['estado']) ? mysqli_real_escape_string($conn, $_GET['estado']) : '';

// Construir consulta base
$query = "
    SELECT o.*, a.nombre as area_nombre, l.nombre as usuario_nombre,
           ad.nombre as area_derivada_nombre, ud.nombre as usuario_derivado_nombre
    FROM oficios o 
    LEFT JOIN areas a ON o.area_id = a.id 
    LEFT JOIN login l ON o.usuario_id = l.id
    LEFT JOIN areas ad ON o.area_derivada_id = ad.id
    LEFT JOIN login ud ON o.usuario_derivado_id = ud.id
    WHERE 1=1
";

// Aplicar filtros
if (!empty($filtro_numero)) {
    $query .= " AND o.numero_documento LIKE '%$filtro_numero%'";
}

if (!empty($filtro_estado)) {
    $query .= " AND o.estado = '$filtro_estado'";
}

$query .= " ORDER BY o.fecha_registro DESC";

// Ejecutar consulta
$result = mysqli_query($conn, $query);
$expedientes = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $expedientes[] = $row;
    }
}

// Procesar eliminación de oficio
if (isset($_GET['eliminar'])) {
    $id_eliminar = mysqli_real_escape_string($conn, $_GET['eliminar']);
    
    // Primero obtenemos la información del archivo para eliminarlo del servidor
    $query_archivo = "SELECT archivo_ruta FROM oficios WHERE id = '$id_eliminar'";
    $result_archivo = mysqli_query($conn, $query_archivo);
    
    if ($result_archivo && mysqli_num_rows($result_archivo) > 0) {
        $archivo_info = mysqli_fetch_assoc($result_archivo);
        $archivo_ruta = $archivo_info['archivo_ruta'];
        
        // Eliminar el archivo físico si existe
        if (!empty($archivo_ruta) && file_exists($archivo_ruta)) {
            unlink($archivo_ruta);
        }
    }
    
    // Ahora eliminamos el registro de la base de datos
    $delete_query = "DELETE FROM oficios WHERE id = '$id_eliminar'";
    
    if (mysqli_query($conn, $delete_query)) {
        header("Location: expedientes.php?mensaje=Oficio eliminado correctamente");
        exit();
    } else {
        $mensaje_error = "Error al eliminar el oficio: " . mysqli_error($conn);
    }
}

// En la sección de procesar derivación de oficio
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['derivar_oficio'])) {
    $oficio_id = mysqli_real_escape_string($conn, $_POST['oficio_id']);
    $area_derivada = mysqli_real_escape_string($conn, $_POST['area_derivada']);
    $usuario_derivado = mysqli_real_escape_string($conn, $_POST['usuario_derivado']);
    $respuesta = mysqli_real_escape_string($conn, $_POST['respuesta']);
    
    $update_query = "UPDATE oficios SET 
                    area_derivada_id = '$area_derivada',
                    usuario_derivado_id = '$usuario_derivado',
                    respuesta = '$respuesta',
                    estado = 'tramite',  -- Cambiado a 'tramite' para admin
                    fecha_derivacion = NOW()
                    WHERE id = '$oficio_id'";
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: expedientes.php?mensaje=Oficio derivado correctamente");
        exit();
    } else {
        $mensaje_error = "Error al derivar el oficio: " . mysqli_error($conn);
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
    <title>SIS-OP - Gestión de Expedientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="..\..\css\dashboard\styledash.css">
</head>
<body>
    
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>SIS-OP</h3>
            <p>Sistema de Oficialia de Partes</p>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="homeadmin.php">
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
                <a class="nav-link active" href="expedientes.php">
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

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
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

        <!-- Mostrar mensajes -->
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['mensaje']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($mensaje_error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $mensaje_error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Page Title -->
        <h3 class="page-title">Gestión de Expedientes</h3>

        <!-- Search Section -->
        <div class="search-section">
            <h5 class="search-title">Búsqueda de Expedientes</h5>
            <form method="GET" action="">
                <div class="search-grid">
                    <div class="search-field">
                        <label for="numero">Número de Documento</label>
                        <input type="text" id="numero" name="numero" placeholder="Ingrese número de documento" value="<?php echo htmlspecialchars($filtro_numero); ?>">
                    </div>
                    <div class="search-field">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" <?php echo $filtro_estado == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="tramite" <?php echo $filtro_estado == 'tramite' ? 'selected' : ''; ?>>En tramite</option>
                            <option value="completado" <?php echo $filtro_estado == 'completado' ? 'selected' : ''; ?>>Completado</option>
                            <option value="denegado" <?php echo $filtro_estado == 'denegado' ? 'selected' : ''; ?>>Denegado</option>
                        </select>
                        
                    </div>
                </div>
                <div class="search-actions">
                    <a href="expedientes.php" class="btn btn-secondary">Limpiar</a>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-header">
                <h5>Listado de Expedientes</h5>
                <div class="export-buttons">
                    <!-- Los botones se generarán automáticamente con DataTables -->
                </div>
            </div>
            <!--AQUIIII-->
                        <div class="table-container">
                <?php if (empty($expedientes)): ?>
                    <!-- Mensaje cuando no hay expedientes -->
                    <div class="no-expedientes-message">
                        <i class="fas fa-folder-open"></i>
                        <h4>No se encontraron expedientes en ese estado</h4>
                        <p>No hay expedientes que coincidan con los criterios de búsqueda.</p>
                    </div>
                    
                    <!-- Tabla oculta para DataTables (necesaria para evitar errores) -->
                    <table id="expedientesTable" class="table table-striped table-hover" style="width:100%; display:none;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha/Hora</th>
                                <th>Remitente</th>
                                <th>Asunto</th>
                                <th>Nro. Documento</th>
                                <th>Estado</th>
                                <th>Derivado a</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                <?php else: ?>
                    <!-- Tabla normal cuando sí hay expedientes -->
                    <table id="expedientesTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha/Hora</th>
                                <th>Remitente</th>
                                <th>Asunto</th>
                                <th>Nro. Documento</th>
                                <th>Estado</th>
                                <th>Derivado a</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expedientes as $expediente): ?>
                                <tr>
                                    <td><?php echo $expediente['id']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($expediente['fecha_registro'])); ?></td>
                                    <td><?php echo htmlspecialchars($expediente['remitente']); ?></td>
                                    <td><?php echo htmlspecialchars($expediente['asunto']); ?></td>
                                    <td><?php echo htmlspecialchars($expediente['numero_documento'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php 
                                        $badge_class = '';
                                        switch ($expediente['estado']) {
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
                                    </td>
                                    <td>
                                        <?php if (!empty($expediente['area_derivada_nombre'])): ?>
                                            <div><strong>Área:</strong> <?php echo htmlspecialchars($expediente['area_derivada_nombre']); ?></div>
                                            <?php if (!empty($expediente['usuario_derivado_nombre'])): ?>
                                                <div><strong>Usuario:</strong> <?php echo htmlspecialchars($expediente['usuario_derivado_nombre']); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($expediente['fecha_derivacion'])): ?>
                                                <div class="info-derivacion">
                                                    <?php echo date('d/m/Y H:i', strtotime($expediente['fecha_derivacion'])); ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">No derivado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="action-buttons">
                                        <!-- Ver documento -->
                                        <?php if (!empty($expediente['archivo_ruta'])): ?>
                                            <a href="<?php echo $expediente['archivo_ruta']; ?>" target="_blank" class="btn btn-sm btn-primary" title="Ver documento">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" title="Sin documento" disabled>
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <!-- Derivar documento -->
                                        <button class="btn btn-sm btn-warning" title="Derivar documento" onclick="abrirModalDerivacion(<?php echo $expediente['id']; ?>, '<?php echo htmlspecialchars($expediente['respuesta'] ?? ''); ?>')">
                                            <i class="fas fa-share"></i>
                                        </button>
                                        
                                        <!-- Responder documento -->
                                        <a href="responder_oficio.php?id=<?php echo $expediente['id']; ?>" class="btn btn-sm btn-success" title="Responder documento">
                                            <i class="fas fa-reply"></i>
                                        </a>
                                        
                                        <!-- Eliminar documento -->
                                        <button class="btn btn-sm btn-danger" title="Eliminar documento" onclick="confirmarEliminacion(<?php echo $expediente['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
                                        <!--aquiiiiiii-->
        </div>
    </div>

    <!-- Modal de Derivación -->
    <div class="modal fade" id="modalDerivacion" tabindex="-1" aria-labelledby="modalDerivacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDerivacionLabel">Derivar Oficio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="oficio_id" id="oficio_id">
                        <input type="hidden" name="derivar_oficio" value="1">
                        
                        <div class="mb-3">
                            <label for="area_derivada" class="form-label">Área de Destino</label>
                            <select class="form-select" id="area_derivada" name="area_derivada" required onchange="cargarUsuariosPorArea(this.value)">
                                <option value="">Seleccionar área</option>
                                <?php foreach ($areas as $area): ?>
                                    <option value="<?php echo $area['id']; ?>"><?php echo htmlspecialchars($area['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="usuario_derivado" class="form-label">Usuario de Destino (Opcional)</label>
                            <select class="form-select" id="usuario_derivado" name="usuario_derivado">
                                <option value="">Seleccionar usuario</option>
                                <!-- Los usuarios se cargarán dinámicamente mediante AJAX -->
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="respuesta" class="form-label">Respuesta/Comentario</label>
                            <textarea class="form-control" id="respuesta" name="respuesta" rows="4" placeholder="Ingrese una respuesta o comentario sobre este oficio"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Derivar Oficio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTable con botones de exportación
            if ($('#expedientesTable:visible').length) {
            $('#expedientesTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copiar',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-sm btn-secondary'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-sm btn-secondary'
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json'
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                order: [[0, 'desc']]
            });
        }
            // Mover los botones de exportación al contenedor correcto
            $('.dt-buttons').appendTo('.export-buttons');
        });

        function abrirModalDerivacion(id, respuesta) {
            $('#oficio_id').val(id);
            $('#respuesta').val(respuesta);
            
            // Resetear selects al abrir el modal
            $('#area_derivada').val('');
            $('#usuario_derivado').html('<option value="">Seleccionar usuario</option>');
            
            $('#modalDerivacion').modal('show');
        }

        function confirmarEliminacion(id) {
            if (confirm('¿Está seguro de eliminar este oficio? Esta acción no se puede deshacer.')) {
                window.location.href = 'expedientes.php?eliminar=' + id;
            }
        }

        // Función para cargar usuarios por área mediante AJAX
        function cargarUsuariosPorArea(areaId) {
            if (!areaId) {
                $('#usuario_derivado').html('<option value="">Seleccionar usuario</option>');
                return;
            }
            
            $.ajax({
                url: 'expedientes.php?ajax=usuarios_por_area&area_id=' + areaId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var options = '<option value="">Seleccionar usuario</option>';
                        
                        $.each(response.usuarios, function(index, usuario) {
                            options += '<option value="' + usuario.id + '">' + 
                                       usuario.nombre + ' (' + usuario.usuario + ')' + 
                                       '</option>';
                        });
                        
                        $('#usuario_derivado').html(options);
                    } else {
                        alert('Error al cargar los usuarios');
                        $('#usuario_derivado').html('<option value="">Seleccionar usuario</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud AJAX:', error);
                    alert('Error al cargar los usuarios. Por favor, intente nuevamente.');
                    $('#usuario_derivado').html('<option value="">Seleccionar usuario</option>');
                }
            });
        }
    </script>
</body>
</html>