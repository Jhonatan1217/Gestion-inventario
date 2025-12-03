<?php

class MaterialFormacionModel {

    private $db;

    public function __construct(PDO $conn)
    {
        $this->db = $conn;
    }

    
    //   GET ALL MATERIALS
       
    public function getAll()
    {
        $sql = "SELECT * FROM material_formacion ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    //   GET MATERIAL BY ID
       
    public function getById($id_material)
    {
        $sql = "SELECT * FROM material_formacion WHERE id_material = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_material]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    //  CREATE MATERIAL
       
    public function create($data)
    {
        // Convert empty string to NULL
        $codigo = ($data['codigo_inventario'] === "" ? null : $data['codigo_inventario']);

        // If Inventariado → inventory code required
        if ($data['clasificacion'] === "Inventariado" && $codigo === null) {
            return false;
        }

        $sql = "INSERT INTO material_formacion 
                (nombre, descripcion, unidad_medida, clasificacion, codigo_inventario, estado)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['nombre'],
            $data['descripcion'],
            $data['unidad_medida'],
            $data['clasificacion'],
            $codigo,
            $data['estado']
        ]);
    }

    
    //   UPDATE MATERIAL
       
    public function update($id_material, $data)
    {
        // Convert empty string to NULL
        $codigo = ($data['codigo_inventario'] === "" ? null : $data['codigo_inventario']);

        // If Inventariado → inventory code required
        if ($data['clasificacion'] === "Inventariado" && $codigo === null) {
            return false;
        }

        $sql = "UPDATE material_formacion
                SET nombre = ?, descripcion = ?, unidad_medida = ?, 
                    clasificacion = ?, codigo_inventario = ?, estado = ?
                WHERE id_material = ?";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['nombre'],
            $data['descripcion'],
            $data['unidad_medida'],
            $data['clasificacion'],
            $codigo,
            $data['estado'],
            $id_material
        ]);
    }

    
    //    DELETE MATERIAL (check all relations)
       
    public function delete($id_material)
    {
        // Tables that use id_material
        $tables = [
            "movimientos_material",
            "devoluciones_material",
            "stock_bodega",
            "stock_subbodega",
            "solicitudes_material"
        ];

        // Check relations one by one
        foreach ($tables as $table) {

            $sql = "SELECT COUNT(*) AS total FROM $table WHERE id_material = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_material]);

            $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            if ($count > 0) {
                return false; // cannot delete, material in use
            }
        }

        // Delete material
        $sql = "DELETE FROM material_formacion WHERE id_material = ?";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([$id_material]);
    }

    
    //   GET TOTAL STOCK (bodega + subbodega)
       
    public function getStockTotal($id_material)
    {
        $sql = "
            SELECT 
                (SELECT IFNULL(SUM(stock_actual),0) 
                 FROM stock_bodega 
                 WHERE id_material = ?) AS stock_bodega,

                (SELECT IFNULL(SUM(stock_actual),0) 
                 FROM stock_subbodega 
                 WHERE id_material = ?) AS stock_subbodega
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_material, $id_material]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    //   SEARCH MATERIAL BY NAME OR CODE
       
    public function search($term)
    {
        $like = "%".$term."%";

        $sql = "SELECT *
                FROM material_formacion
                WHERE nombre LIKE ? OR codigo_inventario LIKE ?
                ORDER BY nombre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$like, $like]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
