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
    public function crear($nombre, $tipo_doc, $num_doc, $telefono, $cargo, $correo, $direccion, $password, $id_programa = null) {
        $stmt = $this->conn->prepare("
            INSERT INTO usuarios 
            (nombre_completo, tipo_documento, numero_documento, telefono, cargo, correo, direccion, password, id_programa)
            VALUES (:nombre, :tipo_doc, :num_doc, :telefono, :cargo, :correo, :direccion, :password, :id_programa)
        ");

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':tipo_doc', $tipo_doc);
        $stmt->bindParam(':num_doc', $num_doc);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':cargo', $cargo);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':password', $password);

        if ($id_programa === null) {
            $stmt->bindValue(':id_programa', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':id_programa', $id_programa, PDO::PARAM_INT);
        }

        return $stmt->execute();
    }

    public function actualizar($id_usuario, $nombre, $tipo_doc, $num_doc, $telefono, $cargo, $correo, $password, $direccion, $id_programa = null) {

        if ($password !== null && $password !== "") {
            $sql = "UPDATE usuarios SET 
                    nombre_completo = :nombre,
                    tipo_documento = :tipo_doc,
                    numero_documento = :num_doc,
                    telefono = :telefono,
                    cargo = :cargo,
                    correo = :correo,
                    password = :password,
                    direccion = :direccion,
                    id_programa = :programa
                WHERE id_usuario = :id_usuario";

            $stmt = $this->conn->prepare($sql);

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hash);

        } else {
            $sql = "UPDATE usuarios SET 
                    nombre_completo = :nombre,
                    tipo_documento = :tipo_doc,
                    numero_documento = :num_doc,
                    telefono = :telefono,
                    cargo = :cargo,
                    correo = :correo,
                    direccion = :direccion,
                    id_programa = :programa
                WHERE id_usuario = :id_usuario";
            $stmt = $this->conn->prepare($sql);
        }

        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':tipo_doc', $tipo_doc);
        $stmt->bindParam(':num_doc', $num_doc);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':cargo', $cargo);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':direccion', $direccion);

        // 👇 manejo correcto de programa (NULL o INT)
        if ($id_programa === null || $id_programa === '') {
            $stmt->bindValue(':programa', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':programa', (int)$id_programa, PDO::PARAM_INT);
        }

        return $stmt->execute();
    }

    // Function to delete a user
    public function eliminar($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute(); 
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
    
    // Function to get a user by their document number
    public function obtenerPorDocumento($documento) {
        $sql = "SELECT * FROM " . $this->table . " WHERE numero_documento = :documento";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':documento', $documento);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Function to handle user login
    public function login($correo, $password) {
        $user = $this->obtenerPorCorreo($correo);
        if ($user && password_verify($password, $user['password'])) {
            return $user; 
        }
        return false;
    }

    // ==========================================
    // 🔥 NUEVO: actualizarPerfil (para perfil propio)
    // NO toca cargo, estado ni id_programa
    // ==========================================
    public function actualizarPerfil(
        int $idUsuario,
        string $nombreCompleto,
        string $tipoDocumento,
        string $numeroDocumento,
        string $telefono,
        string $direccion,
        string $correo,
        ?string $rutaFotoPerfil = null
    ) {
        if ($rutaFotoPerfil !== null) {
            $sql = "UPDATE {$this->table}
                    SET nombre_completo = :nombre,
                        tipo_documento = :tipo_doc,
                        numero_documento = :num_doc,
                        telefono = :tel,
                        direccion = :dir,
                        correo = :correo,
                        foto_perfil = :foto
                    WHERE id_usuario = :id_usuario";
        } else {
            $sql = "UPDATE {$this->table}
                    SET nombre_completo = :nombre,
                        tipo_documento = :tipo_doc,
                        numero_documento = :num_doc,
                        telefono = :tel,
                        direccion = :dir,
                        correo = :correo
                    WHERE id_usuario = :id_usuario";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':nombre',     $nombreCompleto);
        $stmt->bindValue(':tipo_doc',   $tipoDocumento);
        $stmt->bindValue(':num_doc',    $numeroDocumento);
        $stmt->bindValue(':tel',        $telefono);
        $stmt->bindValue(':dir',        $direccion);
        $stmt->bindValue(':correo',     $correo);
        $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);

        if ($rutaFotoPerfil !== null) {
            $stmt->bindValue(':foto', $rutaFotoPerfil);
        }

        return $stmt->execute();
    }

    // =====================================================
    // 🔒 PASSWORD: obtener hash actual (PDO + id_usuario)
    // =====================================================
    public function obtenerHashPasswordPorId(int $idUsuario) {
        $sql = "SELECT password FROM {$this->table} WHERE id_usuario = :id_usuario LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['password'] ?? null;
    }

    // =====================================================
    // 🔒 PASSWORD: actualizar hash (PDO + id_usuario)
    // =====================================================
    public function actualizarPasswordPorId(int $idUsuario, string $nuevoHash): bool {
        $sql = "UPDATE {$this->table} SET password = :password WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':password', $nuevoHash, PDO::PARAM_STR);
        $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
