<?php
class Database {
    /*private $host = "sql303.infinityfree.com";
    private $user = "if0_40017275";
    private $pass = "j0rgel0pez123";
    private $dbname = "if0_40017275_oficialiap"; */// <-- cambia si tu BD tiene otro nombre

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
