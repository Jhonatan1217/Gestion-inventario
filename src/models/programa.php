<?php
class Programa {
    private $conn;
    private $table = "programas_formacion";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Funcion para listar todos los programas
    public function listar() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Funcion para obtener un programa por su ID
    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id_programa = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Funcion para crear un nuevo programa
    public function crear($codigo, $nombre, $nivel, $descripcion, $duracion, $estado) {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
            (codigo_programa, nombre_programa, nivel_programa, descripcion_programa, duracion_horas, estado)
            VALUES (:codigo, :nombre, :nivel, :descripcion, :duracion, :estado)
        ");
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':nivel', $nivel);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':duracion', $duracion, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();
    }

    // Funcion para actualizar un programa existente
    public function actualizar($id, $codigo, $nombre, $nivel, $descripcion, $duracion, $estado) {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table}
            SET codigo_programa = :codigo,
                nombre_programa = :nombre,
                nivel_programa = :nivel,
                descripcion_programa = :descripcion,
                duracion_horas = :duracion,
                estado = :estado
            WHERE id_programa = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':nivel', $nivel);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':duracion', $duracion, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();
    }
 
    // Funcion para eliminar un programa
    public function eliminar($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id_programa = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Funcion para cambiar el estado de un programa (activo/inactivo)
    public function cambiarEstado($id, $estado) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET estado = :estado WHERE id_programa = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();
    }
}
?>
