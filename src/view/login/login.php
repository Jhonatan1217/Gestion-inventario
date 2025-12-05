<?php
// =====================================
// LOGIN PAGE — VERSIÓN PHP SIN REACT
// =====================================

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluye la conexión a la BD y la BASE_URL si ya la defines allí
require_once _DIR_ . '/../../../Config/database.php';

// Si no tienes BASE_URL definida en otro sitio, la calculamos aquí
if (!defined('BASE_URL')) {
    $protocol   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host       = $_SERVER['HTTP_HOST'];
    $script_dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    define('BASE_URL', $protocol . $host . $script_dir); // ej: .../src/view/login/
}

// Si ya está logueado, mandarlo al dashboard (usando el index de la raíz)
if (isset($_SESSION['usuario_id'])) {
    // vamos al index.php de la raíz, con page=dashboard
    header('Location: ' . BASE_URL . '../../../index.php?page=dashboard');
    exit;
}

$loginError = "";

// ------------------------
// PROCESAR LOGIN (POST)
// ------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $loginError = "Por favor ingresa tu correo y contraseña.";
    } else {
        try {
            // Ajusta el nombre de tabla/campos según tu BD
            try {
    // AJUSTA los nombres de columnas a lo que tengas realmente en la tabla
    $sql = "SELECT id_usuario, nombre_completo, correo, password 
            FROM usuarios 
            WHERE correo = :correo 
            LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':correo', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $hash = $user['password']; // columna real de tu tabla

                $passwordOk = false;

                // 1) Si está hasheada con password_hash()
                if (password_verify($password, $hash)) {
                    $passwordOk = true;
                } else {
                    // 2) Si está en texto plano (temporal, si tu BD aún no usa hash)
                    if ($password === $hash) {
                        $passwordOk = true;
                    }
                }

                if ($passwordOk) {
                    // Guardar datos en sesión
                    $_SESSION['usuario_id']     = $user['id_usuario'];
                    $_SESSION['usuario_nombre'] = $user['nombre_completo'];
                    // $_SESSION['usuario_rol']    = $user['rol']; // ← LO QUITAMOS

                    header('Location: ' . BASE_URL . '../../../index.php?page=dashboard');
                    exit;
                } else {
                    $loginError = "Credenciales incorrectas. Verifica tu correo y contraseña.";
                }
            } else {
                $loginError = "Credenciales incorrectas. Verifica tu correo y contraseña.";
            }

        } catch (PDOException $e) {
            $loginError = "Error BD: " . $e->getMessage();
        }
              } catch (PDOException $e) {
            // SOLO PARA DEPURAR: muestra el mensaje real
            $loginError = "Error BD: " . $e->getMessage();
        }

    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login SIGA</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="../../assets/css/globals.css">
  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-background p-4 relative">

  <!-- Fondo degradado -->
  <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-accent/5"></div>

  <!-- Card -->
  <div class="relative w-full max-w-md shadow-xl bg-white rounded-xl border border-gray-200 fade-in">

    <!-- Header -->
    <div class="space-y-4 text-center pb-2 p-6">
      <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-secondary">
        <img src="../../assets/img/logo-sena-blanco.png" alt="logo sena blanco" class="h-8 w-auto object-contain">
      </div>

      <div>
        <h1 class="text-2xl font-bold">Bienvenido a SIGA</h1>
        <p class="mt-1 text-gray-500">Sistema de Gestión de Almacén</p>
      </div>

      <?php if ($loginError !== ""): ?>
        <p class="mt-2 text-sm text-red-600">
          <?= htmlspecialchars($loginError) ?>
        </p>
      <?php endif; ?>
    </div>

    <!-- Form -->
    <div class="pt-4 px-6 pb-6">
      <form id="loginForm" class="space-y-4" method="POST">

        <!-- EMAIL -->
        <div class="space-y-2">
          <label for="email" class="text-sm font-medium">Correo electrónico</label>
          <input
            id="email"
            name="email"
            type="email"
            placeholder="usuario@sena.edu.co"
            required
            class="h-11 w-full border rounded-md px-3"
          />
        </div>

        <!-- PASSWORD -->
        <div class="space-y-2">
          <div class="flex items-center justify-between">
            <label for="password" class="text-sm font-medium">Contraseña</label>

            <a href="/recuperar-contrasena" class="text-xs text-secondary hover:underline">
              ¿Olvidaste tu contraseña?
            </a>
          </div>

          <div class="relative">
            <input
              id="password"
              name="password"
              type="password"
              placeholder="••••••••"
              required
              class="h-11 pr-10 w-full border rounded-md px-3"
            />

            <!-- Botón mostrar/ocultar -->
            <button
              type="button"
              id="togglePassword"
              class="absolute right-0 top-0 h-11 w-11 flex items-center justify-center hover:bg-transparent"
            >
              <!-- ICONO EYE -->
              <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- BOTÓN LOGIN -->
        <button
          type="submit"
          id="btnLogin"
          class="w-full h-11 bg-secondary text-white rounded-md flex items-center justify-center"
        >
          <span id="btnText">Iniciar sesión</span>

          <!-- LOADER -->
          <svg
            id="loaderIcon"
            class="hidden ml-2 h-4 w-4 animate-spin"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
            <path d="M4 12a8 8 0 018-8" />
          </svg>
        </button>

      </form>
    </div>
  </div>

  <!-- ======================== -->
  <!-- JS — MISMA LÓGICA DE REACT -->
  <!-- ======================== -->
  <script>
    const togglePasswordBtn = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");

    togglePasswordBtn.addEventListener("click", () => {
      const isText = passwordInput.type === "text";
      passwordInput.type = isText ? "password" : "text";

      // Cambiar icono
      eyeIcon.innerHTML = isText
        ? <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>
        : <path d="M13.875 18.825A10.05 10.05 0 0112 19c-7 0-11-7-11-7a19.207 19.207 0 015.677-5.48m3.461-.762A11.413 11.413 0 0112 5c7 0 11 7 11 7a20.626 20.626 0 01-2.364 3.442M3 3l18 18"/>;
    });

    // Mantener el loader pero dejar que PHP maneje la redirección
    document.getElementById("loginForm").addEventListener("submit", function () {
      const btn = document.getElementById("btnLogin");
      const loader = document.getElementById("loaderIcon");
      const text = document.getElementById("btnText");

      btn.disabled = true;
      loader.classList.remove("hidden");
      text.textContent = "Iniciando sesión...";
      // PHP se encarga de validar y redirigir.
    });
  </script>

</body>
</html>