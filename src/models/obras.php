<?php

class ObraModel {

    private $conn;
    private $table = "actividades_formacion";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /* List all works */
    public function listar() {
        try {
            $sql = "SELECT * FROM " . $this->table;
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    /* Get work by ID */
    public function obtener($id) {
        try {
            $sql = "SELECT * FROM " . $this->table . " WHERE id_actividad = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }

    /* Create work */
    public function crear($data) {
        try {
            $sql = "INSERT INTO " . $this->table . "
                (id_ficha, id_rae, id_instructor, nombre_actividad, descripcion,
                 tipo_trabajo, fecha_inicio, fecha_fin, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                $data["id_ficha"],
                $data["id_rae"],
                $data["id_instructor"],
                $data["nombre_actividad"],
                $data["descripcion"] ?? null,
                $data["tipo_trabajo"],
                $data["fecha_inicio"] ?? null,
                $data["fecha_fin"] ?? null,
                $data["estado"] ?? "Activa"
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /* Update work */
    public function actualizar($data) {
        try {
            $sql = "UPDATE " . $this->table . "
                SET id_ficha = ?,
                    id_rae = ?,
                    id_instructor = ?,
                    nombre_actividad = ?,
                    descripcion = ?,
                    tipo_trabajo = ?,
                    fecha_inicio = ?,
                    fecha_fin = ?,
                    estado = ?
                WHERE id_actividad = ?";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                $data["id_ficha"],
                $data["id_rae"],
                $data["id_instructor"],
                $data["nombre_actividad"],
                $data["descripcion"],
                $data["tipo_trabajo"],
                $data["fecha_inicio"],
                $data["fecha_fin"],
                $data["estado"],
                $data["id_actividad"]
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /* Change state */
    public function cambiarEstado($id, $estado) {
        try {
            $sql = "UPDATE " . $this->table . " SET estado = ? WHERE id_actividad = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$estado, $id]);
        } catch (Exception $e) {
            return false;
        }
    }
}
