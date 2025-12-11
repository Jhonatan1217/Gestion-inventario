<?php
class Programa {
    private $conn;
    private $table = "programas_formacion";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Function to list all programs
    public function listar() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Function to list instructors by program
    public function listarInstructoresPorPrograma($id_programa) {
        $stmt = $this->conn->prepare("
            SELECT u.*
            FROM usuarios u
            WHERE u.cargo = 'Instructor'
            AND u.id_programa = :id_programa
        ");
        $stmt->bindParam(':id_programa', $id_programa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Function to get a program by its ID
    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id_programa = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Function to create a new program
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

    // Function to update an existing program
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
 
    // Function to delete a program
    public function eliminar($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id_programa = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Function to change the status of a program (active/inactive)
    public function cambiarEstado($id, $estado) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET estado = :estado WHERE id_programa = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();
    }

    // Function to count instructors
    public function contarInstructores() {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total_instructores
            FROM usuarios
            WHERE cargo = 'Instructor'
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Function to count instructors by program
    public function contarInstructoresPorPrograma($id_programa) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(DISTINCT u.id_usuario) AS instructores_por_programa
            FROM usuarios u
            INNER JOIN fichas_instructores fi ON fi.id_usuario = u.id_usuario
            INNER JOIN fichas f ON f.id_ficha = fi.id_ficha
            WHERE u.cargo = 'Instructor'
            AND f.id_programa = :id_programa
        ");
        $stmt->bindParam(':id_programa', $id_programa, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
?>
