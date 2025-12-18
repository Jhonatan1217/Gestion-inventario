<?php

class ObraModel {

    private $conn;
    private $table = "actividades_formacion";

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    /* LISTAR */
    public function listar() {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* OBTENER POR ID */
    public function obtener($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_actividad = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* CREAR */
    public function crear($data) {
        $sql = "INSERT INTO {$this->table}
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
    }

    /* ACTUALIZAR */
    public function actualizar($data) {
        $sql = "UPDATE {$this->table}
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
    }

    /* CAMBIAR ESTADO */
    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE {$this->table} SET estado = ? WHERE id_actividad = ?";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([$estado, $id]);
    }
}
