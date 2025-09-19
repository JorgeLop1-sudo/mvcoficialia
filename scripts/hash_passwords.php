<?php
require_once __DIR__ . '/../config/database.php';
$db = (new Database())->connect();

$res = $db->query("SELECT id, password FROM login");
while ($row = $res->fetch_assoc()) {
    $pwd = $row['password'];
    if (!(is_string($pwd) && strlen($pwd) === 60 && strpos($pwd, '$2') === 0)) {
        $hash = password_hash($pwd, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE login SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hash, $row['id']);
        $stmt->execute();
    }
}
echo "Migración completada. Revisa la BD.";
