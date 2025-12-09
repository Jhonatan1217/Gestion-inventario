<?php
// ==========================
// HEADER DASHBOARD – PHP
// ==========================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Si no hay usuario logueado, redirige al login (ajusta la ruta según tu estructura)
if (!isset($_SESSION['usuario_id'])) {
    // Ejemplo de ruta, cámbiala si tu login está en otro sitio
    header('Location: src/view/login/login.php');
    exit;
}

// Armar nombre completo desde la sesión
$nombreSesion   = $_SESSION['usuario_nombre']   ?? '';
$apellidoSesion = $_SESSION['usuario_apellido'] ?? '';
$nombreCompleto = trim($nombreSesion . ' ' . $apellidoSesion);

// Datos del usuario tomados de la sesión
$currentUser = [
    "nombre_completo" => $nombreCompleto !== '' ? $nombreCompleto : "Usuario",
    "cargo"           => $_SESSION['usuario_cargo'] ?? "encargado_inventario",
    "foto_url"        => $_SESSION['usuario_foto']  ?? null, // sin foto → iniciales
];

// Ejemplo de notificaciones mock (puedes luego reemplazar por datos de BD)
$mockAlerts = [
    ["material_id" => 1, "material_nombre" => "Cemento gris",       "stock_actual" => 8, "stock_minimo" => 10],
    ["material_id" => 2, "material_nombre" => "Guantes de carnaza", "stock_actual" => 4, "stock_minimo" => 6],
    ["material_id" => 1, "material_nombre" => "Cemento gris",       "stock_actual" => 8, "stock_minimo" => 10],
    ["material_id" => 2, "material_nombre" => "Guantes de carnaza", "stock_actual" => 4, "stock_minimo" => 6],
    ["material_id" => 1, "material_nombre" => "Cemento gris",       "stock_actual" => 8, "stock_minimo" => 10],
    ["material_id" => 2, "material_nombre" => "Guantes de carnaza", "stock_actual" => 4, "stock_minimo" => 6],
    ["material_id" => 1, "material_nombre" => "Cemento gris",       "stock_actual" => 8, "stock_minimo" => 10],
    ["material_id" => 2, "material_nombre" => "Guantes de carnaza", "stock_actual" => 4, "stock_minimo" => 6],
];

$roleLabels = [
    "coordinador"          => "Coordinador",
    "instructor"           => "Instructor",
    "pasante"              => "Pasante",
    "encargado_inventario" => "Encargado de Inventario",
    "encargado_bodega"     => "Encargado de Bodega",
];

// Iniciales: primer nombre + primer apellido
function getUserInitials(string $nombreCompleto): string {
    $partes = preg_split('/\s+/', trim($nombreCompleto));
    if (!$partes || count($partes) === 0) return '';

    $primerNombre   = $partes[0];
    $primerApellido = $partes[1] ?? $partes[0];

    $iniNombre   = mb_substr($primerNombre,   0, 1, 'UTF-8');
    $iniApellido = mb_substr($primerApellido, 0, 1, 'UTF-8');

    return mb_strtoupper($iniNombre . $iniApellido, 'UTF-8');
}

// Margen según sidebar
$sidebarMarginClass = 'ml-[260px]';
if (isset($collapsed)) {
    $sidebarMarginClass = $collapsed ? 'ml-[70px]' : 'ml-[260px]';
}

// 🔹 NUEVO: bandera para scroll en notificaciones si hay más de 5
$manyAlerts = count($mockAlerts) > 5;
?>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="../assets/css/globals.css">

<header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-border bg-card px-6 transition-all duration-300 <?php echo $sidebarMarginClass; ?>">

  <!-- Buscador estilo pill -->
  <div class="relative flex-1 max-w-xl">
    <div class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2">
      <!-- Icono búsqueda (Lucide) -->
      <i data-lucide="search" class="h-4 w-4 text-slate-400"></i>
    </div>

    <input
      type="search"
      placeholder="Buscar materiales, usuarios, fichas..."
      class="h-11 w-full rounded-xl border border-[#e2e8f0] bg-[#f8fbff] pl-11 pr-4 text-sm text-slate-600 placeholder:text-slate-400 shadow-[0_0_0_1px_rgba(15,23,42,0.02),0_10px_20px_rgba(15,23,42,0.05)] focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/40"
    />
  </div>

  <div class="flex items-center gap-4 ml-4">

    <!-- Notificaciones -->
    <div class="relative group">
      <button class="relative flex h-9 w-9 items-center justify-center rounded-full hover:bg-muted/70 transition">
        <!-- Campana Lucide -->
        <i data-lucide="bell" class="h-5 w-5 text-slate-500"></i>

        <?php if (count($mockAlerts) > 0): ?>
          <span class="absolute right-1.5 top-1.5 h-2.5 w-2.5 rounded-full bg-[#ff4b4b] ring-2 ring-card"></span>
        <?php endif; ?>
      </button>

      <!-- Dropdown notificaciones -->
      <div class="absolute right-0 mt-2 hidden w-80 rounded-md border border-border bg-card shadow-md group-hover:block">
        <div class="flex items-center justify-between px-3 py-2">
          <span class="text-sm font-semibold">Notificaciones</span>
          <span class="rounded-full bg-muted px-2 py-0.5 text-xs">
            <?php echo count($mockAlerts); ?>
          </span>
        </div>
        <hr class="border-border" />

        <?php if (count($mockAlerts) === 0): ?>
          <p class="px-3 py-3 text-xs text-muted-foreground">No hay notificaciones nuevas.</p>
        <?php else: ?>

          <!-- 🔹 NUEVO WRAPPER: hace scroll si hay muchas -->
          <div class="<?php echo $manyAlerts ? 'max-h-60 overflow-y-auto' : ''; ?>">
            <?php foreach ($mockAlerts as $alert): ?>
              <div class="flex flex-col gap-1 px-3 py-2 hover:bg-muted/50">
                <div class="flex items-center gap-2">
                  <span class="h-2 w-2 rounded-full bg-warning"></span>
                  <span class="text-xs font-medium">Stock bajo</span>
                </div>
                <p class="text-xs text-muted-foreground">
                  <?php echo $alert["material_nombre"]; ?>:
                  <?php echo $alert["stock_actual"]; ?>/<?php echo $alert["stock_minimo"]; ?> unidades
                </p>
              </div>
            <?php endforeach; ?>
          </div>
          <!-- 🔹 FIN WRAPPER -->

        <?php endif; ?>
      </div>
    </div>

    <!-- Menú de usuario -->
    <div class="relative group">
      <button class="flex items-center gap-3 rounded-full px-2 py-1.5 h-auto hover:bg-muted/70 transition">

        <!-- Avatar / Iniciales -->
        <div
          class="flex h-9 w-9 items-center justify-center rounded-full overflow-hidden"
          <?php if (empty($currentUser["foto_url"])): ?>
            style="background-color: color-mix(in srgb, var(--secondary) 39%, #ffffff 61%);"
          <?php else: ?>
            style="background-color: transparent;"
          <?php endif; ?>
        >
          <?php if (!empty($currentUser["foto_url"])): ?>
            <img
              src="<?php echo htmlspecialchars($currentUser["foto_url"], ENT_QUOTES, 'UTF-8'); ?>"
              alt="<?php echo htmlspecialchars($currentUser["nombre_completo"], ENT_QUOTES, 'UTF-8'); ?>"
              class="h-full w-full object-cover"
            />
          <?php else: ?>
            <span class="text-xs font-semibold text-primary">
              <?php echo getUserInitials($currentUser["nombre_completo"]); ?>
            </span>
          <?php endif; ?>
        </div>

        <div class="hidden flex-col items-start md:flex">
          <span class="text-sm font-medium text-slate-900">
            <?php
              $parts = preg_split('/\s+/', trim($currentUser["nombre_completo"]));
              $first = $parts[0] ?? '';
              $last  = $parts[1] ?? '';
              echo trim($first . ' ' . $last);
            ?>
          </span>
          <span class="text-xs text-slate-500">
            <?php echo $roleLabels[$currentUser["cargo"]] ?? $currentUser["cargo"]; ?>
          </span>
        </div>

        <!-- Flechita -->
        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-500"></i>
      </button>

      <!-- Dropdown usuario -->
      <div class="absolute right-0 mt-2 hidden w-56 rounded-md border border-border bg-card shadow-md group-hover:block">
        <div class="px-3 py-2">
          <span class="block text-sm font-semibold">Mi cuenta</span>
        </div>
        <hr class="border-border" />

        <button class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-muted/60">
          <i data-lucide="user" class="mr-1 h-4 w-4"></i>
          Perfil
        </button>

        <button class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-muted/60">
          <i data-lucide="settings" class="mr-1 h-4 w-4"></i>
          Editar Perfil
        </button>

        <hr class="border-border" />

        <button class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-destructive hover:bg-red-50">
          <i data-lucide="log-out" class="mr-1 h-4 w-4"></i>
          Cerrar sesión
        </button>
      </div>
    </div>

  </div>
</header>

<!-- Asegúrate de tener Lucide cargado en tu layout principal -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    if (window.lucide && typeof lucide.createIcons === "function") {
      lucide.createIcons();
    }
  });
</script>
