<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS-OP - Gestión de Áreas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />

    
    <link rel="stylesheet" href="/mvc_oficialiapartes/css/dashboard/styledash.css">
    <link rel="stylesheet" href="/mvc_oficialiapartes/css/dashboard/styleareasusers.css">
    <link rel="stylesheet" href="/mvc_oficialiapartes/css/dashboard/styleareasexpedientes.css">

    <style>
        .btn-action {
            margin: 0 3px;
        }
        .area-card {
            transition: transform 0.2s;
        }
        .area-card:hover {
            transform: translateY(-2px);
        }
        .user-list {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        .user-item {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .user-item:last-child {
            border-bottom: none;
        }
        .user-count {
            display: inline-block;
            background-color: #3498db;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            text-align: center;
            line-height: 25px;
            font-size: 0.8rem;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    
    <!-- Sidebar header -->
    <aside class="sidebar">

        <div class="sidebar-header">
            <div class="user-avatar"><?php echo substr($_SESSION['nombre'], 0, 1); ?></div>
            <button class="toggler sidebar-toggler">
                <span class="material-symbols-rounded">chevron_left</span>
            </button>
            <button class="toggler menu-toggler">
                <span class="material-symbols-rounded">menu</span>
            </button>
        </div>
        
        <nav class="sidebar-nav">
        <ul class="nav-list primary-nav">

            <li class="nav-item">
                <a class="nav-link" href="index.php?action=homedash">
                    <span class="nav icon
                    material-symbols-rounded">Home</span>
                    <span class="nav-label">Inicio</span>
                </a>
                <span class="nav-tooltip">Inicio</span>
            </li>

            <?php if ($_SESSION['tipo_usuario'] === 'Administrador'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?action=areasadmin">
                        <span class="nav icon
                        material-symbols-rounded">Apartment</span>
                        <span class="nav-label">Áreas</span>
                    </a>
                    <span class="nav-tooltip">Áreas</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?action=usersadmin">
                        <span class="nav icon
                        material-symbols-rounded">Group</span>
                        <span class="nav-label">Usuarios</span>
                    </a>
                    <span class="nav-tooltip">Usuarios</span>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link" href="index.php?action=expedientes">
                    <span class="nav icon
                        material-symbols-rounded">Folder</span>
                    <span class="nav-label">Expedientes</span>
                </a>
                <span class="nav-tooltip">Expedientes</span>
            </li>

        </ul>

        <ul class="nav-list secondary-nav">
            <li class="nav-item mt-4">
                <a class="nav-link" href="index.php?action=config">
                    <span class="nav icon
                        material-symbols-rounded">Settings</span>
                    <span class="nav-label">Configuración</span>
                </a>
                <span class="nav-tooltip">Configuración</span>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="index.php?action=logout">
                    <span class="nav icon
                        material-symbols-rounded">Logout</span>
                    <span class="nav-label">Cerrar Sesión</span>
                </a>
                <span class="nav-tooltip">Cerrar Sesión</span>
            </li>
        </ul>
        </nav>

    </aside>

    
    <div class="main-content">
        
        <div class="header">
            <h2 class="mb-0">Dashboard Administrador</h2>
            <div class="user-info">
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

        <?php if (!empty($mensaje) && !isset($_GET['mensaje'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        
        <div class="page-title">
            <h3>Gestión de Áreas</h3>
            <button class="btn btn-new" data-bs-toggle="modal" data-bs-target="#nuevaAreaModal">
                <i class="fas fa-plus"></i> Nueva Área
            </button>
        </div>

        
        <div class="users-container">
            <?php if (empty($areas)): ?>
                <div class="alert alert-warning">
                    No hay áreas registradas en el sistema.
                </div>
            <?php else: ?>
                <?php foreach ($areas as $area): ?>
                    <div class="user-card area-card">
                        <div class="user-card-header">
                            <div class="user-name"><?php echo htmlspecialchars($area['nombre']); ?></div>
                            <div class="user-id">ID: <?php echo $area['id']; ?></div>
                        </div>
                        <div class="user-details">
                            <div class="user-detail">
                                <div class="user-detail-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="user-detail-text">
                                    <strong>Descripción:</strong> 
                                    <?php echo !empty($area['descripcion']) ? htmlspecialchars($area['descripcion']) : 'Sin descripción'; ?>
                                </div>
                            </div>
                            <div class="user-detail">
                                <div class="user-detail-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="user-detail-text">
                                    <strong>Creación:</strong> 
                                    <?php echo date('d/m/Y', strtotime($area['fecha_creacion'])); ?>
                                </div>
                            </div>
                            <div class="user-detail">
                                <div class="user-detail-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="user-detail-text">
                                    <strong>Usuarios asignados:</strong> 
                                    <span class="badge bg-<?php echo $area['total_usuarios'] > 0 ? 'success' : 'secondary'; ?>">
                                        <?php echo $area['total_usuarios']; ?> usuario(s)
                                    </span>
                                </div>
                            </div>
                            <div class="user-detail">
                                <div class="user-detail-icon">
                                    <i class="fas fa-status"></i>
                                </div>
                                <div class="user-detail-text">
                                    <strong>Estado:</strong> 
                                    <span class="badge bg-<?php echo $area['activo'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $area['activo'] ? 'Activa' : 'Inactiva'; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Mostrar lista de usuarios si existen -->
                            <?php if ($area['total_usuarios'] > 0): ?>
                            <div class="user-detail">
                                <div class="user-detail-icon">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                                <div class="user-detail-text">
                                    <strong>Lista de usuarios:</strong>
                                    <div class="user-list">
                                        <?php 
                                        $usuarios = explode('| ', $area['usuarios_lista']);
                                        foreach ($usuarios as $usuario): 
                                        ?>
                                            <div class="user-item">
                                                <i class="fas fa-user-circle me-2 text-muted"></i>
                                                <?php echo htmlspecialchars($usuario); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="user-actions">
                            <a href="index.php?action=areasadmin&editar=<?php echo $area['id']; ?>" class="btn btn-sm btn-primary btn-action">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button class="btn btn-sm btn-danger btn-action" 
                                    onclick="confirmarEliminacion(<?php echo $area['id']; ?>, '<?php echo htmlspecialchars($area['nombre']); ?>')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para Nueva Área -->
    <div class="modal fade" id="nuevaAreaModal" tabindex="-1" aria-labelledby="nuevaAreaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nuevaAreaModalLabel">Nueva Área</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Área *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required 
                                   placeholder="Ej: Recursos Humanos, Contabilidad, etc.">
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" 
                                      rows="3" placeholder="Descripción de las funciones del área"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="crear_area" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Área -->
    <?php if ($area_editar): ?>
    <div class="modal fade show" id="editarAreaModal" tabindex="-1" aria-labelledby="editarAreaModalLabel" aria-hidden="false" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarAreaModalLabel">Editar Área</h5>
                    <a href="index.php?action=areasadmin" class="btn-close" aria-label="Close"></a>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?php echo $area_editar['id']; ?>">
                    <input type="hidden" name="nombre_anterior" value="<?php echo htmlspecialchars($area_editar['nombre']); ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre_edit" class="form-label">Nombre del Área *</label>
                            <input type="text" class="form-control" id="nombre_edit" name="nombre" 
                                   value="<?php echo htmlspecialchars($area_editar['nombre']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion_edit" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion_edit" name="descripcion" 
                                      rows="3"><?php echo htmlspecialchars($area_editar['descripcion']); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="index.php?action=areasadmin" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" name="editar_area" class="btn btn-success">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarEliminacion(id, nombre) {
            if (confirm(`¿Estás seguro de eliminar el área "${nombre}"? Esta acción no se puede deshacer.`)) {
                window.location.href = `index.php?action=areasadmin&eliminar=${id}`;
            }
        }
        
        // Cerrar modal de edición al hacer clic fuera
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('editarAreaModal');
            if (event.target === modal) {
                window.location.href = 'index.php?action=areasadmin';
            }
        });

        // Cerrar modal con ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                window.location.href = 'index.php?action=areasadmin';
            }
        });
    </script>

<script src="../mvc_oficialiapartes/scripts/script.js"></script>
</body>
</html>