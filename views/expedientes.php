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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />

    <link rel="stylesheet" href="/mvc_oficialiapartes/css/globals/style-body.css">
    <link rel="stylesheet" href="/mvc_oficialiapartes/css/globals/style-sidebar.css">
    <link rel="stylesheet" href="/mvc_oficialiapartes/css/globals/style-badge.css">
    <link rel="stylesheet" href="/mvc_oficialiapartes/css/dashboard/styleexpedientes.css">


</head>
<body>
    <!-- Incluir el sidebar -->
    <?php include 'partials/sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'partials/header.php'; ?>

        <!-- Mostrar mensajes -->
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['mensaje']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Page Title -->
        <h3 class="page-title">Gestión de Expedientes</h3>

        <!-- Search Section -->
        <div class="search-section">
            <h5 class="search-title">Búsqueda de Expedientes</h5>
            <form method="GET" action="">
                <input type="hidden" name="action" value="expedientes">
                <div class="search-grid">
                    <div class="search-field">
                        <label for="numero">Número de Documento</label>
                        <input type="text" id="numero" name="numero" placeholder="Ingrese número de documento" value="<?php echo htmlspecialchars($filtros['numero'] ?? ''); ?>">
                    </div>
                    <div class="search-field">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="">Todos los estados</option>

                            <?php if ($_SESSION['tipo_usuario'] === 'Administrador'): ?>
                                <option value="pendiente" <?php echo ($filtros['estado'] ?? '') == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="tramite" <?php echo ($filtros['estado'] ?? '') == 'tramite' ? 'selected' : ''; ?>>En tramite</option>
                                <option value="completado" <?php echo ($filtros['estado'] ?? '') == 'completado' ? 'selected' : ''; ?>>Completado</option>
                                <option value="denegado" <?php echo ($filtros['estado'] ?? '') == 'denegado' ? 'selected' : ''; ?>>Denegado</option>
                            <?php endif; ?>

                            <?php if ($_SESSION['tipo_usuario'] === 'Usuario'): ?>
                                <option value="tramite" <?php echo ($filtros['estado'] ?? '') == 'tramite' ? 'selected' : ''; ?>>En tramite</option>
                                <option value="completado" <?php echo ($filtros['estado'] ?? '') == 'completado' ? 'selected' : ''; ?>>Completado</option>
                                <option value="denegado" <?php echo ($filtros['estado'] ?? '') == 'denegado' ? 'selected' : ''; ?>>Denegado</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="search-actions">
                    <a href="index.php?action=expedientes" class="btn btn-secondary">Limpiar</a>
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
            <div class="table-container">
                <?php if (empty($expedientes)): ?>
                    <div class="no-expedientes-message">
                        <i class="fas fa-folder-open"></i>
                        <h4>No se encontraron expedientes en ese estado</h4>
                        <p>No hay expedientes que coincidan con los criterios de búsqueda.</p>
                    </div>
                    
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
                                    <?php if (!empty($expediente['archivo_ruta'])): ?>
                                        <a href="<?php echo $expediente['archivo_ruta']; ?>" target="_blank" class="btn btn-sm btn-primary" title="Ver documento">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" title="Sin documento" disabled>
                                            <i class="fas fa-eye-slash"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <!-- Solo mostrar derivar si es admin o si el oficio está asignado al usuario -->
                                    <?php if ($tipo_usuario === 'Administrador' || $expediente['usuario_derivado_id'] == $_SESSION['id']): ?>
                                        <button class="btn btn-sm btn-warning" title="Derivar documento" onclick="abrirModalDerivacion(<?php echo $expediente['id']; ?>, '<?php echo htmlspecialchars($expediente['respuesta'] ?? ''); ?>')">
                                            <i class="fas fa-share"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <!-- Solo mostrar responder si el oficio está asignado al usuario actual -->
                                    <?php if ($expediente['usuario_derivado_id'] == $_SESSION['id'] || $tipo_usuario === 'Administrador'): ?>
                                        <a href="index.php?action=responderoficio&id=<?php echo $expediente['id']; ?>" class="btn btn-sm btn-success" title="Responder documento">
                                            <i class="fas fa-reply"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($tipo_usuario === 'Administrador'): ?>
                                        <button class="btn btn-sm btn-danger" title="Eliminar documento" onclick="confirmarEliminacion(<?php echo $expediente['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
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
                <form method="POST" action="" id="formDerivacion">
                    <input type="hidden" name="action" value="expedientes">
                    <div class="modal-body">
                        <input type="hidden" name="oficio_id" id="oficio_id">
                        <input type="hidden" name="derivar_oficio" value="1">
                        
                        <div class="mb-3">
                                <label for="area_derivada" class="form-label required">Área de Destino</label>                            <select class="form-select" id="area_derivada" name="area_derivada" required onchange="cargarUsuariosPorArea(this.value)">
                                <option value="">Seleccionar área</option>
                                <?php foreach ($areas as $area): ?>
                                    <option value="<?php echo $area['id']; ?>"><?php echo htmlspecialchars($area['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                        <label for="usuario_derivado" class="form-label required">Usuario de Destino</label>                            <select class="form-select" id="usuario_derivado" name="usuario_derivado" required disabled>
                                <option value="">Primero seleccione un área</option>
                            </select>
                            <div class="form-text">Debe seleccionar un usuario para derivar el oficio</div>
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
                    responsive: true,  // Esta línea activa el plugin responsivo
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                    order: [[0, 'desc']],
                    // Configuración adicional para responsivo
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function (row) {
                                    var data = row.data();
                                    return 'Detalles del Expediente #' + data[0];
                                }
                            }),
                            renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                                tableClass: 'table'
                            })
                        }
                    }
                });
                
                $('.dt-buttons').appendTo('.export-buttons');
            }
        });

        // Validación del formulario de derivación
        document.getElementById('formDerivacion').addEventListener('submit', function(e) {
            var areaSelect = document.getElementById('area_derivada');
            var usuarioSelect = document.getElementById('usuario_derivado');
            
            if (!areaSelect.value) {
                e.preventDefault();
                alert('Debe seleccionar un área de destino');
                areaSelect.focus();
                return false;
            }
            
            if (!usuarioSelect.value || usuarioSelect.disabled) {
                e.preventDefault();
                alert('Debe seleccionar un usuario de destino');
                usuarioSelect.focus();
                return false;
            }
            
            return true;
        });

        function abrirModalDerivacion(id, respuesta) {
            $('#oficio_id').val(id);
            $('#respuesta').val(respuesta);
            $('#area_derivada').val('');
            $('#usuario_derivado').html('<option value="">Seleccionar usuario</option>');
            $('#usuario_derivado').prop('disabled', true);
            $('#usuario_derivado').prop('required', true);
            $('#modalDerivacion').modal('show');
        }

        function confirmarEliminacion(id) {
            if (confirm('¿Está seguro de eliminar este oficio? Esta acción no se puede deshacer.')) {
                window.location.href = 'index.php?action=expedientes&eliminar=' + id;
            }
        }

        function cargarUsuariosPorArea(areaId) {
            var usuarioSelect = $('#usuario_derivado');
            
            if (!areaId) {
                usuarioSelect.html('<option value="">Primero seleccione un área</option>');
                usuarioSelect.prop('disabled', true);
                usuarioSelect.prop('required', true);
                return;
            }
            
            $.ajax({
                url: 'index.php?action=expedientes&ajax=usuarios_por_area&area_id=' + areaId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var options = '<option value="">Seleccionar usuario</option>';
                        if (response.usuarios.length > 0) {
                            $.each(response.usuarios, function(index, usuario) {
                                options += '<option value="' + usuario.id + '">' + 
                                        usuario.nombre + ' (' + usuario.usuario + ')' + 
                                        '</option>';
                            });
                            usuarioSelect.html(options);
                            usuarioSelect.prop('disabled', false);
                        } else {
                            options = '<option value="">No hay usuarios en esta área</option>';
                            usuarioSelect.html(options);
                            usuarioSelect.prop('disabled', true);
                        }
                    } else {
                        usuarioSelect.html('<option value="">Error al cargar usuarios</option>');
                        usuarioSelect.prop('disabled', true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud AJAX:', error);
                    usuarioSelect.html('<option value="">Error al cargar usuarios</option>');
                    usuarioSelect.prop('disabled', true);
                }
            });
        }
    </script>

<script src="../mvc_oficialiapartes/scripts/navbar.js"></script>
</body>
</html>