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

//        
//         case 'bodegas':
//             include_once __DIR__ . "/src/controllers/bodega_controller.php";

//          
//             $controller = new BodegaController($conn);

//             switch ($accion) {

//                 case 'listar':
//                     $controller->listar();
//                     break;

//                 case 'obtener':
//                     $controller->obtener($_GET['id_bodega'] ?? null);
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
//                     echo json_encode(['error' => 'Acción inválida en el módulo bodegas']);
//             }
//             break;

//        
//         default:
//             echo json_encode(['error' => 'Módulo inválido']);
//     }

//     exit;
// }


// if (isset($_GET['accion']) && isset($_GET['modulo'])) {

//     include_once __DIR__ . "/Config/database.php";
//     $modulo = $_GET['modulo'];
//     $accion = $_GET['accion'];

//     switch ($modulo) {


//         case 'raes':
//             include_once __DIR__ . "/src/controllers/rae_controller.php";

//             $controller = new rae_controller($conn);

//             switch ($accion) {

//                 case 'listar':
//                     $controller->listar();
//                     break;

//                 case 'obtener':
//                     $controller->obtener($_GET['id_rae'] ?? null);
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
//                     echo json_encode(['error' => 'Acción inválida en el módulo RAEs']);
//             }
//             break;

//         default:
//             echo json_encode(['error' => 'Módulo inválido']);
//             break;

//     } 
// } 

if (isset($_GET['accion']) && isset($_GET['modulo']) && $_GET['modulo'] === 'programa') {

    header('Content-Type: application/json; charset=utf-8');

    include_once __DIR__ . "/src/controllers/programa_controller.php";
    include_once __DIR__ . "/Config/database.php";

    $model = new Programa($conn);
    $accion = $_GET['accion'] ?? '';

    switch ($accion) {
        case 'listar':
            echo json_encode($model->listar());
            break;

        case 'obtener':
            $id = $_GET['id_programa'] ?? null;
            echo json_encode($model->obtenerPorId($id) ?: ['error'=>'Programa no encontrado']);
            break;

        case 'crear':
            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data) {
                echo json_encode(['error'=>'No se recibió JSON válido']);
                exit;
            }

            echo json_encode(
                $model->crear(
                    $data['codigo_programa'],
                    $data['nombre_programa'],
                    $data['nivel_programa'],
                    $data['descripcion_programa'],
                    (int)$data['duracion_horas'],
                    $data['estado']
                )
                ? ['mensaje'=>'Programa creado correctamente']
                : ['error'=>'No se pudo crear el programa']
            );
            break;

        case 'actualizar':
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $_GET['id_programa'] ?? $data['id_programa'] ?? null;

            echo json_encode(
                $model->actualizar(
                    $id,
                    $data['codigo_programa'],
                    $data['nombre_programa'],
                    $data['nivel_programa'],
                    $data['descripcion_programa'],
                    (int)$data['duracion_horas'],
                    $data['estado']
                )
                ? ['mensaje'=>'Programa actualizado correctamente']
                : ['error'=>'No se pudo actualizar']
            );
            break;

        case 'eliminar':
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $_GET['id_programa'] ?? $data['id_programa'] ?? null;

            echo json_encode(
                $model->eliminar($id)
                ? ['mensaje'=>'Programa eliminado correctamente']
                : ['error'=>'No se pudo eliminar']
            );
            break;

        case 'cambiar_estado':
            $data = json_decode(file_get_contents("php://input"), true);
            echo json_encode(
                $model->cambiarEstado(
                    $data['id_programa'],
                    $data['estado']
                )
                ? ['mensaje'=>'Estado actualizado correctamente']
                : ['error'=>'No se pudo actualizar estado']
            );
            break;

        default:
            echo json_encode(['error'=>'Acción inválida']);
            break;
    }
    exit;
}


error_reporting(E_ALL);
ini_set('display_errors', 1);
define('ACCESO_PERMITIDO', true);

// Ruta base del proyecto
define('BASE_PATH', __DIR__);

// Base URL dinámica
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$script_dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
define('BASE_URL', $protocol . $host . $script_dir);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestion Inventario</title>
    <link rel="icon" type="image/png" href="">
    <!-- <link rel="stylesheet" href="./public/css/output.css"> -->
</head>
<body class="flex flex-col min-h-screen font-sans bg-white text-gray-900">
    <header>
        <?php require_once BASE_PATH . '/src/includes/header.php'; ?>
    </header>

    <main class="flex-grow">
        <?php require_once BASE_PATH . '/src/includes/main.php'; ?>
    </main>

    <footer>
        <?php require_once BASE_PATH . '/src/includes/footer.php'; ?>
    </footer>
</body>
</html>
