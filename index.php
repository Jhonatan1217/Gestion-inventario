<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('ACCESO_PERMITIDO', true);

session_start();

// =============================
// CONFIGURACIÓN BASE
// =============================

// Ruta base del proyecto (carpeta GESTION_INVENTARIO / Gestion-inventario)
define('BASE_PATH', __DIR__);

// Base URL dinámica
$protocol   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host       = $_SERVER['HTTP_HOST'];
$script_dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
define('BASE_URL', $protocol . $host . $script_dir);

// URL base para los assets
define('ASSETS_URL', BASE_URL . "src/assets/");

// 🔐 Nombre de la clave de sesión donde guardas el ID del usuario
//   AJÚSTALO al nombre REAL que uses en login.php
$SESSION_USER_KEY = 'usuario_id';  // si en tu login usaste 'id_usuario', cambia esto

// =============================
// LÓGICA DE PÁGINA ACTUAL
// =============================

$page = $_GET['page'] ?? 'landing';
$page = basename($page); // sanitizar

// Si el usuario YA está logueado y pide la landing,
// lo mandamos al dashboard (o la página que quieras como inicio logueado)
if (isset($_SESSION[$SESSION_USER_KEY]) && $page === 'landing') {
    header('Location: ' . BASE_URL . 'index.php?page=dashboard');
    exit;
}

// 1) LANDING PÚBLICA (sin header/sidebar)
if ($page === 'landing') {
    $landingFile = BASE_PATH . "/src/view/landing.php";

    if (file_exists($landingFile)) {
        include $landingFile;
    } else {
        echo "<p style='color:red; text-align:center; padding:2rem;'>
                No se encontró la vista <strong>landing.php</strong>.
            </p>";
    }
    exit;
}

// 2) PÁGINAS PROTEGIDAS → si no hay sesión, mandar al login
if (!isset($_SESSION[$SESSION_USER_KEY])) {
    // login REAL según tu árbol: src/view/login/login.php
    header('Location: ' . BASE_URL . 'src/view/login/login.php');
    exit;
}
?>
<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Inventario</title>
    <link rel="icon" type="image/png" href="">
    <link rel="stylesheet" href="src/assets/css/globals.css">
    <!-- aquí metes tu CSS/Tailwind si no lo haces en los includes -->
</head>
<body class="flex flex-col min-h-screen font-sans bg-white text-gray-900 transition-all duration-300">
    <header>
        <?php require_once BASE_PATH . '/src/includes/header.php'; ?>
        <?php require_once BASE_PATH . '/src/includes/sidebar.php'; ?>
    </header>

    <main class="flex-grow">
        <?php require_once BASE_PATH . '/src/includes/main.php'; ?>
    </main>

    <script>
        const BASE_URL = "<?= BASE_URL ?>";
    </script>
</body>
</html>
