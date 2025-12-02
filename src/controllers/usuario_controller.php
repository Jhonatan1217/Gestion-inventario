<?php
header('Content-Type: application/json; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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

// Instanciar modelo
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

        echo json_encode(
            $res ? $res : ['error' => 'Usuario no encontrado']
        );
    break;


    case 'crear':
    $data = json_decode(file_get_contents("php://input"), true);

    $u = [
        'nombre_completo'  => $data['nombre_completo']  ?? $_POST['nombre_completo']  ?? null,
        'tipo_documento'   => $data['tipo_documento']   ?? $_POST['tipo_documento']   ?? null,
        'numero_documento' => $data['numero_documento'] ?? $_POST['numero_documento'] ?? null,
        'telefono'        => $data['telefono']        ?? $_POST['telefono']        ?? null,
        'cargo'           => $data['cargo']           ?? $_POST['cargo']           ?? null,
        'correo'          => $data['correo']          ?? $_POST['correo']          ?? null,
        'direccion'       => $data['direccion']       ?? $_POST['direccion']       ?? null
    ];

    if (in_array(null, $u, true)) {
        echo json_encode(['error' => 'Debe enviar todos los campos obligatorios']);
        exit;
    }

    // Normalizar nombre
    $u['nombre_completo'] = colapsarEspacios($u['nombre_completo']);
    if ($u['nombre_completo'] === '' || !validarSoloTexto($u['nombre_completo'])) {
        echo json_encode(['error' => 'El nombre solo puede contener letras y espacios']);
        exit;
    }

    // Validar cargo permitido por ENUM
    $cargosValidos = ['Coordinador','subcoordinador','Instructor','Pasante','Aprendiz'];
    if (!in_array($u['cargo'], $cargosValidos, true)) {
        echo json_encode(['error' => 'Cargo no válido']);
        exit;
    }

    // Verificar UNIQUE correo
    if ($usuario->obtenerPorCorreo($u['correo'])) {
        echo json_encode(['error' => 'El correo ya está registrado']);
        exit;
    }

    // Llamado correcto al modelo (enviando parámetros individuales)
    $usuario->crear(
        $u['nombre_completo'],
        $u['tipo_documento'],
        $u['numero_documento'],
        $u['telefono'],
        $u['cargo'],
        $u['correo'],
        $u['direccion']
    );

    echo json_encode(['mensaje' => 'Usuario creado correctamente']);
    break;


    case 'actualizar':
    $data = json_decode(file_get_contents("php://input"), true);

    $id_usuario = $data['id_usuario'] ?? $_POST['id_usuario'] ?? $_GET['id_usuario'] ?? null;
    $nombre = $data['nombre_completo'] ?? $_POST['nombre_completo'] ?? null;
    $tipo_doc = $data['tipo_documento'] ?? $_POST['tipo_documento'] ?? null;
    $num_doc = $data['numero_documento'] ?? $_POST['numero_documento'] ?? null;
    $telefono = $data['telefono'] ?? $_POST['telefono'] ?? null;
    $cargo = $data['cargo'] ?? $_POST['cargo'] ?? null;
    $correo = $data['correo'] ?? $_POST['correo'] ?? null;
    $direccion = $data['direccion'] ?? $_POST['direccion'] ?? null;

    if (!$id_usuario || !$nombre || !$tipo_doc || !$num_doc || !$telefono || !$cargo || !$correo || !$direccion) {
        echo json_encode(['error' => 'Debe enviar todos los campos obligatorios para actualizar']);
        exit;
    }

    // Normalizar y validar nombre
    $nombre = colapsarEspacios($nombre);
    if ($nombre === '' || !validarSoloTexto($nombre)) {
        echo json_encode(['error' => 'El nombre solo puede contener letras y espacios']);
        exit;
    }

    // Validar cargo por ENUM
    $cargosValidos = ['Coordinador','Instructor','Pasante'];
    if (!in_array($cargo, $cargosValidos, true)) {
        echo json_encode(['error' => 'Cargo no válido']);
        exit;
    }

    // Si el correo cambió, verificar que no exista en otro usuario
    $usuarioActual = $usuario->obtenerPorId($id_usuario);
    if (!$usuarioActual) {
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit;
    }

    if ($correo !== $usuarioActual['correo'] && $usuario->obtenerPorCorreo($correo)) {
        echo json_encode(['error' => 'El correo ya está registrado por otro usuario']);
        exit;
    }

    // Llamado correcto al modelo
    echo json_encode(
        $usuario->actualizar($id_usuario, $nombre, $tipo_doc, $num_doc, $telefono, $cargo, $correo, $direccion)
            ? ['mensaje' => 'Usuario actualizado correctamente']
            : ['error' => 'No se pudo actualizar el usuario']
    );
    break;

    case 'eliminar':
    $data = json_decode(file_get_contents("php://input"), true);

    $id_usuario = $data['id_usuario'] ?? $_POST['id_usuario'] ?? $_GET['id_usuario'] ?? null;

    if (!$id_usuario) {
        echo json_encode(['error' => 'Debe enviar el parámetro id_usuario']);
        exit;
    }

    // Verificar que el usuario exista antes de eliminar
    if (!$usuario->obtenerPorId($id_usuario)) {
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit;
    }

    // Llamar al modelo para eliminar
    echo json_encode(
        $usuario->eliminar($id_usuario)
            ? ['mensaje' => 'Usuario eliminado correctamente']
            : ['error' => 'No se pudo eliminar el usuario']
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
        echo json_encode(['error' => 'El estado debe ser 1 (activo) o 0 (inactivo)']);
        exit;
    }

    if (!$usuario->obtenerPorId($id_usuario)) {
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit;
    }

    echo json_encode(
        $usuario->cambiarEstado($id_usuario, $estado)
            ? ['mensaje' => 'Estado del usuario actualizado correctamente']
            : ['error' => 'No se pudo actualizar el estado']
    );
    break;


    default:
        echo json_encode(['error' => 'Acción no válida']);
        break;
}
