<?php

class FichaModel {

    private $conn;
    private $table = "fichas";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /*List all FICHAS*/
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

    /*Get FICHA for ID*/
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

    /*Create FICHA*/
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

    /*Update FICHA*/
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

    public function cambiarEstado($id, $estado) {
        try {
            $sql = "UPDATE " . $this->table . " SET estado = ? WHERE id_ficha = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$estado, $id]);

        } catch (Exception $e) {
            return false;
        }
    }

}
