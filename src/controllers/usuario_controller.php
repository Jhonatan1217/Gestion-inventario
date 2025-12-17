<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// ⚠️ NO poner JSON global porque hay redirecciones (activar/logout)
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

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================================
// ✅ BASE_URL (para NO hardcodear localhost)
// Ajusta SOLO el $project si cambia la carpeta raíz.
// =====================================================
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host     = $_SERVER['HTTP_HOST']; // incluye puerto (ej: localhost:8888)

    // ✅ tu carpeta raíz del proyecto
    $project  = '/gestion_inventario/Gestion-inventario/';

    define('BASE_URL', $protocol . $host . $project);
}

if (!isset($conn)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'No se pudo establecer conexión con la base de datos']);
    exit;
}

// ================= HELPERS =================
function validarSoloTexto($s) {
    return preg_match('/^[A-Za-zÁÉÍÓÚÜáéíóúüñ\s]+$/u', (string)$s) === 1;
}

function colapsarEspacios($s) {
    return trim(preg_replace('/\s{2,}/u', ' ', (string)$s));
}

$usuario = new Usuario($conn);

$accion = $_GET['accion'] ?? null;
if (!$accion) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Debe especificar la acción']);
    exit;
}
// ============================
// ✅ ENVIAR CORREO RESET (helper)
// ============================
function enviarCorreoResetPassword($toEmail, $toName, $resetLink) {
    $mail = new PHPMailer(true);

    // 🔐 SOLUCIÓN DE ACENTOS
    $mail->CharSet  = 'UTF-8';
    $mail->Encoding = 'base64';


    // SMTP (MISMO que ya te funciona en "crear")
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sigainvetario2025@gmail.com';
    $mail->Password   = 'dwltqzowfouydwgf'; // app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('sigainvetario2025@gmail.com', 'Gestion Inventario');
    $mail->addAddress($toEmail, $toName ?: $toEmail);

    $mail->isHTML(true);
    $mail->Subject = 'Restablecer contraseña - SIGA';

    $mail->Body = "
        <h2>Restablecer contraseña</h2>
        <p>Recibimos una solicitud para restablecer tu contraseña.</p>
        <p>Haz clic aquí para continuar:</p>
        <p>
          <a href='$resetLink'
             style='background:#007832;color:#fff;padding:12px 18px;text-decoration:none;border-radius:8px;display:inline-block;'>
            RESTABLECER CONTRASEÑA
          </a>
        </p>
        <p style='color:#666;font-size:12px;'>Si no solicitaste esto, ignora este correo.</p>
        <hr>
    ";

    $mail->AltBody = "Restablecer contraseña: $resetLink";

    $mail->send();
    return true;
}


switch ($accion) {

    // =====================================================
    // ✅ LISTAR USUARIOS
    // GET: ?accion=listar
    // =====================================================
    case 'listar':
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($usuario->listar());
    break;


        // =====================================================
    // ✅ RESET PASSWORD (VALIDA TOKEN + CAMBIA PASSWORD)
    // POST FORM: ?accion=reset_password
    // =====================================================
    case 'reset_password':

        $token = trim($_POST['token'] ?? '');
        $p1    = (string)($_POST['password'] ?? '');
        $p2    = (string)($_POST['password2'] ?? '');

        if ($token === '') {
            header("Location: " . BASE_URL . "src/view/login/recuperar_contrasena.php?err=token");
            exit;
        }

        if ($p1 === '' || strlen($p1) < 8) {
            header("Location: " . BASE_URL . "src/view/login/reset_password.php?token=" . urlencode($token) . "&err=pass");
            exit;
        }

        if ($p1 !== $p2) {
            header("Location: " . BASE_URL . "src/view/login/reset_password.php?token=" . urlencode($token) . "&err=match");
            exit;
        }

        try {
            // Buscar token válido
            $q = $conn->prepare("
                SELECT id_token, id_usuario, fecha_expiracion, usado
                FROM tokens_correo
                WHERE token = :t AND tipo = 'reset_password'
                LIMIT 1
            ");
            $q->execute([':t' => $token]);
            $row = $q->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                header("Location: " . BASE_URL . "src/view/login/recuperar_contrasena.php?err=invalid");
                exit;
            }

            if ((int)$row['usado'] === 1) {
                header("Location: " . BASE_URL . "src/view/login/recuperar_contrasena.php?err=used");
                exit;
            }

            if (strtotime($row['fecha_expiracion']) < time()) {
                // Marcar token como usado
                $conn->prepare("UPDATE tokens_correo SET usado = 1 WHERE id_token = :id")
                     ->execute([':id' => (int)$row['id_token']]);

                header("Location: " . BASE_URL . "src/view/login/recuperar_contrasena.php?err=expired");
                exit;
            }

            $idUsuario = (int)$row['id_usuario'];
            $idToken   = (int)$row['id_token'];

            // 🔐 Hashear nueva contraseña
            $newHash = password_hash($p1, PASSWORD_DEFAULT);

            // Actualizar contraseña
            $conn->prepare("
                UPDATE usuarios
                SET password = :ph
                WHERE id_usuario = :uid
                LIMIT 1
            ")->execute([
                ':ph'  => $newHash,
                ':uid' => $idUsuario
            ]);

            // Marcar token como usado
            $conn->prepare("
                UPDATE tokens_correo
                SET usado = 1
                WHERE id_token = :id
                LIMIT 1
            ")->execute([':id' => $idToken]);

            // Redirigir al login con éxito
            header("Location: " . BASE_URL . "src/view/login/login.php?reset=ok");
            exit;

        } catch (Exception $e) {
            header("Location: " . BASE_URL . "src/view/login/recuperar_contrasena.php?err=server");
            exit;
        }

    break;

    // =====================================================
// ✅ SOLICITAR RESET PASSWORD (GUARDA TOKEN + ENVÍA CORREO)
// POST FORM: ?accion=request_reset_password  (correo)
// =====================================================
case 'request_reset_password':

    $correo = trim($_POST['correo'] ?? '');

    // Mensaje genérico (seguridad)
    $msgOk = "Si el correo está registrado, recibirás un enlace para restablecer tu contraseña.";

    if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: " . BASE_URL . "src/view/login/recuperar_contrasena.php?err=correo");
        exit;
    }

    try {
        // Buscar usuario por correo
        $stmt = $conn->prepare("SELECT id_usuario, nombre_completo, correo FROM usuarios WHERE correo = :c LIMIT 1");
        $stmt->execute([':c' => $correo]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);

        // Siempre redirige con OK aunque no exista (no revelar)
        if (!$u) {
            header("Location: " . BASE_URL . "src/view/login/recuperar_contrasena.php?ok=1");
            exit;
        }

        $idUsuario = (int)$u['id_usuario'];
        $nombre    = $u['nombre_completo'] ?? $correo;

        // Invalidar tokens anteriores sin usar
        $conn->prepare("
            UPDATE tokens_correo
            SET usado = 1
            WHERE id_usuario = :uid AND tipo = 'reset_password' AND usado = 0
        ")->execute([':uid' => $idUsuario]);

        // Token + expiración
        $token = bin2hex(random_bytes(32));
        $fechaExp = (new DateTime('now'))->modify('+30 minutes')->format('Y-m-d H:i:s');

        // Guardar token
        $ins = $conn->prepare("
            INSERT INTO tokens_correo (id_usuario, token, tipo, fecha_expiracion, usado)
            VALUES (:uid, :t, 'reset_password', :exp, 0)
        ");
        $ins->execute([
            ':uid' => $idUsuario,
            ':t'   => $token,
            ':exp' => $fechaExp
        ]);

        // Link (usa tu BASE_URL real)
        $resetLink = BASE_URL . "src/view/login/reset_password.php?token=" . urlencode($token);

        // Enviar correo real
        enviarCorreoResetPassword($correo, $nombre, $resetLink);

        header("Location: " . BASE_URL . "src/view/login/recuperar_contrasena.php?ok=1");
        exit;

    } catch (Exception $e) {
        // Si quieres ver el error exacto:
        // echo "Error: " . $e->getMessage(); exit;

        header("Location: " . BASE_URL . "src/view/login/recuperar_contrasena.php?err=send");
        exit;
    }

break;


    // =====================================================
    // ✅ OBTENER USUARIO POR ID
    // GET: ?accion=obtener&id_usuario=1
    // =====================================================
    case 'obtener':
        header('Content-Type: application/json; charset=utf-8');

        $id_usuario = $_GET['id_usuario'] ?? null;

        if (!$id_usuario) {
            echo json_encode(['error' => 'Debe enviar el parámetro id_usuario']);
            exit;
        }

        $res = $usuario->obtenerPorId($id_usuario);
        echo json_encode($res ? $res : ['error' => 'Usuario no encontrado']);
    break;

    // =====================================================
    // ✅ CREAR USUARIO + TOKEN + CORREO ACTIVACIÓN + CREDENCIALES
    // POST JSON: ?accion=crear
    // =====================================================
    case 'crear':
        header('Content-Type: application/json; charset=utf-8');

        $data = json_decode(file_get_contents("php://input"), true);

        $nombre           = $data['nombre_completo']   ?? null;
        $tipo_documento   = $data['tipo_documento']    ?? null;
        $numero_documento = $data['numero_documento']  ?? null;
        $telefono         = $data['telefono']          ?? null;
        $cargo            = $data['cargo']             ?? null;
        $correo           = $data['correo']            ?? null;
        $direccion        = $data['direccion']         ?? null;
        $password         = $data['password']          ?? null;
        $id_programa      = $data['id_programa']       ?? null;

        if (!$nombre || !$correo || !$password) {
            echo json_encode(['error' => 'Datos incompletos']);
            exit;
        }

        // Validación nombre
        $nombre = colapsarEspacios($nombre);
        if ($nombre === '' || !validarSoloTexto($nombre)) {
            echo json_encode(['error' => 'El nombre solo puede contener letras y espacios']);
            exit;
        }

        // Validar cargo
        $cargosValidos = ['Coordinador','Subcoordinador','Instructor','Pasante','Aprendiz'];
        if ($cargo && !in_array($cargo, $cargosValidos, true)) {
            echo json_encode(['error' => 'Cargo no válido']);
            exit;
        }

        // Regla: solo Instructor puede llevar id_programa
        if ($cargo !== 'Instructor') {
            $id_programa = null;
        } else {
            if ($id_programa === null || $id_programa === '' || (int)$id_programa <= 0) {
                echo json_encode(['error' => 'Debe seleccionar un programa para el Instructor.']);
                exit;
            }
        }

        try {
            // Duplicados
            if ($numero_documento && $usuario->obtenerPorDocumento($numero_documento)) {
                echo json_encode(['error' => 'El número de documento ya está registrado']);
                exit;
            }
            if ($usuario->obtenerPorCorreo($correo)) {
                echo json_encode(['error' => 'El correo ya está registrado']);
                exit;
            }

            $token = bin2hex(random_bytes(32));

            // ✅ Crear usuario: modelo hashea; crea como inactivo si hay token
            $lastId = $usuario->crear(
                $nombre,
                $tipo_documento,
                $numero_documento,
                $telefono,
                $cargo,
                $correo,
                $direccion,
                $password,
                $token,
                $id_programa
            );

            if (!$lastId) {
                echo json_encode(['error' => 'Error al crear usuario']);
                exit;
            }

            // ✅ Guardar token (si falla, muestra error real)
            try {
                $usuario->crearTokenVerificacion($lastId, $token);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'error' => 'No se pudo guardar el token en tokens_correo',
                    'detalle' => $e->getMessage(),
                    'id_usuario' => $lastId
                ]);
                exit;
            }

            // ✅ Enviar correo
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

                // ✅ LINK con BASE_URL (sin localhost hardcode)
                $link = BASE_URL . "src/controllers/usuario_controller.php?accion=activar&token=$token";

                $mail->isHTML(true);
                $mail->Subject = 'Activacion de cuenta - Sistema de Inventario';

                // ✅ BODY con credenciales incluidasf
                $mail->Body = "
                    <h2>Hola $nombre</h2>

                    <p>Tu cuenta en el <strong>Sistema de Gestión de Inventario</strong> ha sido creada correctamente.</p>

                    <p><strong>📌 Tus credenciales de acceso son:</strong></p>

                    <ul>
                        <li><strong>Usuario (correo):</strong> $correo</li>
                        <li><strong>Contraseña:</strong> $password</li>
                    </ul>

                    <p style='color:#666;font-size:12px;'>
                        Por seguridad, te recomendamos cambiar tu contraseña después de iniciar sesión.
                    </p>

                    <hr>

                    <p>Para activar tu cuenta, haz clic en el siguiente botón:</p>

                    <p>
                        <a href='$link'
                           style='background-color:#4CAF50;
                                  color:#ffffff;
                                  padding:12px 24px;
                                  text-decoration:none;
                                  border-radius:5px;
                                  display:inline-block;'>
                           ACTIVAR MI CUENTA
                        </a>
                    </p>

                   
                ";

                // ✅ ALT BODY con credenciales
                $mail->AltBody =
"Hola $nombre,

Tu cuenta en el Sistema de Gestión de Inventario ha sido creada correctamente.

Credenciales de acceso:
Usuario (correo): $correo
Contraseña: $password

Activa tu cuenta aquí:
$link

Recomendación: cambia tu contraseña después de iniciar sesión.
";

                $mail->send();

                echo json_encode([
                    'success' => true,
                    'mensaje' => 'Usuario creado. Revisa tu correo para activar la cuenta',
                    'id_usuario' => $lastId
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Usuario creado pero error enviando correo',
                    'detalle' => $e->getMessage()
                ]);
            }

        } catch (PDOException $e) {
            if ((string)$e->getCode() === '23000') {
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

    // =====================================================
    // ✅ ACTIVAR CUENTA POR TOKEN (REDIRECCIÓN)
    // GET: ?accion=activar&token=...
    // =====================================================
    case 'activar':
        $token = $_GET['token'] ?? null;

        if (!$token) {
            echo "Token inválido";
            exit;
        }

        if ($usuario->activarCuenta($token)) {
            session_unset();
            session_destroy();

            // ✅ REDIRECCIÓN con BASE_URL
            header("Location: " . BASE_URL . "src/view/login/login.php?activacion=exito");
            exit;
        } else {
            echo "Token inválido o expirado. Contacta al administrador.";
            exit;
        }
    break;

    // =====================================================
    // ✅ ACTUALIZAR USUARIO (EDITAR)
    // POST JSON: ?accion=actualizar
    // =====================================================
    case 'actualizar':
        header('Content-Type: application/json; charset=utf-8');

        $data = json_decode(file_get_contents("php://input"), true);

        $id_usuario = $data['id_usuario'] ?? $_POST['id_usuario'] ?? $_GET['id_usuario'] ?? null;

        if (!$id_usuario) {
            echo json_encode(['error' => 'Debe enviar id_usuario']);
            exit;
        }

        $usuarioActual = $usuario->obtenerPorId($id_usuario);
        if (!$usuarioActual) {
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }

        $nombre      = $data['nombre_completo']  ?? $usuarioActual['nombre_completo'];
        $tipo_doc    = $data['tipo_documento']   ?? $usuarioActual['tipo_documento'];
        $num_doc     = $data['numero_documento'] ?? $usuarioActual['numero_documento'];
        $telefono    = $data['telefono']         ?? $usuarioActual['telefono'];
        $cargo       = $data['cargo']            ?? $usuarioActual['cargo'];
        $correo      = $data['correo']           ?? $usuarioActual['correo'];
        $direccion   = $data['direccion']        ?? $usuarioActual['direccion'];
        $password    = $data['password']         ?? null;
        $id_programa = $data['id_programa']      ?? ($usuarioActual['id_programa'] ?? null);

        $nombre = colapsarEspacios($nombre);
        if ($nombre === '' || !validarSoloTexto($nombre)) {
            echo json_encode(['error' => 'El nombre solo puede contener letras y espacios']);
            exit;
        }

        $cargosValidos = ['Coordinador','Subcoordinador','Instructor','Pasante','Aprendiz'];
        if (!in_array($cargo, $cargosValidos, true)) {
            echo json_encode(['error' => 'Cargo no válido']);
            exit;
        }

        // Regla id_programa
        if ($cargo !== 'Instructor') {
            $id_programa = null;
        } else {
            if ($id_programa === null || $id_programa === '' || (int)$id_programa <= 0) {
                echo json_encode(['error' => 'Debe seleccionar un programa para el Instructor.']);
                exit;
            }
        }

        // Documento duplicado
        if ($num_doc !== $usuarioActual['numero_documento']) {
            $existeDoc = $usuario->obtenerPorDocumento($num_doc);
            if ($existeDoc && (int)$existeDoc['id_usuario'] !== (int)$id_usuario) {
                echo json_encode(['error' => 'El número de documento ya está registrado']);
                exit;
            }
        }

        // Correo duplicado
        if ($correo !== $usuarioActual['correo'] && $usuario->obtenerPorCorreo($correo)) {
            echo json_encode(['error' => 'El correo ya está registrado']);
            exit;
        }

        $ok = $usuario->actualizar(
            $id_usuario,
            $nombre,
            $tipo_doc,
            $num_doc,
            $telefono,
            $cargo,
            $correo,
            $password,
            $direccion,
            $id_programa
        );

        echo json_encode(
            $ok ? ['success' => true, 'mensaje' => 'Usuario actualizado correctamente']
                : ['success' => false, 'error' => 'No se pudo actualizar el usuario']
        );
    break;

    // =====================================================
    // ✅ CAMBIAR ESTADO (NO ELIMINA)
    // POST JSON: ?accion=cambiar_estado
    // =====================================================
    case 'cambiar_estado':
        header('Content-Type: application/json; charset=utf-8');

        $data = json_decode(file_get_contents("php://input"), true);

        $id_usuario = $data['id_usuario'] ?? $_POST['id_usuario'] ?? $_GET['id_usuario'] ?? null;
        $estado     = $data['estado']     ?? $_POST['estado']     ?? $_GET['estado']     ?? null;

        if ($id_usuario === null || $estado === null) {
            echo json_encode(['error' => 'Debe enviar id_usuario y estado (1 o 0)']);
            exit;
        }

        if ((int)$estado !== 1 && (int)$estado !== 0) {
            echo json_encode(['error' => 'El estado debe ser 1 o 0']);
            exit;
        }

        if (!$usuario->obtenerPorId($id_usuario)) {
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }

        echo json_encode(
            $usuario->cambiarEstado($id_usuario, $estado)
                ? ['success' => true, 'mensaje' => 'Estado actualizado']
                : ['success' => false, 'error' => 'Error al actualizar estado']
        );
    break;

    // =====================================================
    // ✅ LOGIN (JSON)
    // =====================================================
    case 'login':
        header('Content-Type: application/json; charset=utf-8');

        $data = json_decode(file_get_contents("php://input"), true);

        $correo   = $data['correo'] ?? null;
        $password = $data['password'] ?? null;

        if (!$correo || !$password) {
            echo json_encode(['error' => 'Credenciales incompletas']);
            exit;
        }

        $user = $usuario->login($correo, $password);

        if ($user) {
            $_SESSION['usuario'] = [
                'id'     => $user['id_usuario'],
                'nombre' => $user['nombre_completo'],
                'correo' => $user['correo'],
                'cargo'  => $user['cargo']
            ];

            echo json_encode([
                'success' => true,
                'mensaje' => 'Login exitoso',
                'usuario' => [
                    'id'     => $user['id_usuario'],
                    'nombre' => $user['nombre_completo'],
                    'cargo'  => $user['cargo']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Credenciales incorrectas o cuenta inactiva'
            ]);
        }
    break;

    // =====================================================
    // ✅ LOGOUT (REDIRECCIÓN)
    // =====================================================
    case 'logout':
        session_unset();
        session_destroy();

        // ✅ REDIRECCIÓN con BASE_URL
        header("Location: " . BASE_URL . "src/view/login/login.php");
        exit;
    break;

    default:
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Acción no válida']);
    break;

   // =====================================================
// ✅ ACTUALIZAR PERFIL (TELÉFONO, DIRECCIÓN Y FOTO)
// POST multipart/form-data: ?accion=actualizar_perfil
// Usa el usuario logueado (SESSION)
// Guarda la foto en la columna: foto_perfil
// =====================================================
case 'actualizar_perfil':
    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['error' => 'No hay sesión activa. Inicia sesión nuevamente.']);
        exit;
    }

    $id_usuario = (int)$_SESSION['usuario_id'];

    // Campos editables en tu modal
    $telefono  = isset($_POST['telefono'])  ? colapsarEspacios($_POST['telefono'])  : '';
    $direccion = isset($_POST['direccion']) ? colapsarEspacios($_POST['direccion']) : '';

    // Validación básica del teléfono (opcional)
    if ($telefono !== '' && !preg_match('/^[0-9+\s\-()]{7,20}$/', $telefono)) {
        echo json_encode(['error' => 'Teléfono no válido.']);
        exit;
    }

    // ---- FOTO (si viene) ----
    $fotoPathDB = null;

    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_NO_FILE) {

        if ($_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['error' => 'Error subiendo la foto.']);
            exit;
        }

        $tmp  = $_FILES['foto_perfil']['tmp_name'];
        $name = $_FILES['foto_perfil']['name'] ?? '';
        $size = (int)($_FILES['foto_perfil']['size'] ?? 0);

        // Límite 2MB
        if ($size > 2 * 1024 * 1024) {
            echo json_encode(['error' => 'La foto supera el límite de 2MB.']);
            exit;
        }

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (!in_array($ext, $allowed, true)) {
            echo json_encode(['error' => 'Formato no permitido. Usa JPG, PNG o WEBP.']);
            exit;
        }

        // Carpeta destino (ABS)
        $uploadDirAbs = __DIR__ . '/../uploads/perfiles/';
        if (!is_dir($uploadDirAbs)) {
            @mkdir($uploadDirAbs, 0777, true);
        }

        // Nombre único
        $fileName = 'perfil_' . $id_usuario . '_' . time() . '.' . $ext;
        $destAbs  = $uploadDirAbs . $fileName;

        if (!move_uploaded_file($tmp, $destAbs)) {
            echo json_encode(['error' => 'No se pudo guardar la foto.']);
            exit;
        }

        // Ruta RELATIVA (lo que guardas en DB)
        $fotoPathDB = 'src/uploads/perfiles/' . $fileName;
    }

    try {
        // ✅ Actualiza SOLO telefono/direccion + foto_perfil (si hay)
        $sql = "UPDATE usuarios 
                SET telefono = :t,
                    direccion = :d"
                . ($fotoPathDB ? ", foto_perfil = :f" : "") .
               " WHERE id_usuario = :id
                 LIMIT 1";

        $stmt = $conn->prepare($sql);

        $params = [
            ':t'  => $telefono,
            ':d'  => $direccion,
            ':id' => $id_usuario
        ];
        if ($fotoPathDB) $params[':f'] = $fotoPathDB;

        $ok = $stmt->execute($params);

        if (!$ok) {
            echo json_encode(['error' => 'No se pudo actualizar el perfil.']);
            exit;
        }

        // ✅ Actualiza sesión para reflejar cambios en el header
        $_SESSION['usuario_telefono']  = $telefono;
        $_SESSION['usuario_direccion'] = $direccion;

        if ($fotoPathDB) {
            // Tu header lee $_SESSION['usuario_foto']
            $_SESSION['usuario_foto'] = $fotoPathDB;
        }

        echo json_encode(['success' => true, 'mensaje' => 'Perfil actualizado correctamente']);
        exit;

    } catch (Exception $e) {
        echo json_encode(['error' => 'Error del servidor al actualizar perfil', 'detalle' => $e->getMessage()]);
        exit;
    }

break;


// =====================================================
// ✅ CAMBIAR PASSWORD (DESDE PERFIL)
// POST multipart/form-data: ?accion=cambiar_password
// =====================================================
case 'cambiar_password':
    header('Content-Type: application/json; charset=utf-8');

    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['error' => 'No hay sesión activa. Inicia sesión nuevamente.']);
        exit;
    }

    $id_usuario = (int)$_SESSION['usuario_id'];

    $actual     = (string)($_POST['password_actual'] ?? '');
    $nueva      = (string)($_POST['password_nueva'] ?? '');
    $confirmar  = (string)($_POST['password_confirmar'] ?? '');

    if ($actual === '' || $nueva === '' || $confirmar === '') {
        echo json_encode(['error' => 'Complete todos los campos.']);
        exit;
    }

    if (strlen($nueva) < 8) {
        echo json_encode(['error' => 'La nueva contraseña debe tener mínimo 8 caracteres.']);
        exit;
    }

    if ($nueva !== $confirmar) {
        echo json_encode(['error' => 'La confirmación no coincide.']);
        exit;
    }

    try {
        // Traer hash actual
        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id_usuario = :id LIMIT 1");
        $stmt->execute([':id' => $id_usuario]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            echo json_encode(['error' => 'Usuario no encontrado.']);
            exit;
        }

        $hashActual = (string)$row['password'];

        if (!password_verify($actual, $hashActual)) {
            echo json_encode(['error' => 'La contraseña actual es incorrecta.']);
            exit;
        }

        // Evitar repetir
        if (password_verify($nueva, $hashActual)) {
            echo json_encode(['error' => 'La nueva contraseña no puede ser igual a la actual.']);
            exit;
        }

        $newHash = password_hash($nueva, PASSWORD_DEFAULT);

        $upd = $conn->prepare("UPDATE usuarios SET password = :ph WHERE id_usuario = :id LIMIT 1");
        $ok  = $upd->execute([':ph' => $newHash, ':id' => $id_usuario]);

        echo json_encode(
            $ok ? ['success' => true, 'message' => 'Contraseña actualizada correctamente.']
               : ['error' => 'No se pudo actualizar la contraseña.']
        );
        exit;

    } catch (Exception $e) {
        echo json_encode(['error' => 'Error del servidor al cambiar contraseña', 'detalle' => $e->getMessage()]);
        exit;
    }

break;


}
?>
