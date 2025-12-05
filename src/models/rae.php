<?php

class RaeModel {
    private $conn; // PDO connection

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    /* LIST ALL RAEs */
    public function listar(): array {
        try {
            $sql = "SELECT * FROM raes ORDER BY id_rae DESC";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /* GET RAE BY ID */
    public function obtenerPorId(int $id): ?array {
        try {
            $sql = "SELECT * FROM raes WHERE id_rae = :id LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;

        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /* CREATE RAE */
    public function crear(
        string $codigo_rae,
        string $nombre_rae,
        string $descripcion,
        int $id_ficha,
        string $fecha_inicio,
        string $fecha_fin,
        string $estado
    ): bool {
        try {
            $sql = "INSERT INTO raes 
                    (codigo_rae, nombre_rae, descripcion, id_ficha, fecha_inicio, fecha_fin, estado)
                    VALUES (:codigo, :nombre, :descripcion, :ficha, :inicio, :fin, :estado)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':codigo', $codigo_rae);
            $stmt->bindParam(':nombre', $nombre_rae);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':ficha', $id_ficha, PDO::PARAM_INT);
            $stmt->bindParam(':inicio', $fecha_inicio);
            $stmt->bindParam(':fin', $fecha_fin);
            $stmt->bindParam(':estado', $estado);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al crear RAE: " . $e->getMessage());
            return false;
        }
    }

    /* UPDATE RAE */
    public function actualizar(
        int $id,
        string $codigo_rae,
        string $nombre_rae,
        string $descripcion,
        int $id_ficha,
        string $fecha_inicio,
        string $fecha_fin,
        string $estado
    ): bool {
        try {
            $sql = "UPDATE raes 
                    SET codigo_rae = :codigo,
                        nombre_rae = :nombre,
                        descripcion = :descripcion,
                        id_ficha = :ficha,
                        fecha_inicio = :inicio,
                        fecha_fin = :fin,
                        estado = :estado
                    WHERE id_rae = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':codigo', $codigo_rae);
            $stmt->bindParam(':nombre', $nombre_rae);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':ficha', $id_ficha, PDO::PARAM_INT);
            $stmt->bindParam(':inicio', $fecha_inicio);
            $stmt->bindParam(':fin', $fecha_fin);
            $stmt->bindParam(':estado', $estado);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al actualizar RAE: " . $e->getMessage());
            return false;
        }
    }

    /* DELETE RAE */
    public function eliminar(int $id): bool {
        try {
            $sql = "DELETE FROM raes WHERE id_rae = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar RAE: " . $e->getMessage());
            return false;
        }
    }

    /* CHANGE STATUS */
    public function cambiarEstado(int $id, string $estado): bool {
        try {
            $sql = "UPDATE raes SET estado = :estado WHERE id_rae = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al cambiar estado de RAE: " . $e->getMessage());
            return false;
        }
    }

    /*LIST RAEs BY FICHA */
    public function listarPorFicha(int $id_ficha): array {
        try {
            $sql = "SELECT * FROM raes WHERE id_ficha = :ficha ORDER BY fecha_inicio DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':ficha', $id_ficha, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
