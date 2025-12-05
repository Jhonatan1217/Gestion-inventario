
<?php
// Si viene 'accion' ignoramos el router de páginas y usamos API  (SOLO PARA PRUEBAS Y modificar dependiendo del controlador)
// if (isset($_GET['accion'])) {
//     include_once __DIR__ . "/Config/database.php";
//     include_once __DIR__ . "/src/controllers/usuario_controller.php";
//     $controller = new usuario($conn);

//     switch ($_GET['accion']) {
//         case 'listar':
//             $controller->listar();
//             break;
//         case 'obtener':
//             $controller->obtener($_GET['id_usuario'] ?? null);
//             break;
//         case 'crear':
//             $controller->crear();
//             break;
//         case 'actualizar':
//             $controller->actualizar($_GET['id_usuario'] ?? null);
//             break;
//         case 'eliminar':
//             $controller->eliminar($_GET['id_usuario'] ?? null);
//             break;
//         case 'cambiar_estado':
//             $controller->cambiarEstado();
//             break;
//         default:
//             echo json_encode(['error'=>'Acción inválida']);
//     }
//     exit; 
// }


// if (isset($_GET['accion']) && isset($_GET['modulo'])) {

//     include_once __DIR__ . "/Config/database.php";
//     $modulo = $_GET['modulo'];
//     $accion = $_GET['accion'];

//     switch ($modulo) {

       
//         case 'movimiento':
//             include_once __DIR__ . "/src/controllers/movimiento_controller.php";

         
//             $controller = new MovimientoModel($conn);

//             switch ($accion) {

//                 case 'listar':
//                     $controller->listar();
//                     break;

//                 case 'obtener':
//                     $controller->obtener($_GET['id_movimiento'] ?? null);
//                     break;

//                 case 'crear':
//                     $controller->crear();
//                     break;

//                 case 'actualizar':
//                     $controller->actualizar();
//                     break;

//                 case 'activar':
//                     $controller->activar();
//                     break;

//                 case 'inactivar':
//                     $controller->inactivar();
//                     break;

//                 default:
//                     echo json_encode(['error' => 'Acción inválida en el módulo movimiento']);
//             }
//             break;

       
//         default:
//             echo json_encode(['error' => 'Módulo movimiento']);
//     }

//     exit;
// }
 


// if (isset($_GET['accion']) && isset($_GET['modulo']) && $_GET['modulo'] === 'movimiento') {

//     header('Content-Type: application/json; charset=utf-8');

//     include_once __DIR__ . "/src/controllers/movimiento_controller.php";
//     include_once __DIR__ . "/Config/database.php";

//     $model = new MovimientoModel($conn);
//     $accion = $_GET['accion'] ?? '';

//     switch ($accion) {
//         case 'listar':
//             echo json_encode($model->listar());
//             break;

//         case 'obtener':
//             $id = $_GET['id_movimiento'] ?? null;
//             echo json_encode($model->obtenerPorId($id) ?: ['error'=>'Movimiento no encontrado']);
//             break;

//         case 'crear':
//             $data = json_decode(file_get_contents("php://input"), true);
//             if (!$data) {
//                 echo json_encode(['error'=>'No se recibió JSON válido']);
//                 exit;
//             }

//             echo json_encode(
//                 $model->crear(
//                     $data['tipo_movimiento'],
//                     $data['fecha_hora'],
//                     $data['id_usuario'],
//                     (int)$data['id_material'],
//                     (int)$data['id_bodega'],
//                     isset($data['id_subbodega']) ? (int)$data['id_subbodega'] : null,
//                     (int)$data['cantidad'],
//                     isset($data['id_programa']) ? (int)$data['id_programa'] : null,
//                     isset($data['id_ficha']) ? (int)$data['id_ficha'] : null,
//                     isset($data['id_rae']) ? (int)$data['id_rae'] : null,
//                     $data['observaciones'] ?? null,
//                     isset($data['id_solicitud']) ? (int)$data['id_solicitud'] : null
//                 )
//                 ? ['mensaje'=>'Movimiento creado correctamente']
//                 : ['error'=>'No se pudo crear el movimiento']
//             );
//             break;

//         case 'actualizar':
//             $data = json_decode(file_get_contents("php://input"), true);
//             $id = $_GET['id_movimiento'] ?? $data['id_movimiento'] ?? null;

//             echo json_encode(
//                 $model->actualizar(
//                     $id,
//                     $data['tipo_movimiento'],
//                     $data['fecha_hora'],
//                     $data['id_usuario'],
//                     (int)$data['id_material'],
//                     (int)$data['id_bodega'],
//                     isset($data['id_subbodega']) ? (int)$data['id_subbodega'] : null,
//                     (int)$data['cantidad'],
//                     isset($data['id_programa']) ? (int)$data['id_programa'] : null,
//                     isset($data['id_ficha']) ? (int)$data['id_ficha'] : null,
//                     isset($data['id_rae']) ? (int)$data['id_rae'] : null,
//                     $data['observaciones'] ?? null,
//                     isset($data['id_solicitud']) ? (int)$data['id_solicitud'] : null
//                 )
//                 ? ['mensaje'=>'Movimiento actualizado correctamente']
//                 : ['error'=>'No se pudo actualizar']
//             );
//             break;

//         case 'eliminar':
//             $data = json_decode(file_get_contents("php://input"), true);
//             $id = $_GET['id_movimiento'] ?? $data['id_movimiento'] ?? null;

//             echo json_encode(
//                 $model->eliminar($id)
//                 ? ['mensaje'=>'Movimiento eliminado correctamente']
//                 : ['error'=>'No se pudo eliminar']
//             );
//             break;

//         default:
//             echo json_encode(['error'=>'Acción inválida']);
//             break;
//     }
//     exit;
// }
// $accion = $_GET['accion'] ?? '';
// $modulo = $_GET['modulo'] ?? '';

// if (isset($_GET["modulo"]) && $_GET["modulo"] === "usuario_roles") {
//     include_once __DIR__ . "/src/controllers/rol_usuario_controller.php";
//     include_once __DIR__ . "/Config/database.php";
//     exit;
// }

// if (isset($_GET['accion']) && isset($_GET['modulo']) && $_GET['modulo'] === 'bodega') {

//    header('Content-Type: application/json; charset=utf-8');

//     include_once __DIR__ . "/src/controllers/bodega_controller.php";
//     include_once __DIR__ . "/Config/database.php";

//     $model = new BodegaModel($conn);
//     $accion = $_GET['accion'] ?? '';

//     switch ($accion) {
//         case 'listar':
//             echo json_encode($model->listar());
//             break;

//         case 'obtener':
//             $id = $_GET['id_bodega'] ?? null;
//             echo json_encode($model->obtenerPorId($id) ?: ['error'=>'Bodega no encontrada']);
//             break;

//         case 'crear':
//             $data = json_decode(file_get_contents("php://input"), true);
//             if (!$data) {
//                 echo json_encode(['error'=>'No se recibió JSON válido']);
//                 exit;
//             }

//             echo json_encode(
//                 $model->crear(
//                     $data['codigo_bodega'],
//                     $data['nombre'],
//                     $data['ubicacion'],
//                     $data['estado']
//                 )
//                 ? ['mensaje'=>'Bodega creada correctamente']
//                 : ['error'=>'No se pudo crear la bodega']
//             );
//             break;

//         case 'actualizar':
//             $data = json_decode(file_get_contents("php://input"), true);
//             $id = $_GET['id_bodega'] ?? $data['id_bodega'] ?? null;

//             echo json_encode(
//                 $model->actualizar(
//                     $id,
//                     $data['codigo_bodega'],
//                     $data['nombre'],
//                     $data['ubicacion'],
//                     $data['estado']
//                 )
//                 ? ['mensaje'=>'Bodega actualizada correctamente']
//                 : ['error'=>'No se pudo actualizar']
//             );
//             break;

//         case 'eliminar':
//             $data = json_decode(file_get_contents("php://input"), true);
//             $id = $_GET['id_bodega'] ?? $data['id_bodega'] ?? null;

//             echo json_encode(
//                 $model->eliminar($id)
//                 ? ['mensaje'=>'Bodega eliminada correctamente']
//                 : ['error'=>'No se pudo eliminar']
//             );
//             break;

//         case 'cambiar_estado':
//             $data = json_decode(file_get_contents("php://input"), true);
//             echo json_encode(
//                 $model->cambiarEstado(
//                     $data['id_bodega'],
//                     $data['estado']
//                 )
//                 ? ['mensaje'=>'Estado actualizado correctamente']
//                 : ['error'=>'No se pudo actualizar estado']
//             );
//             break;

//         default:
//             echo json_encode(['error'=>'Acción inválida']);
//             break;
//     }
//     exit;
// }
// if (isset($_GET['accion'])&& $_GET['accion'] === 'raes') {

//     header('Content-Type: application/json; charset=utf-8');

//     include_once __DIR__ . "/src/controllers/rae_controller.php";
//     include_once __DIR__ . "/Config/database.php";

//     $model = new RaeModel($conn);
//     $accion = $_GET['accion'] ?? '';

//     switch ($accion) {
//         case 'listar':
//             echo json_encode($model->listar());
//             break;

//         case 'obtener':
//             $id = $_GET['id_rae'] ?? null;
//             echo json_encode($model->obtenerPorId($id) ?: ['error'=>'RAE no encontrado']);
//             break;

//         case 'crear':
//             $data = json_decode(file_get_contents("php://input"), true);
//             if (!$data) {
//                 echo json_encode(['error'=>'No se recibió JSON válido']);
//                 exit;
//             }

//             echo json_encode(
//                 $model->crear(
//                     $data['codigo_rae'],
//                     $data['nombre_rae'],
//                     $data['descripcion'],
//                     (int)$data['id_ficha'],
//                     $data['fecha_inicio'],
//                     $data['fecha_fin'],
//                     $data['estado']
//                 )
//                 ? ['mensaje'=>'RAE creado correctamente']
//                 : ['error'=>'No se pudo crear el RAE']
//             );
//             break;

//         case 'actualizar':
//             $data = json_decode(file_get_contents("php://input"), true);
//             $id = $_GET['id_rae'] ?? $data['id_rae'] ?? null;

//             echo json_encode(
//                 $model->actualizar(
//                     $id,
//                     $data['codigo_rae'],
//                     $data['nombre_rae'],
//                     $data['descripcion'],
//                     (int)$data['id_ficha'],
//                     $data['fecha_inicio'],
//                     $data['fecha_fin'],
//                     $data['estado']
//                 )
//                 ? ['mensaje'=>'RAE actualizado correctamente']
//                 : ['error'=>'No se pudo actualizar']
//             );
//             break;

//         case 'eliminar':
//             $data = json_decode(file_get_contents("php://input"), true);
//             $id = $_GET['id_rae'] ?? $data['id_rae'] ?? null;

//             echo json_encode(
//                 $model->eliminar($id)
//                 ? ['mensaje'=>'RAE eliminado correctamente']
//                 : ['error'=>'No se pudo eliminar']
//             );
//             break;

//         case 'cambiar_estado':
//             $data = json_decode(file_get_contents("php://input"), true);
//             echo json_encode(
//                 $model->cambiarEstado(
//                     $data['id_rae'],
//                     $data['estado']
//                 )
//                 ? ['mensaje'=>'Estado actualizado correctamente']
//                 : ['error'=>'No se pudo actualizar estado']
//             );
//             break;

//         case 'listar_por_ficha':
//             $id_ficha = $_GET['id_ficha'] ?? null;
//             if (!$id_ficha) {
//                 echo json_encode(['error'=>'Se requiere id_ficha']);
//                 exit;
//             }
//             echo json_encode($model->listarPorFicha($id_ficha));
//             break;

//         default:
//             echo json_encode(['error'=>'Acción inválida']);
//             break;
//     }
//     exit;
// }


// echo '<h1>Conexion activa</h1>';

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('ACCESO_PERMITIDO', true);

session_start();

// Ruta base del proyecto
define('BASE_PATH', __DIR__);

// Base URL dinámica
$protocol   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host       = $_SERVER['HTTP_HOST'];
$script_dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
define('BASE_URL', $protocol . $host . $script_dir);

// =============================
// LÓGICA DE PÁGINA ACTUAL
// =============================

// Página solicitada (por defecto 'landing')
$page = $_GET['page'] ?? 'landing';
$page = basename($page); // sanitizar

// 1) Si es la LANDING → mostrar solo landing.php sin header/sidebar/footer
if ($page === 'landing') {
    $landingFile = BASE_PATH . "/src/view/landing.php"; // ajusta ruta si tu vista está en otro lado

    if (file_exists($landingFile)) {
        include $landingFile;
    } else {
        echo "<p style='color:red; text-align:center; padding:2rem;'>
                No se encontró la vista <strong>landing.php</strong>.
              </p>";
    }
    exit; // importante: no seguir renderizando el layout
}

// 2) A PARTIR DE AQUÍ, TODAS LAS PÁGINAS SON PROTEGIDAS
//    Si NO hay sesión → mandar al login (que puede ser tu login.php que me pasaste)

if (!isset($_SESSION['usuario_id'])) {
    // Ajusta la ruta según dónde tengas el login
    header('Location: ' . BASE_URL . 'src/view/auth/login.php');
    exit;
}
?>