<?php
header('Content-Type: application/json; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../../Config/database.php';
include_once __DIR__ . '/../models/usuario.php';

if (!isset($conn)) {
    echo json_encode(['error' => 'No se pudo establecer conexión con la base de datos']);
    exit;
}

function validarSoloTexto($s) {
    return preg_match('/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+$/u', $s) === 1;
}

function colapsarEspacios($s) {
    return trim(preg_replace('/\s{2,}/u', ' ', (string)$s));
}

$usuario = new Usuario($conn);

$accion = $_GET['accion'] ?? null;
if (!$accion) {
    echo json_encode(['error' => 'Debe especificar la acción en la URL, por ejemplo: ?accion=listar']);
    exit;
}

switch ($accion) {

    case 'listar':
        echo json_encode($usuario->listar());
    break;

    case 'obtener':
        $id_usuario = $_GET['id_usuario'] ?? null;

        if (!$id_usuario) {
            echo json_encode(['error' => 'Debe enviar el parámetro id_usuario']);
            exit;
        }

        $res = $usuario->obtenerPorId($id_usuario);
        echo json_encode($res ? $res : ['error' => 'Usuario no encontrado']);
    break;

    case 'crear':
        $data = json_decode(file_get_contents("php://input"), true);

        $u = [
            'nombre_completo'  => $data['nombre_completo']  ?? $_POST['nombre_completo']  ?? null,
            'tipo_documento'   => $data['tipo_documento']   ?? $_POST['tipo_documento']   ?? null,
            'numero_documento' => $data['numero_documento'] ?? $_POST['numero_documento'] ?? null,
            'telefono'         => $data['telefono']         ?? $_POST['telefono']         ?? null,
            'cargo'            => $data['cargo']            ?? $_POST['cargo']            ?? null,
            'correo'           => $data['correo']           ?? $_POST['correo']           ?? null,
            'direccion'        => $data['direccion']        ?? $_POST['direccion']        ?? null,
            'password'         => $data['password']         ?? $_POST['password']         ?? null,
            'id_programa'      => $data['id_programa']      ?? $_POST['id_programa']      ?? null,
        ];

        // 👉 Estado por defecto: ACTIVO (lo manejas como 'activo' en el modelo)
        $u['estado'] = 1; // puedes dejarlo si lo usas luego para otra cosa

        if (in_array(null, [
            $u['nombre_completo'],
            $u['tipo_documento'],
            $u['numero_documento'],
            $u['telefono'],
            $u['cargo'],
            $u['correo'],
            $u['direccion'],
            $u['password'],
        ], true)) {
            echo json_encode(['error' => 'Debe enviar todos los campos obligatorios']);
            exit;
        }

        $u['nombre_completo'] = colapsarEspacios($u['nombre_completo']);
        if ($u['nombre_completo'] === '' || !validarSoloTexto($u['nombre_completo'])) {
            echo json_encode(['error' => 'El nombre solo puede contener letras y espacios']);
            exit;
        }

        $cargosValidos = ['Coordinador','Subcoordinador','Instructor','Pasante','Aprendiz'];
        if (!in_array($u['cargo'], $cargosValidos, true)) {
            echo json_encode(['error' => 'Cargo no válido']);
            exit;
        }

        if ($usuario->obtenerPorDocumento($u['numero_documento'])) {
            echo json_encode(['error' => 'El número de documento ya está registrado']);
            exit;
        }

        if ($usuario->obtenerPorCorreo($u['correo'])) {
            echo json_encode(['error' => 'El correo ya está registrado']);
            exit;
        }

        if ($u['cargo'] !== 'Instructor') {
            $u['id_programa'] = null;
        }

        if ($u['cargo'] === 'Instructor' && empty($u['id_programa'])) {
            echo json_encode(['error' => 'Debe seleccionar un programa para el Instructor.']);
            exit;
        }

        $ok = $usuario->crear(
            $u['nombre_completo'],
            $u['tipo_documento'],
            $u['numero_documento'],
            $u['telefono'],
            $u['cargo'],
            $u['correo'],
            $u['direccion'],
            $u['password'],
            $u['id_programa'] // 👈 AHORA SÍ ES id_programa
        );

        echo json_encode(
            $ok ? ['mensaje' => 'Usuario creado correctamente']
                : ['error' => 'No se pudo crear el usuario']
        );
    break;


    // ==========================
    // Actualizar usuario
    // ==========================
    case 'actualizar':
        $data = json_decode(file_get_contents("php://input"), true);

        $id_usuario = $data['id_usuario'] ?? $_POST['id_usuario'] ?? $_GET['id_usuario'] ?? null;

        if (!$id_usuario) {
            echo json_encode(['error' => 'Debe enviar id_usuario']);
            exit;
        }

        $nombre      = $data['nombre_completo']  ?? null;
        $tipo_doc    = $data['tipo_documento']   ?? null;
        $num_doc     = $data['numero_documento'] ?? null;
        $telefono    = $data['telefono']         ?? null;
        $cargo       = $data['cargo']            ?? null;
        $correo      = $data['correo']           ?? null;
        $direccion   = $data['direccion']        ?? null;
        $password    = $data['password']         ?? null;
        $id_programa = $data['id_programa']      ?? null;

        $usuarioActual = $usuario->obtenerPorId($id_usuario);
        if (!$usuarioActual) {
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }

        $nombre      = $nombre      ?? $usuarioActual['nombre_completo'];
        $tipo_doc    = $tipo_doc    ?? $usuarioActual['tipo_documento'];
        $num_doc     = $num_doc     ?? $usuarioActual['numero_documento'];
        $telefono    = $telefono    ?? $usuarioActual['telefono'];
        $cargo       = $cargo       ?? $usuarioActual['cargo'];
        $correo      = $correo      ?? $usuarioActual['correo'];
        $direccion   = $direccion   ?? $usuarioActual['direccion'];
        $id_programa = $id_programa ?? $usuarioActual['id_programa'];

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

        if ($num_doc !== $usuarioActual['numero_documento']) {
            $existeDoc = $usuario->obtenerPorDocumento($num_doc);
            if ($existeDoc && (int)$existeDoc['id_usuario'] !== (int)$id_usuario) {
                echo json_encode(['error' => 'El número de documento ya está registrado']);
                exit;
            }
        }

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
            $ok ? ['mensaje' => 'Usuario actualizado correctamente']
                : ['error' => 'No se pudo actualizar el usuario']
        );
    break;

    case 'buscar_documento':
        $doc = $_GET['numero_documento'] ?? null;
        if (!$doc) {
            echo json_encode(['error' => 'Debe enviar numero_documento']);
            exit;
        }
        echo json_encode(
            $usuario->obtenerPorDocumento($doc) ?: ['error' => 'Usuario no encontrado']
        );
    break;

    case 'login':
        $data = json_decode(file_get_contents("php://input"), true);

        $correo   = $data['correo']   ?? $_POST['correo']   ?? null;
        $password = $data['password'] ?? $_POST['password'] ?? null;

        if (!$correo || !$password) {
            echo json_encode(['error' => 'Debe enviar correo y password']);
            exit;
        }

        echo json_encode(
            $usuario->login($correo, $password)
                ? ['mensaje' => 'Login correcto']
                : ['error' => 'Credenciales incorrectas']
        );
    break;

    case 'cambiar_estado':
        $data = json_decode(file_get_contents("php://input"), true);

        $id_usuario = $data['id_usuario'] ?? $_POST['id_usuario'] ?? $_GET['id_usuario'] ?? null;
        $estado     = $data['estado']     ?? $_POST['estado']     ?? $_GET['estado']     ?? null;

        if ($id_usuario === null || $estado === null) {
            echo json_encode(['error' => 'Debe enviar id_usuario y estado (1 o 0)']);
            exit;
        }

        if ($estado != 1 && $estado != 0) {
            echo json_encode(['error' => 'El estado debe ser 1 o 0']);
            exit;
        }

        if (!$usuario->obtenerPorId($id_usuario)) {
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }

        echo json_encode(
            $usuario->cambiarEstado($id_usuario, $estado)
                ? ['mensaje' => 'Estado actualizado']
                : ['error' => 'Error al actualizar estado']
        );
    break;

    case 'actualizar_perfil':

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $idUsuario = (int)($_SESSION['usuario_id'] ?? 0);
        if ($idUsuario <= 0) {
            echo json_encode(['error' => 'ID de usuario inválido (sesión)']);
            exit;
        }

        $dataPerfil = $_POST;

        $nombreCompleto  = colapsarEspacios($dataPerfil['nombre_completo'] ?? '');
        $tipoDocumento   = $dataPerfil['tipo_documento'] ?? 'CC';
        $numeroDocumento = $dataPerfil['numero_documento'] ?? '';
        $telefono        = $dataPerfil['telefono'] ?? '';
        $direccion       = $dataPerfil['direccion'] ?? '';
        $correo          = $dataPerfil['correo'] ?? '';

        if ($nombreCompleto === '' || !validarSoloTexto($nombreCompleto)) {
            echo json_encode(['error' => 'Nombre inválido']);
            exit;
        }

        $rutaFotoPerfil = null;

        if (!empty($_FILES['foto_perfil']) && isset($_FILES['foto_perfil']['name']) && $_FILES['foto_perfil']['name'] !== '') {

            if ($_FILES['foto_perfil']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode([
                    'error' => 'No se recibió bien el archivo',
                    'upload_error_code' => $_FILES['foto_perfil']['error']
                ]);
                exit;
            }

            $ext = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg','jpeg','png','webp'];

            if (!in_array($ext, $permitidas, true)) {
                echo json_encode(['error' => 'Formato no permitido (jpg, jpeg, png, webp)']);
                exit;
            }

            $uploadDirAbs = __DIR__ . '/../uploads/perfiles/';
            if (!is_dir($uploadDirAbs)) {
                mkdir($uploadDirAbs, 0777, true);
            }

            $nuevoNombre = 'user_' . $idUsuario . '_' . time() . '.' . $ext;
            $destinoAbs  = $uploadDirAbs . $nuevoNombre;

            if (!move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $destinoAbs)) {
                echo json_encode([
                    'error' => 'No se pudo mover el archivo al destino',
                    'destino' => $destinoAbs
                ]);
                exit;
            }

            $rutaFotoPerfil = 'src/uploads/perfiles/' . $nuevoNombre;
        }

        $ok = $usuario->actualizarPerfil(
            $idUsuario,
            $nombreCompleto,
            $tipoDocumento,
            $numeroDocumento,
            $telefono,
            $direccion,
            $correo,
            $rutaFotoPerfil
        );

        if (!$ok) {
            echo json_encode(['error' => 'No se pudo actualizar el perfil']);
            exit;
        }

        $_SESSION['usuario_nombre']           = $nombreCompleto;
        $_SESSION['usuario_tipo_documento']   = $tipoDocumento;
        $_SESSION['usuario_numero_documento'] = $numeroDocumento;
        $_SESSION['usuario_telefono']         = $telefono;
        $_SESSION['usuario_direccion']        = $direccion;
        $_SESSION['usuario_correo']           = $correo;

        if ($rutaFotoPerfil !== null) {
            $_SESSION['usuario_foto'] = $rutaFotoPerfil;
        }

        echo json_encode([
            'ok' => true,
            'foto_guardada' => $rutaFotoPerfil
        ]);
        exit;
    break;

    // =====================================================
    // 🔒 CAMBIAR CONTRASEÑA (PDO + id_usuario)
    // =====================================================
    case 'cambiar_password':

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        if (!isset($_SESSION['usuario_id'])) {
            echo json_encode(['error' => 'Sesión no válida. Inicie sesión nuevamente.']);
            exit;
        }

        $usuarioId = (int)$_SESSION['usuario_id'];

        $passwordActual    = (string)($_POST['password_actual'] ?? '');
        $passwordNueva     = (string)($_POST['password_nueva'] ?? '');
        $passwordConfirmar = (string)($_POST['password_confirmar'] ?? '');

        if (trim($passwordActual) === '' || trim($passwordNueva) === '' || trim($passwordConfirmar) === '') {
            echo json_encode(['error' => 'Complete todos los campos para cambiar la contraseña.']);
            exit;
        }

        if (mb_strlen($passwordNueva, 'UTF-8') < 8) {
            echo json_encode(['error' => 'La nueva contraseña debe tener mínimo 8 caracteres.']);
            exit;
        }

        // ✅ NUEVO: debe tener al menos 1 número y 1 carácter especial
        $tieneNumero = preg_match('/[0-9]/', $passwordNueva) === 1;
        $tieneEspecial = preg_match('/[!@#$%^&*()_\-+=\[\]{};:\'",.<>\/?\\\\|`~]/', $passwordNueva) === 1;

        if (!$tieneNumero || !$tieneEspecial) {
            echo json_encode(['error' => 'La nueva contraseña debe incluir al menos un número y un carácter especial.']);
            exit;
        }

        if ($passwordNueva !== $passwordConfirmar) {
            echo json_encode(['error' => 'La confirmación no coincide con la nueva contraseña.']);
            exit;
        }

        if ($passwordActual === $passwordNueva) {
            echo json_encode(['error' => 'La nueva contraseña no puede ser igual a la actual.']);
            exit;
        }

        $hashActual = $usuario->obtenerHashPasswordPorId($usuarioId);

        if (!$hashActual) {
            echo json_encode(['error' => 'No se encontró el usuario o no fue posible validar la contraseña.']);
            exit;
        }

        if (!password_verify($passwordActual, $hashActual)) {
            echo json_encode(['error' => 'La contraseña actual es incorrecta.']);
            exit;
        }

        $nuevoHash = password_hash($passwordNueva, PASSWORD_DEFAULT);

        $ok = $usuario->actualizarPasswordPorId($usuarioId, $nuevoHash);

        if (!$ok) {
            echo json_encode(['error' => 'No fue posible actualizar la contraseña.']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente.']);
        exit;
    break;

    default:
        echo json_encode(['error' => 'Acción no válida']);
    break;
}
