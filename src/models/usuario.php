<?php
class Usuario {
    private $conn;
    private $table = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Funcion para listar todos los usuarios
    public function listar() {
        $sql = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Función para obtener un usuario por su ID
public function obtenerPorId($id) {
    $sql = "SELECT * FROM " . $this->table . " WHERE id_usuario = :id_usuario";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    // Funcion para obtener un usuario por correo (para validar unicidad o login después)
    public function obtenerPorCorreo($correo) {
        $sql = "SELECT * FROM " . $this->table . " WHERE correo = :correo";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Funcion para crear un nuevo usuario
    public function crear($nombre, $tipo_doc, $num_doc, $telefono, $cargo, $correo, $direccion) {
        $sql = "INSERT INTO usuarios (nombre_completo, tipo_documento, numero_documento, telefono, cargo, correo, direccion, estado)
                VALUES (:nombre, :tipo_doc, :num_doc, :telefono, :cargo, :correo, :direccion, 'activo')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':tipo_doc', $tipo_doc);
        $stmt->bindParam(':num_doc', $num_doc);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':cargo', $cargo);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':direccion', $direccion);
    
    return $stmt->execute();
}

    // Funcion para actualizar un usuario existente
    public function actualizar($id_usuario, $nombre, $tipo_doc, $num_doc, $telefono, $cargo, $correo, $direccion) {
    $sql = "UPDATE usuarios SET 
                nombre_completo = :nombre,
                tipo_documento = :tipo_doc,
                numero_documento = :num_doc,
                telefono = :telefono,
                cargo = :cargo,
                correo = :correo,
                direccion = :direccion
            WHERE id_usuario = :id_usuario";

    $stmt = $this->conn->prepare($sql);

    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':tipo_doc', $tipo_doc);
    $stmt->bindParam(':num_doc', $num_doc);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':cargo', $cargo);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':direccion', $direccion);

    return $stmt->execute();
}

    // Funcion para eliminar un usuario
    public function eliminar($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    // Funcion para cambiar el estado de un usuario (activo/inactivo)

    public function cambiarEstado($id_usuario, $estado) {
        $sql = "UPDATE usuarios SET estado = :estado WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($sql);

        $estadoBD = $estado == 1 ? 'activo' : 'inactivo';

        $stmt->bindParam(':estado', $estadoBD);
        $stmt->bindParam(':id_usuario', $id_usuario);

        return $stmt->execute();
    }


}
?>
