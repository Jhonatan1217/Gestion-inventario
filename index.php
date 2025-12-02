<?php
// Si viene 'accion' ignoramos el router de páginas y usamos API  (SOLO PARA PRUEBAS Y modificar dependiendo del controlador)
if (isset($_GET['accion'])) {
    include_once __DIR__ . "/Config/database.php";
    include_once __DIR__ . "/src/controllers/usuario_controller.php";
    $controller = new usuario($conn);

    switch ($_GET['accion']) {
        case 'listar':
            $controller->listar();
            break;
        case 'obtener':
            $controller->obtener($_GET['id_usuario'] ?? null);
            break;
        case 'crear':
            $controller->crear();
            break;
        case 'actualizar':
            $controller->actualizar($_GET['id_usuario'] ?? null);
            break;
        case 'eliminar':
            $controller->eliminar($_GET['id_usuario'] ?? null);
            break;
        case 'cambiar_estado':
            $controller->cambiarEstado();
            break;
        default:
            echo json_encode(['error'=>'Acción inválida']);
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
