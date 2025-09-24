<?php
// Headers para prevenir caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficialia de Partes - Registrar Oficio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/mvc_oficialiapartes/css/caseta/styleregistro.css">
</head>

<body>
    <!-- Overlay para cerrar barra en móviles -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Botón para abrir/cerrar la barra lateral - SIEMPRE VISIBLE -->
    <button id="sidebarToggle" class="btn btn-primary sidebar-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Barra lateral - INICIALMENTE OCULTA -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-header-content">
                <h3>SIS-OP</h3>
                <p>Sistema de Oficialia de Partes</p>
            </div>
            <button class="close-sidebar" id="closeSidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="index.php?action=registrar">
                    <i class="fas fa-file-alt"></i>
                    <span>Registrar</span>
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

    <!-- Contenido principal -->
    <div class="main-content" id="mainContent">
        <div class="header">
            <h2 class="mb-0">Registrar Oficio</h2>
            <div class="user-info">
                <div>
                    <div class="fw-bold">Oficialía de Partes</div>
                    <div class="small text-muted">Caseta</div>
                </div>
            </div>
        </div>

        <div class="form-container">
            <form id="registerForm" method="POST" enctype="multipart/form-data">
                <!-- Sección de Remitente -->
                <div class="form-section">
                    <h3 class="form-section-title">Datos del Remitente</h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="remitente" class="form-label">Remitente</label>
                            <input type="text" class="form-control" id="remitente" name="remitente" placeholder="Nombre completo o razón social" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tipoPersona" class="form-label">Tipo de Persona</label>
                            <select class="form-select" id="tipoPersona" name="tipoPersona" required>
                                <option value="" selected disabled>Seleccionar tipo</option>
                                <option value="natural">Persona Natural</option>
                                <option value="juridica">Persona Jurídica</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Documento</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input type="radio" id="tipoCarta" name="tipoDocumento" value="carta" required>
                                    <label for="tipoCarta">Carta, oficio, etc.</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" id="tipoRucDni" name="tipoDocumento" value="ruc_dni">
                                    <label for="tipoRucDni">Numero de Oficio</label>
                                </div>
                            </div>
                            <input type="text" class="form-control" id="numeroDocumento" name="numeroDocumento" placeholder="Número de oficio" style="display: none;">
                        </div>
                        <div class="col-md-6">
                            <label for="folios" class="form-label">Folios</label>
                            <input type="number" class="form-control" id="folios" name="folios" placeholder="Número de folios" min="1" required>
                        </div>
                    </div>
                </div>
                
                <!-- Sección de Contacto -->
                <div class="form-section">
                    <h3 class="form-section-title">Datos de Contacto</h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" placeholder="ejemplo@correo.com" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Número de teléfono" required>
                        </div>
                    </div>
                </div>
                
                <!-- Sección de Contenido -->
                <div class="form-section">
                    <h3 class="form-section-title">Contenido del Trámite</h3>
                    
                    <div class="mb-3">
                        <label for="asunto" class="form-label">Asunto</label>
                        <input type="text" class="form-control" id="asunto" name="asunto" placeholder="Asunto del trámite" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Archivo</label>
                        <div class="file-upload" id="fileUploadArea">
                            <input type="file" id="archivo" name="archivo" style="display: none;">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Seleccionar archivo</p>
                            <p class="file-name" id="fileName">Ningún archivo seleccionado</p>
                        </div>
                    </div>
                    
                    <!-- Información fija sobre el área y usuario asignado -->
                    <div class="alert alert-info">
                        <strong>Información del registro:</strong><br>
                        - Este documento será dirigido hacia recepción
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn-action btn-register">
                        <i class="fas fa-check-circle me-2"></i> Registrar
                    </button>
                    <button type="button" class="btn-action btn-cancel" id="cancelButton">
                        <i class="fas fa-times-circle me-2"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mostrar/ocultar campo de número de documento según selección
        document.querySelectorAll('input[name="tipoDocumento"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const numeroDocumento = document.getElementById('numeroDocumento');
                if (this.value === 'ruc_dni') {
                    numeroDocumento.style.display = 'block';
                    numeroDocumento.setAttribute('required', 'true');
                } else {
                    numeroDocumento.style.display = 'none';
                    numeroDocumento.removeAttribute('required');
                }
            });
        });
        
        // Manejar la subida de archivos
        const fileInput = document.getElementById('archivo');
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileName = document.getElementById('fileName');
        
        fileUploadArea.addEventListener('click', function() {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileName.textContent = this.files[0].name;
            } else {
                fileName.textContent = 'Ningún archivo seleccionado';
            }
        });
        
        // Manejar el botón cancelar
        document.getElementById('cancelButton').addEventListener('click', function() {
            if (confirm('¿Está seguro que desea cancelar? Se perderán todos los datos ingresados.')) {
                window.location.href = 'index.php';
            }
        });
        
        // Control de la barra lateral - CÓDIGO MEJORADO
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        // Función para abrir la barra lateral
        function openSidebar() {
            sidebar.classList.add('active');
            mainContent.classList.add('sidebar-active');
            sidebarOverlay.classList.add('active');
            sidebarToggle.classList.add('hidden'); // Ocultar botón de abrir
        }

        // Función para cerrar la barra lateral
        function closeSidebarFunc() {
            sidebar.classList.remove('active');
            mainContent.classList.remove('sidebar-active');
            sidebarOverlay.classList.remove('active');
            sidebarToggle.classList.remove('hidden'); // Mostrar botón de abrir
        }

        // Abrir barra lateral al hacer clic en el botón
        sidebarToggle.addEventListener('click', function() {
            openSidebar();
        });

        // Cerrar barra lateral con el botón de cerrar
        closeSidebar.addEventListener('click', closeSidebarFunc);

        // Cerrar barra lateral al hacer clic fuera (solo en móviles)
        sidebarOverlay.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                closeSidebarFunc();
            }
        });

        // Cerrar barra lateral al redimensionar a pantalla grande
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && sidebar.classList.contains('active')) {
                closeSidebarFunc();
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>