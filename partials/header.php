
<div class="header">
    <h2 class="mb-0">Dashboard</h2>
    <div class="user-info">
        <?php if ($_SESSION['tipo_usuario'] === 'Administrador' || $_SESSION['tipo_usuario'] === 'Usuario'): ?>
            <div class="icon-notifi-box">
                <button class="material-symbols-rounded" id="notificationButton">notifications</button>
                <div class="modal-notification-box" id="notificationModal">
                    <div class="header-notification">
                        <div class="header-notification-items">
                            <h3 class="notifi-title">Notificaciones</h3>
                        </div>
                    </div>
                    <!-- Recent Activity -->
                    <div class="notifi-box">            
                        <?php if (empty($actividad_reciente)): ?>
                            <div class="no-notifications">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>Hola <?php echo $_SESSION['nombre']; ?>, no tiene notificaciones</p>
                            </div>
                        <?php else: ?>
                        <?php foreach ($actividad_reciente as $oficio): ?>
                            <a class="notifi-item" href="index.php?action=expedientes">
                                <div class="notifi-icon">
                                    <i class="<?php echo getActivityIcon($oficio['estado']); ?>"></i>
                                </div>
                                <div class="notifi-content">
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
                                    <div class="notifi-time">
                                        <?php echo formatFecha($oficio['fecha_registro']); ?> | 
                                        Área: <?php echo $oficio['area_nombre']; ?> | 
                                        Por: <?php echo $oficio['usuario_nombre']; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div>
            <div class="fw-bold"><?php echo $_SESSION['nombre']; ?></div>
            <div class="small"><?php echo $_SESSION['tipo_usuario']; ?></div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationButton = document.getElementById('notificationButton');
        const notificationModal = document.getElementById('notificationModal');
        let isModalOpen = false;
        
        // Abrir/cerrar modal al hacer clic en el botón de notificaciones
        notificationButton.addEventListener('click', function(e) {
            e.stopPropagation();
            
            if (isModalOpen) {
                closeModal();
            } else {
                openModal();
            }
        });
        
        // Cerrar modal al hacer clic fuera de él
        document.addEventListener('click', function(e) {
            if (isModalOpen && !notificationModal.contains(e.target) && e.target !== notificationButton) {
                closeModal();
            }
        });
        
        // Cerrar modal con la tecla Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isModalOpen) {
                closeModal();
            }
        });
        
        function openModal() {
            notificationModal.classList.add('show');
            isModalOpen = true;
            // Agregar clase al body para posible control adicional
            document.body.classList.add('modal-notification-open');
        }
        
        function closeModal() {
            notificationModal.classList.remove('show');
            isModalOpen = false;
            // Remover clase del body
            document.body.classList.remove('modal-notification-open');
        }
        
        // Prevenir que los clics dentro del modal se propaguen
        notificationModal.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
