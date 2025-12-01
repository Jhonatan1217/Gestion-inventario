<?php

class rae_model {

    private $conn; // PDO

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /* ==========================
       LISTAR RAES
    ========================== */
    public function listar() {

        $sql = "SELECT r.*, p.nombre_programa
                FROM raes r
                LEFT JOIN programas_formacion p ON p.id_programa = r.id_programa
                ORDER BY r.id_rae DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        // CORREGIDO → PDO usa fetchAll()
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==========================
       OBTENER POR ID
    ========================== */
    public function obtener($id) {

        $sql = "SELECT r.*, p.nombre_programa
                FROM raes r
                LEFT JOIN programas_formacion p ON p.id_programa = r.id_programa
                WHERE r.id_rae = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ==========================
       CREAR
    ========================== */
    public function crear($data) {

        $sql = "INSERT INTO raes (codigo_rae, descripcion_rae, id_programa, estado)
                VALUES (:codigo_rae, :descripcion_rae, :id_programa, 'Activo')";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':codigo_rae'     => $data['codigo_rae'],
            ':descripcion_rae' => $data['descripcion_rae'],
            ':id_programa'     => $data['id_programa']
        ]);
    }

    /* ==========================
       ACTUALIZAR
    ========================== */
    public function actualizar($data) {

        $sql = "UPDATE raes
                SET codigo_rae = :codigo_rae,
                    descripcion_rae = :descripcion_rae,
                    id_programa = :id_programa
                WHERE id_rae = :id_rae";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':codigo_rae'      => $data['codigo_rae'],
            ':descripcion_rae' => $data['descripcion_rae'],
            ':id_programa'     => $data['id_programa'],
            ':id_rae'          => $data['id_rae']
        ]);
    }

    /* ==========================
       ACTIVAR
    ========================== */
    public function activar($id) {
        $sql = "UPDATE raes SET estado = 'Activo' WHERE id_rae = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /* ==========================
       INACTIVAR
    ========================== */
    public function inactivar($id) {
        $sql = "UPDATE raes SET estado = 'Inactivo' WHERE id_rae = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
