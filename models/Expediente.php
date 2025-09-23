<?php
class Expediente {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerTodos($filtros = [], $usuario_id = null, $tipo_usuario = null) {
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

        // Filtrar por usuario si no es admin
        if ($tipo_usuario !== 'admin' && $usuario_id) {
            $usuario_id = mysqli_real_escape_string($this->conn, $usuario_id);
            $query .= " AND (o.usuario_derivado_id = '$usuario_id' OR o.usuario_id = '$usuario_id')";
        }

        // Aplicar filtros existentes
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

    // Método para obtener el historial de derivaciones
    public function obtenerHistorialDerivaciones($oficio_id) {
        $historial = [];
        $oficio_id = mysqli_real_escape_string($this->conn, $oficio_id);
        
        $query = "
            SELECT 
                o.id as oficio_id,
                o.remitente,
                o.asunto,
                o.fecha_registro,
                a_origen.nombre as area_origen,
                u_origen.nombre as usuario_origen,
                a_derivada.nombre as area_derivada,
                u_derivado.nombre as usuario_derivado,
                o.respuesta,
                o.estado,
                o.fecha_derivacion,
                o.fecha_respuesta
            FROM oficios o
            LEFT JOIN areas a_origen ON o.area_id = a_origen.id
            LEFT JOIN login u_origen ON o.usuario_id = u_origen.id
            LEFT JOIN areas a_derivada ON o.area_derivada_id = a_derivada.id
            LEFT JOIN login u_derivado ON o.usuario_derivado_id = u_derivado.id
            WHERE o.id = '$oficio_id'
            ORDER BY o.fecha_derivacion ASC, o.fecha_registro ASC
        ";
        
        $result = mysqli_query($this->conn, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $historial[] = $row;
            }
        }
        
        return $historial;
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

    public function actualizarRespuesta($id, $respuesta, $estado) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $respuesta = mysqli_real_escape_string($this->conn, $respuesta);
        $estado = mysqli_real_escape_string($this->conn, $estado);
        
        $update_query = "UPDATE oficios SET 
                        respuesta = '$respuesta',
                        estado = '$estado',
                        fecha_respuesta = NOW()
                        WHERE id = '$id'";
        
        if (mysqli_query($this->conn, $update_query)) {
            return [
                'success' => true,
                'mensaje' => "Oficio " . ($estado == 'completado' ? 'completado' : 'denegado') . " correctamente"
            ];
        } else {
            return [
                'success' => false,
                'mensaje' => "Error al actualizar el oficio: " . mysqli_error($this->conn)
            ];
        }
    }
    
}
?>