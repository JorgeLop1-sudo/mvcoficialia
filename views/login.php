<?php
// Headers adicionales para evitar cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficialía de Partes - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!--link rel="stylesheet" href="/mvc_oficialiapartes/css/caseta/styleindex.css"-->
    <style>
        :root {
            --primary-color: #151A2D;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
        }

        body {
            background: 
    radial-gradient(circle at 20% 80%, rgba(25, 24, 60, 0.2) 0%, transparent 65%),
    radial-gradient(circle at 80% 20%, rgba(70, 29, 60, 0.2) 0%, transparent 65%),
    linear-gradient(135deg, #0f1a35 0%, #160b25 100%);
    /*background: 
    radial-gradient(circle at 20% 80%, rgba(20, 19, 45, 0.15) 0%, transparent 70%),
    radial-gradient(circle at 80% 20%, rgba(50, 19, 45, 0.15) 0%, transparent 70%),
    linear-gradient(135deg, #0d152d 0%, #120820 100%);*/
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            margin: 0;
            padding: 20px;
        }

        /* Capa semitransparente para mejorar legibilidad */
        /*body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.85);
            z-index: -1;
        }*/

        .main-container {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header {
            background: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 0px 10px 0px;
            width: 100%;
            box-sizing: border-box;
        }

        .header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 28px;
        }

        .header p {
            margin: 10px 0 0;
            opacity: 0.8;
        }

        .nav-links {
            display: flex;
            justify-content: center;
            width: 100%;
            padding: 10px 0;
            border-radius: 0 0 10px 10px;
            margin-bottom: 20px;
        }

        .nav-link {
            margin: 0 15px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .nav-link:hover {
            background: var(--light-color);
            color: var(--secondary-color);
        }

        .login-container {
            background-color: var(--primary-color);
            color: white;
            border-radius: 15px 0px 15px 0px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: 100%;
        }

        .login-form {
            padding: 30px;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 2px solid #ddd;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn-login {
            background: var(--secondary-color);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        /* Estilos para el selector de tipo de login */
        .login-type {
            display: flex;
            background: var(--light-color);
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .login-option {
            flex: 1;
            text-align: center;
            padding: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-option.active {
            background: var(--secondary-color);
            color: white;
        }

        .login-option i {
            margin-right: 5px;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                align-items: center;
            }
            
            .nav-link {
                margin: 5px 0;
            }

            .login-type {
                flex-direction: column;
            }
        }
    </style>
    <!-- Meta tags para evitar cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>
<div class="main-container">
    <div class="header">
        <h1>Oficialía de Partes</h1>
        <p>Sistema de Gestión de Trámites y Oficios</p>
    </div>
    
    <div class="nav-links">
        <a href="index.php?action=login" class="nav-link"><i class="fas fa-user"></i> Iniciar Sesion</a>
        <a href="index.php?action=buscar" class="nav-link"><i class="fas fa-search"></i> Buscar Oficio</a>
    </div>

    <div class="login-container">
        <div class="login-form">
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
                    <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña" required>
                </div>
                
                <button type="submit" class="btn btn-login">Ingresar al Sistema</button>
                
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

    // Prevenir que se use la tecla F5 o Ctrl+R
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F5' || e.ctrlKey && e.key === 'r' || e.key === 'F11') {
            e.preventDefault();
            return false;
        }
    });

    // Forzar recarga desde el servidor al cargar la página
    window.onload = function() {
        if (performance.navigation.type === 2) {
            // La página fue cargada desde el cache (botón atrás)
            window.location.reload(true); // true fuerza recarga desde servidor
        }
    };
</script>
</body>
</html>