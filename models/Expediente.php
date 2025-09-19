<?php
class Expediente {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerTodos($filtros = []) {
        $expedientes = [];
        
        $query = "
            SELECT o.*, a.nombre as area_nombre, l.nombre as usuario_nombre,
                   ad.nombre as area_derivada_nombre, ud.nombre as usuario_derivado_nombre
            FROM oficios o 
            LEFT JOIN areas a ON o.area_id = a.id 
            LEFT JOIN login l ON o.usuario_id = l.id
            LEFT JOIN areas ad ON o.area_derivada_id = ad.id
            LEFT JOIN login ud ON o.usuario_derivado_id = ud.id
            WHERE 1=1
        ";

        // Aplicar filtros
        if (!empty($filtros['numero'])) {
            $numero = mysqli_real_escape_string($this->conn, $filtros['numero']);
            $query .= " AND o.numero_documento LIKE '%$numero%'";
        }

        if (!empty($filtros['estado'])) {
            $estado = mysqli_real_escape_string($this->conn, $filtros['estado']);
            $query .= " AND o.estado = '$estado'";
        }

        $query .= " ORDER BY o.fecha_registro DESC";

        $result = mysqli_query($this->conn, $query);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $expedientes[] = $row;
            }
        }
        
        return $expedientes;
    }

    public function obtenerPorId($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "SELECT * FROM oficios WHERE id = '$id'";
        $result = mysqli_query($this->conn, $query);
        return $result && mysqli_num_rows($result) > 0 ? mysqli_fetch_assoc($result) : null;
    }

    public function eliminar($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        
        // Obtener información del archivo
        $archivo_info = $this->obtenerPorId($id);
        $archivo_ruta = $archivo_info['archivo_ruta'] ?? '';
        
        // Eliminar archivo físico si existe
        if (!empty($archivo_ruta) && file_exists($archivo_ruta)) {
            unlink($archivo_ruta);
        }
        
        // Eliminar registro de la base de datos
        $delete_query = "DELETE FROM oficios WHERE id = '$id'";
        return mysqli_query($this->conn, $delete_query) 
            ? "Oficio eliminado correctamente" 
            : "Error al eliminar el oficio: " . mysqli_error($this->conn);
    }

    public function derivar($id, $area_derivada, $usuario_derivado, $respuesta) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $area_derivada = mysqli_real_escape_string($this->conn, $area_derivada);
        $usuario_derivado = mysqli_real_escape_string($this->conn, $usuario_derivado);
        $respuesta = mysqli_real_escape_string($this->conn, $respuesta);

        $update_query = "UPDATE oficios SET 
                        area_derivada_id = '$area_derivada',
                        usuario_derivado_id = '$usuario_derivado',
                        respuesta = '$respuesta',
                        estado = 'tramite',
                        fecha_derivacion = NOW()
                        WHERE id = '$id'";
        
        return mysqli_query($this->conn, $update_query) 
            ? "Oficio derivado correctamente" 
            : "Error al derivar el oficio: " . mysqli_error($this->conn);
    }

    public function obtenerUsuariosPorArea($area_id) {
        $usuarios = [];
        $area_id = mysqli_real_escape_string($this->conn, $area_id);
        
        if ($area_id > 0) {
            $query = "SELECT id, nombre, usuario FROM login WHERE area_id = $area_id ORDER BY nombre";
            $result = mysqli_query($this->conn, $query);
            
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $usuarios[] = $row;
                }
            }
        }
        
        return $usuarios;
    }
}
?>