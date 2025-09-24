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
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --success-color: #27ae60;
        }

        /* Estilos generales */
        body {
            background: linear-gradient(135deg, #82adec 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            margin: 0;
            transition: all 0.3s;
        }

        /* Botón para abrir/cerrar barra lateral - SIEMPRE VISIBLE */
        .sidebar-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1002; /* Mayor z-index para que esté sobre la barra */
            display: flex;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            align-items: center;
            justify-content: center;
            background-color: var(--primary-color);
            color: white;
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: all 0.3s;
            cursor: pointer;
        }

        .sidebar-toggle:hover {
            background-color: var(--secondary-color);
            transform: scale(1.1);
        }

        /* Barra lateral - INICIALMENTE OCULTA */
        .sidebar {
            background: var(--primary-color);
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            padding-top: 20px;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1001;
            transition: transform 0.3s ease;
            transform: translateX(-100%); /* Inicialmente oculta */
        }

        .sidebar.active {
            transform: translateX(0); /* Visible cuando tiene clase active */
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-header-content {
            flex: 1;
        }

        .sidebar-header h3 {
            font-weight: 700;
            margin: 0;
            color: white;
        }

        .sidebar-header p {
            color: rgba(255, 255, 255, 0.7);
            margin: 5px 0 0;
            font-size: 14px;
        }

        .close-sidebar {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
            margin-left: 10px;
        }

        .close-sidebar:hover {
            color: var(--secondary-color);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 0 30px 30px 0;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Contenido principal - Ocupa todo el ancho cuando barra está oculta */
        .main-content {
            transition: margin-left 0.3s ease;
            width: 100%;
        }

        .main-content.sidebar-active {
            margin-left: 250px;
            width: calc(100% - 250px);
        }

        /* Overlay para cerrar barra al hacer clic fuera en móviles */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .sidebar-overlay.active {
            display: block;
        }

        .header {
            background: var(--primary-color);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            margin-bottom: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 28px;
            margin-left: 60px; /* Espacio para el botón */
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Contenedor del formulario */
        .form-container {
            background: white;
            border-radius: 0 0 10px 10px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            margin-bottom: 20px;
        }

        /* Secciones del formulario */
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid var(--secondary-color);
        }

        .form-section-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        /* Elementos del formulario */
        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 15px;
            border: 2px solid #ddd;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        /* Grupo de radio buttons */
        .radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
        }

        .radio-option {
            display: flex;
            align-items: center;
        }

        .radio-option input[type="radio"] {
            margin-right: 8px;
        }

        /* Subida de archivos */
        .file-upload {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .file-upload:hover {
            border-color: var(--secondary-color);
            background-color: #f8f9fa;
        }

        .file-upload i {
            font-size: 40px;
            color: var(--secondary-color);
            margin-bottom: 10px;
        }

        .file-name {
            margin-top: 10px;
            font-style: italic;
            color: #777;
        }

        /* Botones de acción */
        .btn-action {
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            border: none;
            color: white;
        }

        .btn-register {
            background: var(--success-color);
        }

        .btn-register:hover {
            background: #219653;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background: var(--accent-color);
        }

        .btn-cancel:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 280px;
            }
            
            .main-content.sidebar-active {
                margin-left: 0;
                width: 100%;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .header h2 {
                margin-left: 0;
                margin-top: 20px;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn-action {
                width: 100%;
                margin-bottom: 10px;
            }
            
            .sidebar-toggle {
                top: 15px;
                left: 15px;
            }
        }
    </style>
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
        
        // Control de la barra lateral - CÓDIGO CORREGIDO
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
        }

        // Función para cerrar la barra lateral
        function closeSidebarFunc() {
            sidebar.classList.remove('active');
            mainContent.classList.remove('sidebar-active');
            sidebarOverlay.classList.remove('active');
        }

        // Abrir barra lateral al hacer clic en el botón
        sidebarToggle.addEventListener('click', function() {
            if (sidebar.classList.contains('active')) {
                closeSidebarFunc();
            } else {
                openSidebar();
            }
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