<?php
require_once '../config/database.php';

$database = new Database();
$conn = $database->connect();

// Verificar usuarios guardia
$query = "SELECT * FROM login WHERE tipo_usuario = 'Guardia'";
$result = $conn->query($query);

echo "<h3>Usuarios con rol 'guardia':</h3>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - Usuario: " . $row['usuario'] . " - Nombre: " . $row['nombre'] . "<br>";
    }
} else {
    echo "No hay usuarios con rol 'Guardia'";
}

$conn->close();
?>