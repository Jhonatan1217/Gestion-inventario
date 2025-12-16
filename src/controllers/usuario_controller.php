<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ⚠️ NO poner JSON global porque hay redirecciones
// header('Content-Type: application/json; charset=utf-8');

// ================= PHPMailer =================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../libs/PHPMailer/src/SMTP.php';

// ================= DB + MODEL =================
require_once __DIR__ . '/../../Config/database.php';
require_once __DIR__ . '/../models/usuario.php';

$usuario = new Usuario($conn);

$accion = $_GET['accion'] ?? null;
if (!$accion) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Debe especificar la acción']);
    exit;
}

switch ($accion) {

    // ================= CREAR USUARIO (CORREGIDO) =================
    case 'crear':

        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);

        $nombre      = $data['nombre_completo'] ?? null;
        $tipo_documento    = $data['tipo_documento'] ?? null;
        $numero_documento  = $data['numero_documento'] ?? null;
        $telefono    = $data['telefono'] ?? null;
        $cargo       = $data['cargo'] ?? null;
        $correo      = $data['correo'] ?? null;
        $direccion   = $data['direccion'] ?? null;
        $password    = $data['password'] ?? null;
        $id_programa = $data['id_programa'] ?? null;

        if (!$nombre || !$correo || !$password) {
            echo json_encode(['error' => 'Datos incompletos']);
            exit;
        }

        // 1. Primero crear usuario
        try {
            $token = bin2hex(random_bytes(32));
            
            // CORRECCIÓN: Enviar la contraseña SIN hashear
            $lastId = $usuario->crear(
                $nombre,
                $tipo_documento,
                $numero_documento,
                $telefono,
                $cargo,
                $correo,
                $direccion,
                $password, // ← SIN password_hash() aquí
                $token,
                $id_programa
            );
            
            if (!$lastId) {
                echo json_encode(['error' => 'Error al crear usuario']);
                exit;
            }

            // 2. Guardar token en tabla tokens_correo
            if (!$usuario->crearTokenVerificacion($lastId, $token)) {
                echo json_encode(['error' => 'Error creando token de verificación']);
                exit;
            }
            
            // 3. Enviar correo
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'sigainvetario2025@gmail.com';
                $mail->Password   = 'dwltqzowfouydwgf';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('sigainvetario2025@gmail.com', 'Gestion Inventario');
                $mail->addAddress($correo, $nombre);

                $link = "http://localhost/Gestion-inventario/src/controllers/usuario_controller.php?accion=activar&token=$token";

                $mail->isHTML(true);
                $mail->Subject = 'Activación de cuenta - Sistema de Inventario';
                $mail->Body = "
                    <h2>Hola $nombre</h2>
                    <p>Gracias por registrarte en el sistema de gestión de inventario.</p>
                    <p>Por favor haz clic en el siguiente enlace para activar tu cuenta:</p>
                    <p><a href='$link' style='background-color: #4CAF50; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;'>ACTIVAR MI CUENTA</a></p>
                    <p>Si el botón no funciona, copia y pega esta URL en tu navegador:</p>
                    <p><code>$link</code></p>
                    <p>Este enlace expirará en 24 horas.</p>
                ";
                $mail->AltBody = "Hola $nombre,\n\nActiva tu cuenta aquí: $link\n\nEste enlace expirará en 24 horas.";

                $mail->send();
                echo json_encode([
                    'success' => true,
                    'mensaje' => 'Usuario creado. Revisa tu correo para activar la cuenta'
                ]);
                
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Usuario creado pero error enviando correo',
                    'detalle' => $e->getMessage()
                ]);
            }
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'numero_documento') !== false) {
                    echo json_encode(['error' => 'El número de documento ya está registrado']);
                } elseif (strpos($e->getMessage(), 'correo') !== false) {
                    echo json_encode(['error' => 'El correo ya está registrado']);
                } else {
                    echo json_encode(['error' => 'Error de duplicado en base de datos']);
                }
            } else {
                echo json_encode(['error' => 'Error en base de datos: ' . $e->getMessage()]);
            }
        }
        break;

    // ================= ACTIVAR CUENTA =================
    case 'activar':
        session_start();

        $token = $_GET['token'] ?? null;

        if (!$token) {
            echo "Token inválido";
            exit;
        }

        if ($usuario->activarCuenta($token)) {
            // Cerrar sesión para evitar redirección al dashboard
            session_unset();
            session_destroy();

            // Redirigir al login con mensaje de éxito
            header("Location: http://localhost/Gestion-inventario/src/view/login/login.php?activacion=exito");
            exit;
        } else {
            echo "Token inválido o expirado. Contacta al administrador.";
        }
        break;
        
    // ================= LOGIN =================
    case 'login':
        session_start();
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        $correo = $data['correo'] ?? null;
        $password = $data['password'] ?? null;
        
        if (!$correo || !$password) {
            echo json_encode(['error' => 'Credenciales incompletas']);
            exit;
        }
        
        $user = $usuario->login($correo, $password);
        
        if ($user) {
            $_SESSION['usuario'] = [
                'id' => $user['id_usuario'],
                'nombre' => $user['nombre_completo'],
                'correo' => $user['correo'],
                'cargo' => $user['cargo'],
                'es_sistema' => $user['es_sistema']
            ];
            
            echo json_encode([
                'success' => true,
                'mensaje' => 'Login exitoso',
                'usuario' => [
                    'id' => $user['id_usuario'],
                    'nombre' => $user['nombre_completo'],
                    'cargo' => $user['cargo']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Credenciales incorrectas o cuenta inactiva'
            ]);
        }
        break;
        
    // ================= CERRAR SESIÓN =================
    case 'logout':
        session_start();
        session_unset();
        session_destroy();
        
        header("Location: http://localhost/Gestion-inventario/src/view/login/login.php");
        exit;
        break;
        
    default:
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Acción no válida']);
        break;
}
?>