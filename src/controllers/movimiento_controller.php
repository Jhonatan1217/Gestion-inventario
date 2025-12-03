<?php
// MovimientoModel: Handles CRUD operations for the "movimientos_material" table.
class MovimientoModel {
    private $conn; // PDO connection

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    // List all movements (newest first)
    public function listar() {
        try {
            $sql = "SELECT * FROM movimientos_material ORDER BY fecha_hora DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Get movement by ID
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT * FROM movimientos_material WHERE id_movimiento = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Create a new movement
    public function crear($tipo_movimiento, $fecha_hora, $id_usuario, $id_material,
                         $id_bodega, $id_subbodega, $cantidad, $id_programa,
                         $id_ficha, $id_rae, $observaciones, $id_solicitud) {
        try {
            $sql = "INSERT INTO movimientos_material 
                    (tipo_movimiento, fecha_hora, id_usuario, id_material, id_bodega,
                     id_subbodega, cantidad, id_programa, id_ficha, id_rae,
                     observaciones, id_solicitud)
                    VALUES
                    (:tipo, :fecha, :usuario, :material, :bodega, :subbodega,
                     :cantidad, :programa, :ficha, :rae, :obs, :solicitud)";
            
            $stmt = $this->conn->prepare($sql);

            // Bind params
            $stmt->bindParam(':tipo', $tipo_movimiento);
            $stmt->bindParam(':fecha', $fecha_hora);
            $stmt->bindParam(':usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':material', $id_material, PDO::PARAM_INT);
            $stmt->bindParam(':bodega', $id_bodega, PDO::PARAM_INT);
            $stmt->bindParam(':subbodega', $id_subbodega, PDO::PARAM_INT);
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt->bindParam(':programa', $id_programa, PDO::PARAM_INT);
            $stmt->bindParam(':ficha', $id_ficha, PDO::PARAM_INT);
            $stmt->bindParam(':rae', $id_rae, PDO::PARAM_INT);
            $stmt->bindParam(':obs', $observaciones);
            $stmt->bindParam(':solicitud', $id_solicitud, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error creating movement: " . $e->getMessage());
            return false;
        }
    }

    // Update movement by ID
    public function actualizar($id, $tipo_movimiento, $fecha_hora, $id_usuario, $id_material,
                              $id_bodega, $id_subbodega, $cantidad, $id_programa,
                              $id_ficha, $id_rae, $observaciones, $id_solicitud) {
        try {
            $sql = "UPDATE movimientos_material SET
                    tipo_movimiento = :tipo,
                    fecha_hora = :fecha,
                    id_usuario = :usuario,
                    id_material = :material,
                    id_bodega = :bodega,
                    id_subbodega = :subbodega,
                    cantidad = :cantidad,
                    id_programa = :programa,
                    id_ficha = :ficha,
                    id_rae = :rae,
                    observaciones = :obs,
                    id_solicitud = :solicitud
                    WHERE id_movimiento = :id";

            $stmt = $this->conn->prepare($sql);

            // Bind params
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':tipo', $tipo_movimiento);
            $stmt->bindParam(':fecha', $fecha_hora);
            $stmt->bindParam(':usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':material', $id_material, PDO::PARAM_INT);
            $stmt->bindParam(':bodega', $id_bodega, PDO::PARAM_INT);
            $stmt->bindParam(':subbodega', $id_subbodega, PDO::PARAM_INT);
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt->bindParam(':programa', $id_programa, PDO::PARAM_INT);
            $stmt->bindParam(':ficha', $id_ficha, PDO::PARAM_INT);
            $stmt->bindParam(':rae', $id_rae, PDO::PARAM_INT);
            $stmt->bindParam(':obs', $observaciones);
            $stmt->bindParam(':solicitud', $id_solicitud, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating movement: " . $e->getMessage());
            return false;
        }
    }

    // Delete movement by ID
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM movimientos_material WHERE id_movimiento = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting movement: " . $e->getMessage());
            return false;
        }
    }
}
