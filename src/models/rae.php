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
    public function obtener(int $id): ?array {
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
        string $descripcion_rae,
        int $id_programa,
        string $estado
    ): bool {
        try {
            $sql = "INSERT INTO raes 
                    (codigo_rae, descripcion_rae, id_programa, estado)
                    VALUES (:codigo, :descripcion, :programa, :estado)";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(':codigo', $codigo_rae);
            $stmt->bindValue(':descripcion', $descripcion_rae);
            $stmt->bindValue(':programa', $id_programa, PDO::PARAM_INT);
            $stmt->bindValue(':estado', $estado);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al crear RAE: " . $e->getMessage());
            return false;
        }
    }

    /* UPDATE RAE */
    public function actualizar(int $id_rae, ?string $descripcion, ?string $estado): bool {
    $campos = [];
    $params = [];

    if ($descripcion !== null) {
        $campos[] = "descripcion_rae = ?";
        $params[] = $descripcion;
    }

    if ($estado !== null) {
        $campos[] = "estado = ?";
        $params[] = $estado;
    }

    // Agregar el ID
    $params[] = $id_rae;

    $sql = "UPDATE raes SET " . implode(", ", $campos) . " WHERE id_rae = ?";
    $stmt = $this->conn->prepare($sql);

    return $stmt->execute($params);
}


    /* CHANGE RAE STATE (Active/Inactive)*/
    public function cambiarEstado(int $id, string $estado): bool {
        try {
            $sql = "UPDATE raes SET estado = :estado WHERE id_rae = :id"; // Update only estado
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error cambiar estado bodega: " . $e->getMessage());
            return false;
        }
    }

}
