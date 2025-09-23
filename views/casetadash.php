<?php
session_start();

// Headers para prevenir caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Conexión a la base de datos
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "oficialiap";

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$conn) {
    die("No hay conexión: " . mysqli_connect_error());
}

// Procesar formulario de registro de oficio
$mensaje = "";
$tipoMensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar datos del formulario
    $remitente = mysqli_real_escape_string($conn, $_POST['remitente']);
    $tipo_persona = mysqli_real_escape_string($conn, $_POST['tipoPersona']);
    $tipo_documento = mysqli_real_escape_string($conn, $_POST['tipoDocumento']);
    $numero_documento = isset($_POST['numeroDocumento']) ? mysqli_real_escape_string($conn, $_POST['numeroDocumento']) : null;
    $folios = mysqli_real_escape_string($conn, $_POST['folios']);
    $correo = mysqli_real_escape_string($conn, $_POST['correo']);
    $telefono = mysqli_real_escape_string($conn, $_POST['telefono']);
    $asunto = mysqli_real_escape_string($conn, $_POST['asunto']);
    
    // Valores fijos como solicitaste
    $area_id = 1; // Valor fijo para area_id
    $usuario_id = 1; // Valor fijo para usuario_id
    
    // Verificar si el área existe
    $query_area_check = mysqli_query($conn, "SELECT id FROM areas WHERE id = '$area_id'");
    if (mysqli_num_rows($query_area_check) === 0) {
        $mensaje = "Error: El área seleccionada no es válida";
        $tipoMensaje = "error";
    } else {
        // Procesar archivo subido
        $archivo_nombre = null;
        $archivo_ruta = null;
        $archivo_ruta2 = null;
        
        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $directorio = "../uploads/";
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }
            
            $archivo_nombre = basename($_FILES['archivo']['name']);
            $archivo_temporal = $_FILES['archivo']['tmp_name'];
            $archivo_ruta = $directorio . time() . '_' . $archivo_nombre;
            $archivo_ruta2 = '../../'.$directorio . time() . '_' . $archivo_nombre;
            
            if (!move_uploaded_file($archivo_temporal, $archivo_ruta)) {
                $mensaje = "Error al subir el archivo.";
                $tipoMensaje = "error";
            }
        }
        
        // Si no hay error hasta ahora, insertar en la base de datos
        if (empty($mensaje)) {
            $insert_query = "INSERT INTO oficios (remitente, tipo_persona, tipo_documento, numero_documento, folios, correo, telefono, asunto, archivo_nombre, archivo_ruta, area_id, usuario_id) 
                            VALUES ('$remitente', '$tipo_persona', '$tipo_documento', '$numero_documento', '$folios', '$correo', '$telefono', '$asunto', '$archivo_nombre', '$archivo_ruta2', '$area_id', '$usuario_id')";
            
            if (mysqli_query($conn, $insert_query)) {
                $mensaje = "Oficio registrado correctamente.";
                $tipoMensaje = "success";
                
                // Limpiar el formulario después de un registro exitoso
                echo '<script>document.getElementById("registerForm").reset();</script>';
            } else {
                $mensaje = "Error al registrar el oficio: " . mysqli_error($conn);
                $tipoMensaje = "error";
            }
        }
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
    <title>Oficialia de Partes - Registrar Oficio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="../css/dashboard/styledash.css">
    <style>
        .form-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #3498db;
        }
        
        .form-section-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
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
            border-color: #3498db;
            background-color: #f8f9fa;
        }
        
        .file-upload i {
            font-size: 40px;
            color: #3498db;
            margin-bottom: 10px;
        }
        
        .file-name {
            margin-top: 10px;
            font-style: italic;
            color: #777;
        }
        
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
            background: #27ae60;
        }
        
        .btn-register:hover {
            background: #219653;
            transform: translateY(-2px);
        }
        
        .btn-cancel {
            background: #e74c3c;
        }
        
        .btn-cancel:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .radio-group {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn-action {
                width: 100%;
                margin-bottom: 10px;
            }
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
                <a class="nav-link active" href="index.php?action=casetadash">
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

    <div class="main-content">
        <div class="header">
            <h2 class="mb-0">Registrar Oficio</h2>
            <div class="user-info">
                <div class="user-avatar"><?php echo isset($_SESSION['nombre']) ? substr($_SESSION['nombre'], 0, 2) : 'OP'; ?></div>
                <div>
                    <div class="fw-bold"><?php echo isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Oficialía de Partes'; ?></div>
                    <div class="small text-muted"><?php echo isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'Caseta'; ?></div>
                </div>
            </div>
        </div>

        <div class="form-container">
            <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipoMensaje == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>