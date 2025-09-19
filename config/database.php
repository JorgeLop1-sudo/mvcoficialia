<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "oficialiap"; // <-- cambia si tu BD tiene otro nombre

    public function connect() {
        $conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        if ($conn->connect_error) {
            die("Error en la conexión: " . $conn->connect_error);
        }
        $conn->set_charset('utf8mb4');
        return $conn;
    }
}
