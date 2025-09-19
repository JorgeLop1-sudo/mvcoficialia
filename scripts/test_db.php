<?php
require_once __DIR__ . '/../config/database.php';
$db = (new Database())->connect();
if ($db) {
    echo "Conexión OK. Usuarios en la BD: <br>";
    $r = $db->query("SELECT id, usuario, email, password FROM login LIMIT 5");
    while ($row = $r->fetch_assoc()) {
        echo "id:{$row['id']} usuario:{$row['usuario']} email:{$row['email']} pwd_len:" . strlen($row['password']) . "<br>";
    }
} else {
    echo "Error de conexión";
}
