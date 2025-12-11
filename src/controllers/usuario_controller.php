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

$usuario = new Usuario($conn);

$accion = $_GET['accion'] ?? null;
if (!$accion) {
    echo json_encode(['error' => 'Debe especificar la acción en la URL, por ejemplo: ?accion=listar']);
    exit;
}

switch ($accion) {

    //List users
    case 'listar':
        echo json_encode($usuario->listar());
    break;

    //Get user by id
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

    //Create user
    case 'crear':
        $data = json_decode(file_get_contents("php://input"), true);

        $u = [
            'nombre_completo'  => $data['nombre_completo']  ?? $_POST['nombre_completo']  ?? null,
            'tipo_documento'   => $data['tipo_documento']   ?? $_POST['tipo_documento']   ?? null,
            'numero_documento' => $data['numero_documento'] ?? $_POST['numero_documento'] ?? null,
            'telefono'        => $data['telefono'] ?? $_POST['telefono'] ?? null,
            'cargo'           => $data['cargo'] ?? $_POST['cargo'] ?? null,
            'correo'          => $data['correo'] ?? $_POST['correo'] ?? null,
            'direccion'       => $data['direccion'] ?? $_POST['direccion'] ?? null,
            'password'        => $data['password'] ?? $_POST['password'] ?? null,
        ];

        $id_programa = $data['id_programa'] ?? $_POST['id_programa'] ?? null;

        if (in_array(null, $u, true)) {
            echo json_encode(['error' => 'Debe enviar todos los campos obligatorios']);
            exit;
        }

        $u['nombre_completo'] = colapsarEspacios($u['nombre_completo']);
        if ($u['nombre_completo'] === '' || !validarSoloTexto($u['nombre_completo'])) {

            echo json_encode(['error' => 'El nombre solo puede contener letras y espacios']);
            exit;
        }

        $cargosValidos = ['Coordinador','subcoordinador','Instructor','Pasante','Aprendiz'];
        if (!in_array($u['cargo'], $cargosValidos, true)) {
            echo json_encode(['error' => 'Cargo no válido']);
            exit;
        }

        if ($usuario->obtenerPorCorreo($u['correo'])) {
            echo json_encode(['error' => 'El correo ya está registrado']);
            exit;
        }

        $usuario->crear(
            $u['nombre_completo'],
            $u['tipo_documento'],
            $u['numero_documento'],
            $u['telefono'],
            $u['cargo'],
            $u['correo'],
            $u['direccion'],
            $u['password'], 
            $id_programa
        );

        echo json_encode(['mensaje' => 'Usuario creado correctamente']);
    break;
    
    //Update user
    case 'actualizar':
        $data = json_decode(file_get_contents("php://input"), true);

        // Obtener ID desde JSON, POST o GET
        $id_usuario = $data['id_usuario'] ?? $_POST['id_usuario'] ?? $_GET['id_usuario'] ?? null;

        if (!$id_usuario) {
            echo json_encode(['error' => 'Debe enviar id_usuario']);
            exit;
        }

        // Obtener datos enviados
        $nombre     = $data['nombre_completo']  ?? null;
        $tipo_doc   = $data['tipo_documento']   ?? null;
        $num_doc    = $data['numero_documento'] ?? null;
        $telefono   = $data['telefono']         ?? null;
        $cargo      = $data['cargo']            ?? null;
        $correo     = $data['correo']           ?? null;
        $direccion  = $data['direccion']        ?? null;
        $password   = $data['password']         ?? null;
        $id_programa = $data['id_programa']     ?? null;

        // Obtener datos actuales del usuario
        $usuarioActual = $usuario->obtenerPorId($id_usuario);
        if (!$usuarioActual) {
            echo json_encode(['error' => 'Usuario no encontrado']);
            exit;
        }

        // Conservar valores anteriores si no se enviaron nuevos
        $nombre     = $nombre     ?? $usuarioActual['nombre_completo'];
        $tipo_doc   = $tipo_doc   ?? $usuarioActual['tipo_documento'];
        $num_doc    = $num_doc    ?? $usuarioActual['numero_documento'];
        $telefono   = $telefono   ?? $usuarioActual['telefono'];
        $cargo      = $cargo      ?? $usuarioActual['cargo'];
        $correo     = $correo     ?? $usuarioActual['correo'];
        $direccion  = $direccion  ?? $usuarioActual['direccion'];
        $id_programa = $id_programa ?? $usuarioActual['id_programa'];

        // Validar nombre
        $nombre = colapsarEspacios($nombre);
        if ($nombre === '' || !validarSoloTexto($nombre)) {
            echo json_encode(['error' => 'El nombre solo puede contener letras y espacios']);
            exit;
        }

        // Validar cargo
        $cargosValidos = ['Coordinador','subcoordinador','Instructor','Pasante','Aprendiz'];
        if (!in_array($cargo, $cargosValidos, true)) {
            echo json_encode(['error' => 'Cargo no válido']);
            exit;
        }

        // Validar correo único
        if ($correo !== $usuarioActual['correo'] && $usuario->obtenerPorCorreo($correo)) {
            echo json_encode(['error' => 'El correo ya está registrado por otro usuario']);
            exit;
        }

        echo json_encode(
            $usuario->actualizar(
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
            )
            ? ['mensaje' => 'Usuario actualizado correctamente']
            : ['error' => 'No se pudo actualizar el usuario']
        );
    break;


    // Find by document number
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

    // Login
    case 'login':
        $data = json_decode(file_get_contents("php://input"), true);

        $correo   = $data['correo'] ?? $_POST['correo'] ?? null;
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

     //Change user status
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
