<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS-OP - Configuración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/mvc_oficialiapartes/css/dashboard/styledash.css">
    <style>
        .config-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .password-feedback, .user-feedback {
            display: none;
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        
        .form-group {
            margin-bottom: 1.2rem;
        }
        
        .required-field::after {
            content: " *";
            color: red;
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

            <?php if ($_SESSION['tipo_usuario'] === 'Administrador' || $_SESSION['tipo_usuario'] === 'Usuario'): ?>
            <li class="nav-item">
                <a class="nav-link" href="index.php?action=homedash">
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </a>
            </li>
            <?php endif; ?>
            
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

            <?php if ($_SESSION['tipo_usuario'] === 'Administrador' || $_SESSION['tipo_usuario'] === 'Usuario'): ?>
            <li class="nav-item">
                <a class="nav-link" href="index.php?action=expedientes">
                    <i class="fas fa-folder"></i>
                    <span>Expedientes</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($_SESSION['tipo_usuario'] === 'Guardia'): ?>
            <li class="nav-item">
                <a class="nav-link active" href="index.php?action=registrar">
                    <i class="fas fa-file-alt"></i>
                    <span>Registrar</span>
                </a>
            </li>
            <?php endif; ?>

            <li class="nav-item mt-4">
                <a class="nav-link  active" href="index.php?action=config">
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

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h2 class="mb-0">Configuración</h2>
            <div class="user-info">
                <div class="user-avatar"><?php echo substr($_SESSION['nombre'], 0, 2); ?></div>
                <div>
                    <div class="fw-bold"><?php echo $_SESSION['nombre']; ?></div>
                    <div class="small text-muted"><?php echo $_SESSION['tipo_usuario']; ?></div>
                </div>
            </div>
        </div>

        <!-- Page Title -->
        <h3 class="page-title">Configuración del Sistema</h3>

        <!-- User Configuration Section -->
        <div class="config-section">
            <h4 class="mb-4"><i class="fas fa-user me-2"></i>Configuración de Usuario</h4>
            
            <div class="alert user-feedback" id="userFeedback"></div>
            
            <form id="userDataForm" method="POST">
                <input type="hidden" name="action" value="update_user">
                <input type="hidden" name="user_id" value="<?php echo $usuario_actual['id']; ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre" class="required-field">Nombre completo</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?php echo htmlspecialchars($usuario_actual['nombre']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="usuario" class="required-field">Nombre de usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" 
                                   value="<?php echo htmlspecialchars($usuario_actual['usuario']); ?>" required>
                            <small class="form-text text-muted">Este nombre será usado para iniciar sesión</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="required-field">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($usuario_actual['email']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="area">Área asignada</label>
                            <select class="form-control" id="area" name="area" disabled>
                                <option value="<?php echo $usuario_actual['area_id']; ?>" selected>
                                    <?php echo htmlspecialchars($usuario_actual['area_nombre'] ?? 'Sin área asignada'); ?>
                                </option>
                            </select>
                            <small class="form-text text-muted">El área no puede ser modificada desde aquí</small>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Guardar cambios
                </button>
            </form>
        </div>

        <!-- Password Change Section -->
        <div class="config-section">
            <h4 class="mb-4"><i class="fas fa-lock me-2"></i>Cambiar Contraseña</h4>
            
            <div class="alert password-feedback" id="passwordFeedback"></div>
            
            <form id="passwordForm" method="POST">
                <input type="hidden" name="action" value="update_password">
                <input type="hidden" name="user_id" value="<?php echo $usuario_actual['id']; ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="currentPassword" class="required-field">Contraseña actual</label>
                            <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="newPassword" class="required-field">Nueva contraseña</label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                            <small class="form-text text-muted">Mínimo 8 caracteres, incluir mayúsculas, minúsculas y números</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="confirmPassword" class="required-field">Confirmar nueva contraseña</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-key me-1"></i> Cambiar contraseña
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    $(document).ready(function() {

        const tipoUsuario = "<?php echo $_SESSION['tipo_usuario']; ?>";

            //if (tipoUsuario === 'admin') {
                // Validación del formulario de datos de usuario
                $('#userDataForm').on('submit', function(e) {
                    e.preventDefault();
                    
                    console.log('Enviando formulario de usuario...');
                    
                    const formData = {
                        nombre: $('#nombre').val(),
                        usuario: $('#usuario').val(),
                        email: $('#email').val()
                    };
                    
                    // Validaciones básicas de frontend
                    if (!formData.nombre || !formData.usuario || !formData.email) {
                        showUserFeedback('Por favor, complete todos los campos obligatorios.', 'danger');
                        return;
                    }
                    
                    if (!isValidEmail(formData.email)) {
                        showUserFeedback('Por favor, ingrese un correo electrónico válido.', 'danger');
                        return;
                    }

                    $.ajax({
                    url: 'index.php?action=config',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        console.log('Respuesta del servidor:', response);
                        
                        try {
                            const result = JSON.parse(response);
                            if (result.success) {
                                showUserFeedback(result.message, 'success');
                                // Actualizar información en la barra superior si el nombre cambió
                                if (formData.nombre !== '<?php echo $usuario_actual['nombre']; ?>') {
                                    setTimeout(() => {
                                        location.reload(); // Recargar para ver cambios
                                    }, 1500);
                                }
                            } else {
                                showUserFeedback(result.message, 'danger');
                            }
                        } catch (e) {
                            console.error('Error parsing JSON:', e);
                            console.error('Response received:', response);
                            showUserFeedback('Error al procesar la respuesta del servidor. Respuesta: ' + response.substring(0, 100), 'danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error AJAX:', error);
                        showUserFeedback('Error de conexión con el servidor: ' + error, 'danger');
                    }
                });

            });

            // Validación del formulario de contraseña
            $('#passwordForm').on('submit', function(e) {
                e.preventDefault();
                
                const currentPassword = $('#currentPassword').val();
                const newPassword = $('#newPassword').val();
                const confirmPassword = $('#confirmPassword').val();
                
                // Validaciones
                if (!currentPassword || !newPassword || !confirmPassword) {
                    showPasswordFeedback('Por favor, complete todos los campos.', 'danger');
                    return;
                }
                
                if (newPassword !== confirmPassword) {
                    showPasswordFeedback('Las contraseñas nuevas no coinciden.', 'danger');
                    return;
                }
                
                if (!isPasswordStrong(newPassword)) {
                    showPasswordFeedback('La nueva contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas y números.', 'danger');
                    return;
                }
                
                // Enviar formulario mediante AJAX
                $.ajax({
                    url: 'index.php?action=config',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.success) {
                                showPasswordFeedback(result.message, 'success');
                                $('#passwordForm')[0].reset();
                            } else {
                                showPasswordFeedback(result.message, 'danger');
                            }
                        } catch (e) {
                            showPasswordFeedback('Error al procesar la respuesta del servidor.', 'danger');
                        }
                    },
                    error: function() {
                        showPasswordFeedback('Error de conexión con el servidor.', 'danger');
                    }
                });
            });

            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            function isPasswordStrong(password) {
                // Mínimo 8 caracteres, al menos una mayúscula, una minúscula y un número
                const re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
                return re.test(password);
            }
            
            function showUserFeedback(message, type) {
                const feedback = $('#userFeedback');
                feedback.removeClass('alert-success alert-danger alert-warning');
                feedback.addClass(`alert-${type}`);
                feedback.html(message);
                feedback.show();
                
                // Ocultar después de 5 segundos
                setTimeout(() => feedback.hide(), 5000);
            }
            
            function showPasswordFeedback(message, type) {
                const feedback = $('#passwordFeedback');
                feedback.removeClass('alert-success alert-danger alert-warning');
                feedback.addClass(`alert-${type}`);
                feedback.html(message);
                feedback.show();
                
                // Ocultar después de 5 segundos
                setTimeout(() => feedback.hide(), 5000);
            }
        });

    </script>
</body>
</html>