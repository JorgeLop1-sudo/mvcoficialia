<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficialía de Partes - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="/mvc_oficialiapartes/css/globals/style-dark.css" id="theme-style">

    <!-- Meta tags para evitar cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>

<body>
<div id="themeLoading" class="theme-loading"></div>
<!-- Botón para cambiar tema -->
<button class="theme-toggle" id="themeToggle" title="Cambiar tema">
    <i class="fas fa-moon"></i>
</button>

<div class="main-container">

    <?php include 'partials/titulo.php'; ?>

    <div class="content">
        <div class="form">
            <h3 class="text-center mb-4">Inicio de Sesión</h3>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
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
                    <div class="password-container">
                        <input type="password" class="form-control" id="password" data-toggle="password" name="password" placeholder="Ingrese su contraseña" required>
                        <button type="button" class="toggle-password" data-target="password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn">Ingresar</button>
                
                <div class="text-center mt-3">
                    <a href="recovery/forgot-password.php" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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

    // Prevenir navegación hacia atrás/adelante de manera más robusta
    window.history.pushState(null, null, window.location.href);
    window.onpopstate = function(event) {
        window.history.go(1);
    };

    // Forzar recarga desde el servidor al cargar la página
    window.onload = function() {
        if (performance.navigation.type === 2) {
            // La página fue cargada desde el cache (botón atrás)
            window.location.reload(true); // true fuerza recarga desde servidor
        }
    };

</script>

<script src="../mvc_oficialiapartes/scripts/dark_theme_inicio.js"></script>
<script src="../mvc_oficialiapartes/scripts/vercontra.js"></script>

</body>
</html>