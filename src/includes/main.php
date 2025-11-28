<?php
// Página solicitada (por defecto 'nombre_pagina_inicial')
$page = $_GET['page'] ?? 'landing'; // en este caso landing.php proyecto anterior es la pagina principal 

// Evitar rutas maliciosas
$page = basename($page);

// Ruta de la vista
$file = __DIR__ . "/../view/$page.php";

// Cargar vista o mostrar mensaje de error
if (file_exists($file)) {
    include $file;
} else {
    echo "<p style='color:red; text-align:center; padding:2rem;'>
            La página solicitada <strong>$page</strong> no existe.
          </p>";
}
?>
