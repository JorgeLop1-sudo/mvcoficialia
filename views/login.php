<?php
// Inicializar variables para evitar advertencias
$identificador = "";
$pass = "";
$error = "";
$login_type = "users";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficialía de Partes - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/mvc_login/css/inicio/styleindex.css">
</head>
<body>
<div>
    <div class="header">
        <h1>Oficialía de Partes</h1>
        <p>Sistema de Gestión de Trámites y Oficios</p>
    </div>
    
    <div class="nav-links">
        <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Inicio</a>
        <a href="registrar.php" class="nav-link"><i class="fas fa-file-alt"></i> Registrar Oficio</a>
        <a href="buscar.php" class="nav-link"><i class="fas fa-search"></i> Buscar Oficio</a>
    </div>

    <div class="login-container">
        <div class="login-form">
            <h3 class="text-center mb-4">Inicio de Sesión</h3>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <!-- Selector de tipo de login -->
            <div class="login-type">
                <div class="login-option <?php echo $login_type === 'users' ? 'active' : ''; ?>" id="option-user" data-type="users">
                    <i class="fas fa-user"></i> Usuario
                </div>
                <div class="login-option <?php echo $login_type === 'email' ? 'active' : ''; ?>" id="option-email" data-type="email">
                    <i class="fas fa-envelope"></i> Correo
                </div>
            </div>
            
            <form id="loginForm" action="index.php?action=login" method="POST">
                <!-- Campo oculto para enviar el tipo de login seleccionado -->
                <input type="hidden" id="login_type" name="login_type" value="<?php echo $login_type; ?>">
                
                <div class="mb-3">
                    <label for="identificador" class="form-label" id="label-identificador">
                        <?php echo $login_type === 'email' ? 'Correo electrónico' : 'Usuario'; ?>
                    </label>
                    <input type="text" class="form-control" id="identificador" name="identificador" 
                           placeholder="<?php echo $login_type === 'email' ? 'Ingrese su correo electrónico' : 'Ingrese su usuario'; ?>" 
                           value="<?php echo htmlspecialchars($identificador); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña" required>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Recordar mis datos</label>
                </div>
                
                <button type="submit" class="btn btn-login">Ingresar al Sistema</button>
                
                <div class="text-center mt-3">
                    <a href="recovery/forgot-password.php" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Cambio entre inicio de sesión con usuario o correo
    const optionUser = document.getElementById('option-user');
    const optionEmail = document.getElementById('option-email');
    const labelIdentificador = document.getElementById('label-identificador');
    const inputIdentificador = document.getElementById('identificador');
    const hiddenLoginType = document.getElementById('login_type');
    
    optionUser.addEventListener('click', () => {
        optionUser.classList.add('active');
        optionEmail.classList.remove('active');
        labelIdentificador.textContent = 'Usuario';
        inputIdentificador.placeholder = 'Ingrese su usuario';
        hiddenLoginType.value = 'users';
    });
    
    optionEmail.addEventListener('click', () => {
        optionEmail.classList.add('active');
        optionUser.classList.remove('active');
        labelIdentificador.textContent = 'Correo electrónico';
        inputIdentificador.placeholder = 'Ingrese su dirección de correo';
        hiddenLoginType.value = 'email';
    });
    
    // Validación básica del formulario
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const identificador = document.getElementById('identificador').value;
        const password = document.getElementById('password').value;
        const loginType = document.getElementById('login_type').value;
        
        if (!identificador.trim()) {
            e.preventDefault();
            alert('Por favor, ingresa tu ' + (loginType === 'email' ? 'correo electrónico' : 'usuario'));
            return false;
        }
        
        if (!password.trim()) {
            e.preventDefault();
            alert('Por favor, ingresa tu contraseña');
            return false;
        }
        
        // Si está seleccionado el modo email, validar formato de email
        if (loginType === 'email') {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(identificador)) {
                e.preventDefault();
                alert('Por favor, ingresa una dirección de correo electrónico válida');
                return false;
            }
        }
    });
</script>
</body>
</html>