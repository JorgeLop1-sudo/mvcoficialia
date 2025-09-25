<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        
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