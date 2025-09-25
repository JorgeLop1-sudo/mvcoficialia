<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
            border-radius: 5px; /* Cambiado a cuadrado */
            width: 50px;
            height: 50px;
            align-items: center;
            justify-content: center;
            background-color: var(--primary-color);
            color: white;
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3); /* Sombra más pronunciada */
            transition: all 0.3s;
            cursor: pointer;
        }

        .sidebar-toggle:hover {
            background-color: var(--secondary-color);
            transform: scale(1.1);
        }

        /* Ocultar el botón cuando la barra está activa */
        .sidebar-toggle.hidden {
            display: none;
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

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
                padding-top: 15px;
            }

            .sidebar-header h3, .sidebar-header p, .nav-link span {
                display: none;
            }

            .nav-link {
                text-align: center;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                margin: 10px auto;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .nav-link i {
                margin-right: 0;
                font-size: 20px;
            }

            .sidebar-toggle {
                top: 15px;
                left: 15px;
            }
            .main-content.sidebar-active {
                margin-left: 0;
                width: 100%;
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

    </div>


<script>
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

</body>
</html>