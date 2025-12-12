<?php

class FichaModel {

    private $conn;
    private $table = "fichas";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /* ============================
       LISTAR TODAS LAS FICHAS
    ============================ */
    public function listar() {
        try {
            $sql = "SELECT * FROM " . $this->table;
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /* ============================
       OBTENER FICHA POR ID
    ============================ */
    public function obtener($id) {
        try {
            $sql = "SELECT * FROM " . $this->table . " WHERE id_ficha = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /* ============================
       CREAR FICHA
    ============================ */
    public function crear($data) {
        try {
            $sql = "INSERT INTO " . $this->table . "
                (numero_ficha, id_programa, jornada, modalidad, fecha_inicio, fecha_fin, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                $data['numero_ficha'],
                $data['id_programa'],
                $data['jornada'],
                $data['modalidad'],
                $data['fecha_inicio'],
                $data['fecha_fin'],
                isset($data['estado']) ? $data['estado'] : "Activa"
            ]);

        } catch (Exception $e) {
            return false;
        }
    }

    /* ============================
       ACTUALIZAR FICHA
    ============================ */
    public function actualizar($data) {
        try {
            $sql = "UPDATE " . $this->table . "
                SET numero_ficha = ?, 
                    id_programa = ?, 
                    jornada = ?, 
                    modalidad = ?, 
                    fecha_inicio = ?, 
                    fecha_fin = ?, 
                    estado = ?
                WHERE id_ficha = ?";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                $data['numero_ficha'],
                $data['id_programa'],
                $data['jornada'],
                $data['modalidad'],
                $data['fecha_inicio'],
                $data['fecha_fin'],
                $data['estado'],
                $data['id_ficha']
            ]);

        } catch (Exception $e) {
            return false;
        }
    }

    /* ============================
       ACTIVAR FICHA
    ============================ */
    public function activar($id) {
        try {
            $sql = "UPDATE " . $this->table . " SET estado = 'Activa' WHERE id_ficha = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$id]);

        } catch (Exception $e) {
            return false;
        }
    }

    /* ============================
       INACTIVAR FICHA
    ============================ */
    public function inactivar($id) {
        try {
            $sql = "UPDATE " . $this->table . " SET estado = 'Inactiva' WHERE id_ficha = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$id]);

        } catch (Exception $e) {
            return false;
        }
    }
}
