<?php

class SolicitudMaterialModel {

    private $db;

    public function __construct(PDO $conn)
    {
        $this->db = $conn;
    }

    // Start transaction
    public function begin()
    {
        $this->db->beginTransaction();
    }

    // Commit transaction
    public function commit()
    {
        $this->db->commit();
    }

    // Rollback transaction
    public function rollback()
    {
        $this->db->rollBack();
    }

    // Create solicitud
    public function createSolicitudes($data)
    {
        $sql = "INSERT INTO solicitudes_material 
                (id_usuario_solicitante, id_ficha, id_actividad, id_rae, id_programa)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['id_usuario'],
            $data['id_ficha'],
            $data['id_actividad'],
            $data['id_rae'],
            $data['id_programa']
        ]);

        return $this->db->lastInsertId();
    }

    // Add details
    public function addDetalle($idSolicitud, $materiales)
    {
        $sql = "INSERT INTO solicitudes_detalle
                (id_solicitud, id_material, cantidad)
                VALUES (?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        foreach ($materiales as $mat) {
            $stmt->execute([
                $idSolicitud,
                $mat['id_material'],
                $mat['cantidad']
            ]);
        }
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM solicitudes_material WHERE id_solicitud = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

//borrar
