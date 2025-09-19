<?php
session_start();

// Headers para prevenir caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Verificar si el usuario está logueado y es usuario normal
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../inicio/index.php");
    exit();
}

// Validar que sea ADMIN
if ($_SESSION['tipo_usuario'] !== 'admin') {
    // Si no es admin, lo regresamos al login o a otra página
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

// Obtener datos del usuario actual
$usuario_actual = [];
$usuario_id = $_SESSION['id'] ?? 0;
$query_usuario = "SELECT l.*, a.nombre as area_nombre 
                  FROM login l 
                  LEFT JOIN areas a ON l.area_id = a.id 
                  WHERE l.id = $usuario_id";
$result_usuario = mysqli_query($conn, $query_usuario);

if ($result_usuario && mysqli_num_rows($result_usuario) > 0) {
    $usuario_actual = mysqli_fetch_assoc($result_usuario);
} else {
    // Si no se encuentra el usuario, redirigir al login
    header("Location: ../../inicio/index.php");
    exit();
}

// Procesar actualización de datos de usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_user') {
    $user_id = intval($_POST['user_id']);
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $usuario = mysqli_real_escape_string($conn, $_POST['usuario']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Validar que el usuario que intenta modificar es el mismo de la sesión o es admin
    if ($_SESSION['id'] != $user_id && $_SESSION['tipo_usuario'] != 'admin') {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos para modificar este usuario']);
        exit();
    }
    
    // Verificar si el nombre de usuario ya existe (excluyendo el usuario actual)
    $query_check_user = "SELECT id FROM login WHERE usuario = '$usuario' AND id != $user_id";
    $result_check_user = mysqli_query($conn, $query_check_user);
    
    if (mysqli_num_rows($result_check_user) > 0) {
        echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya está en uso']);
        exit();
    }
    
    // Verificar si el email ya existe (excluyendo el usuario actual)
    $query_check_email = "SELECT id FROM login WHERE email = '$email' AND id != $user_id";
    $result_check_email = mysqli_query($conn, $query_check_email);
    
    if (mysqli_num_rows($result_check_email) > 0) {
        echo json_encode(['success' => false, 'message' => 'El correo electrónico ya está en uso']);
        exit();
    }
    
    // Actualizar datos del usuario
    $query_update = "UPDATE login SET nombre = '$nombre', usuario = '$usuario', email = '$email' WHERE id = $user_id";
    $result_update = mysqli_query($conn, $query_update);
    
    if ($result_update) {
        // Actualizar datos en la sesión si es el usuario actual
        if ($_SESSION['id'] == $user_id) {
            $_SESSION['nombre'] = $nombre;
            $_SESSION['usuario'] = $usuario;
        }
        
        echo json_encode(['success' => true, 'message' => 'Datos actualizados correctamente']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar los datos: ' . mysqli_error($conn)]);
        exit();
    }
}

// Procesar actualización de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_password') {
    $user_id = intval($_POST['user_id']);
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    
    // Validar que el usuario que intenta modificar es el mismo de la sesión o es admin
    if ($_SESSION['id'] != $user_id && $_SESSION['tipo_usuario'] != 'admin') {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos para modificar este usuario']);
        exit();
    }
    
    // Obtener la contraseña actual del usuario
    $query_user = "SELECT password FROM login WHERE id = $user_id";
    $result_user = mysqli_query($conn, $query_user);
    
    if (!$result_user || mysqli_num_rows($result_user) == 0) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit();
    }
    
    $user_data = mysqli_fetch_assoc($result_user);
    
    // Verificar la contraseña actual
    if (!password_verify($currentPassword, $user_data['password'])) {
        echo json_encode(['success' => false, 'message' => 'La contraseña actual es incorrecta']);
        exit();
    }
    
    // Hashear la nueva contraseña
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Actualizar la contraseña
    $query_update = "UPDATE login SET password = '$hashedPassword' WHERE id = $user_id";
    $result_update = mysqli_query($conn, $query_update);
    
    if ($result_update) {
        echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la contraseña: ' . mysqli_error($conn)]);
        exit();
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
    <title>SIS-OP - Configuración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="..\..\css\dashboard\styleconfig.css">
    <link rel="stylesheet" href="..\..\css\dashboard\styledash.css">
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
                <a class="nav-link" href="expedientes.php">
                    <i class="fas fa-folder"></i>
                    <span>Expedientes</span>
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link active" href="config.php">
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
                                    <?php echo htmlspecialchars($usuario_actual['area_nombre']); ?>
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
            // Validación del formulario de datos de usuario
            $('#userDataForm').on('submit', function(e) {
                e.preventDefault();
                
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
                
                // Enviar formulario mediante AJAX
                $.ajax({
                    url: 'config.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.success) {
                                showUserFeedback(result.message, 'success');
                                // Actualizar información en la barra superior si el nombre cambió
                                if (formData.nombre !== '<?php echo $usuario_actual['nombre']; ?>') {
                                    location.reload(); // Recargar para ver cambios
                                }
                            } else {
                                showUserFeedback(result.message, 'danger');
                            }
                        } catch (e) {
                            showUserFeedback('Error al procesar la respuesta del servidor.', 'danger');
                        }
                    },
                    error: function() {
                        showUserFeedback('Error de conexión con el servidor.', 'danger');
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
                    url: 'config.php',
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
            
            // Funciones de utilidad
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