<?php

class Usuario {
    private $conn;
    private $table = "usuarios";

    public function __construct($db) {
        $this->conn = $db;
    }

    // MÉTODO COMPATIBLE CON PARÁMETROS INDIVIDUALES
    public function crear(
        $nombre,
        $tipo_documento,
        $numero_documento,
        $telefono,
        $cargo,
        $correo,
        $direccion,
        $password,
        $token, // Este parámetro no se usa aquí
        $id_programa = null
    ) {
        // Convertir a array y llamar al nuevo método
        return $this->crearConArray([
            'nombre' => $nombre,
            'tipo_documento' => $tipo_documento,
            'numero_documento' => $numero_documento,
            'telefono' => $telefono,
            'cargo' => $cargo,
            'correo' => $correo,
            'direccion' => $direccion,
            'password' => $password,
            'id_programa' => $id_programa
        ]);
    }

    // NUEVO MÉTODO CON ARRAY
    public function crearConArray($data) {
        try {
            $sql = "INSERT INTO usuarios (
                        nombre_completo,
                        tipo_documento,
                        numero_documento,
                        telefono,
                        cargo,
                        correo,
                        direccion,
                        password,
                        fecha_creacion,
                        estado,
                        id_programa,
                        correo_verificado
                    ) VALUES (
                        :nombre_completo,
                        :tipo_documento,
                        :numero_documento,
                        :telefono,
                        :cargo,
                        :correo,
                        :direccion,
                        :password,
                        NOW(),
                        'inactivo',
                        :id_programa,
                        0
                    )";
            
            $stmt = $this->conn->prepare($sql);
            
            // Mapear nombres de campos
            $params = [
                ':nombre_completo' => $data['nombre'],
                ':tipo_documento' => $data['tipo_documento'],
                ':numero_documento' => $data['numero_documento'],
                ':telefono' => $data['telefono'],
                ':cargo' => $data['cargo'],
                ':correo' => $data['correo'],
                ':direccion' => $data['direccion'],
                ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
                ':id_programa' => $data['id_programa'] ?? null
            ];
            
            $stmt->execute($params);
            
            return $this->conn->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Error creando usuario: " . $e->getMessage());
            throw $e;
        }
    }
    
    // O puedes renombrar el método existente y crear uno nuevo compatible
    public function crearUsuario($data) {
        return $this->crearConArray($data);
    }

    // GUARDAR TOKEN DE VERIFICACIÓN
    public function crearTokenVerificacion($id_usuario, $token) {
        $sql = "INSERT INTO tokens_correo (
                    id_usuario,
                    token,
                    tipo,
                    fecha_expiracion
                ) VALUES (
                    :id_usuario,
                    :token,
                    'verificar_correo',
                    DATE_ADD(NOW(), INTERVAL 24 HOUR)
                )";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':token' => $token
        ]);
    }

    // ACTIVAR CUENTA CON TOKEN
    public function activarCuenta($token) {
        try {
            // 1. Verificar token válido
            $sql = "SELECT t.id_usuario 
                    FROM tokens_correo t
                    WHERE t.token = :token 
                    AND t.tipo = 'verificar_correo'
                    AND t.usado = 0
                    AND t.fecha_expiracion > NOW()";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return false; // Token inválido o expirado
            }
            
            $id_usuario = $result['id_usuario'];
            
            // 2. Activar usuario
            $sql_activar = "UPDATE usuarios 
                           SET estado = 'activo', 
                               correo_verificado = 1
                           WHERE id_usuario = :id_usuario";
            
            $stmt_activar = $this->conn->prepare($sql_activar);
            $stmt_activar->bindParam(':id_usuario', $id_usuario);
            $stmt_activar->execute();
            
            // 3. Marcar token como usado
            $sql_usado = "UPDATE tokens_correo 
                         SET usado = 1 
                         WHERE token = :token";
            
            $stmt_usado = $this->conn->prepare($sql_usado);
            $stmt_usado->bindParam(':token', $token);
            $stmt_usado->execute();
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error activando cuenta: " . $e->getMessage());
            return false;
        }
    }

    // LOGIN
    public function login($correo, $password) {
        $sql = "SELECT * FROM usuarios
                WHERE correo = :correo 
                AND estado = 'activo'";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    // VERIFICAR SI CORREO EXISTE
    public function correoExiste($correo) {
        $sql = "SELECT id_usuario FROM usuarios WHERE correo = :correo";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>