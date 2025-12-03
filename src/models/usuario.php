<?php
class Usuario {
    private $conn;
    private $table = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    //Function to list all users
    public function listar() {
        $sql = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Function to get a user by their ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_usuario', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Function to get a user by their email (for uniqueness validation or login later)
    public function obtenerPorCorreo($correo) {
        $sql = "SELECT * FROM " . $this->table . " WHERE correo = :correo";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Function to create a new user
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

    // Function to update an existing user
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

    // Function to delete a user
    public function eliminar($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    // Function to change the status of a user (active/inactive)

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
