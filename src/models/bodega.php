<?php

/**
 * Modelo responsable de las operaciones sobre la tabla bodegas.
 */
class BodegaModel {

    /**
     * @var PDO
     */
    private $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    /* ============================================
       LISTAR BODEGAS
    ============================================ */
    public function getBodegas(?int $estado = null): array {
        if ($estado !== null) {
            $sql = "SELECT * FROM bodegas WHERE estado = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$estado]);
        } else {
            $sql = "SELECT * FROM bodegas";
            $stmt = $this->conn->query($sql);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================
       BUSCAR POR ID
    ============================================ */
    public function getBodegaById(int $id): ?array {
        $sql = "SELECT * FROM bodegas WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /* ============================================
       CREAR
    ============================================ */
    public function crearBodega(array $data): array {
        $sql = "INSERT INTO bodegas (nombre, ubicacion, descripcion, estado, fecha_creacion)
                VALUES (?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($sql);

        $ok = $stmt->execute([
            $data["nombre"],
            $data["ubicacion"],
            $data["descripcion"],
            $data["estado"]
        ]);

        return $ok
            ? ["status" => "ok", "msg" => "Bodega creada correctamente"]
            : ["status" => "error", "msg" => "Error al crear bodega"];
    }

    /* ============================================
       ACTUALIZAR
    ============================================ */
    public function actualizarBodega(array $data): array {
        $sql = "UPDATE bodegas 
                SET nombre = ?, ubicacion = ?, descripcion = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $ok = $stmt->execute([
            $data["nombre"],
            $data["ubicacion"],
            $data["descripcion"],
            $data["id"]
        ]);

        return $ok
            ? ["status" => "ok", "msg" => "Bodega actualizada correctamente"]
            : ["status" => "error", "msg" => "Error al actualizar"];
    }

    /* ============================================
       CAMBIAR ESTADO
    ============================================ */
    public function cambiarEstado(int $id, int $estado): array {
        $sql = "UPDATE bodegas SET estado = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        $ok = $stmt->execute([$estado, $id]);

        return $ok
            ? [
                "status" => "ok",
                "msg" => $estado === 1 ? "Bodega activada" : "Bodega inactivada"
            ]
            : ["status" => "error", "msg" => "Error al cambiar estado"];
    }
}

