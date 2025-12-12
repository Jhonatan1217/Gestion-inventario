<?php
// ==========================
// HEADER – PHP
// ==========================

$collapsed = isset($_GET["coll"]) && $_GET["coll"] == "1";
$sidebarWidth = $collapsed ? "70px" : "260px";

// Datos del usuario (de ejemplo)
$currentUser = [
    "nombre_completo" => "Ana María López",
    "cargo"           => "encargado_inventario",
    "foto_url"        => null,
];

$mockAlerts = [
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

// Iniciales (primer nombre + primer apellido)
function getUserInitials(string $nombreCompleto): string {
    $partes = preg_split('/\s+/', trim($nombreCompleto));
    if (!$partes || count($partes) === 0) return '';
    $ini1 = mb_substr($partes[0], 0, 1, 'UTF-8');
    $ini2 = mb_substr($partes[1] ?? $partes[0], 0, 1, 'UTF-8');
    return mb_strtoupper($ini1 . $ini2, 'UTF-8');
}

// Margen según sidebar
$sidebarMarginClass = isset($collapsed) ? ($collapsed ? 'ml-[70px]' : 'ml-[260px]') : 'ml-[260px]';

// Activar scroll en notificaciones si hay muchas
$manyAlerts = count($mockAlerts) > 5;
?>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="src/assets/css/globals.css">

<header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-border bg-card px-6 transition-all duration-300 <?php echo $sidebarMarginClass; ?>">

  <!-- Buscador -->
  <div class="relative flex-1 max-w-xl">
    <div class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2">
      <i data-lucide="search" class="h-4 w-4 text-slate-400"></i>
    </div>

    <input
      type="search"
      placeholder="Buscar materiales, usuarios, fichas..."
      class="h-11 w-full rounded-xl border border-[#e2e8f0] bg-[#f8fbff] pl-11 pr-4 text-sm text-slate-600 shadow-[0_0_0_1px_rgba(15,23,42,0.02),0_10px_20px_rgba(15,23,42,0.05)] focus:outline-none focus:ring-2 focus:ring-primary/40"
    />
  </div>

  <div class="flex items-center gap-4 ml-4">

    <!-- Notificaciones -->
    <div class="relative group">
      <button class="relative flex h-9 w-9 items-center justify-center rounded-full hover:bg-muted/70 transition">
        <i data-lucide="bell" class="h-5 w-5 text-slate-500"></i>
      </button>

      <div class="absolute right-0 mt-2 hidden w-80 rounded-md border border-border bg-card shadow-md group-hover:block">
        <div class="flex items-center justify-between px-3 py-2">
          <span class="text-sm font-semibold">Notificaciones</span>
          <span class="rounded-full bg-muted px-2 py-0.5 text-xs"><?php echo count($mockAlerts); ?></span>
        </div>

        <hr class="border-border" />

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
      </div>
    </div>

    <!-- Usuario -->
    <div class="relative group">

      <!-- Botón del usuario -->
      <button class="flex items-center gap-3 rounded-full px-2 py-1.5 hover:bg-muted/70">

        <!-- Avatar -->
        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-white shadow-md text-xs font-semibold">
          <?php echo getUserInitials($currentUser["nombre_completo"]); ?>
        </div>

        <!-- Nombre + rol -->
        <div class="hidden md:flex flex-col items-start">
          <span class="text-sm font-medium text-slate-900">
            <?php echo explode(" ", $currentUser["nombre_completo"])[0]; ?>
          </span>
          <span class="text-xs text-slate-500">
            <?php echo $roleLabels[$currentUser["cargo"]]; ?>
          </span>
        </div>

        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-500"></i>
      </button>

      <!-- ================================================================= -->
      <!-- DROPDOWN USUARIO -->
      <!-- ================================================================= -->
      <div class="absolute right-0 mt-2 hidden w-56 rounded-md border border-border bg-card shadow-md group-hover:block">
        <div class="px-3 py-2">
          <span class="block text-sm font-semibold">Mi cuenta</span>
        </div>

        <hr class="border-border" />

        <!-- Botón Perfil -->
        <button id="btnPerfil" class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-green-50">
          <i data-lucide="user" class="mr-1 h-4 w-4"></i> Perfil
        </button> 

        <!-- Botón Editar Perfil -->
        <button id="btnEditarPerfil" class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-green-50">
          <i data-lucide="settings" class="mr-1 h-4 w-4"></i> Editar Perfil
        </button>

        <hr class="border-border" />

        <button class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-destructive hover:bg-red-50">
          <i data-lucide="log-out" class="mr-1 h-4 w-4"></i> Cerrar sesión
        </button>
      </div>

    </div>

  </div>
</header>


<!-- ======================= -->
<!-- POPUP — EDITAR PERFIL -->
<!-- ======================= -->
<div id="modalEditarPerfil" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-[620px] max-h-[85vh] overflow-y-auto relative px-10 py-8">

        <!-- Cerrar -->
        <button id="cerrarEditarPerfil" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
            <i data-lucide="x"></i>
        </button>

        <!-- Cambiar avatar y contraseña -->
        <div class="w-full flex flex-col items-center gap-3 mb-10"></div>

        <h2 class="text-2xl font-semibold text-center mb-6">Editar Perfil</h2>

        <div class="w-full flex flex-col items-center gap-3 mb-10">

            <div class="flex items-center justify-center gap-6">

                <!-- Avatar pequeño -->
                <div id="avatarClick"
                    class="w-16 h-16 rounded-full bg-primary text-white shadow-md flex items-center justify-center
                          text-lg font-semibold cursor-pointer bg-cover bg-center">
                    <?php echo getUserInitials($currentUser["nombre_completo"]); ?>
                </div>

                <!-- Botón pequeño pill -->
                <button id="abrirCambioPass" type="button"
                    class="px-4 py-1.5 bg-primary/10 text-primary border border-primary 
                          rounded-full text-sm font-medium hover:bg-primary/20 transition">
                    Cambiar contraseña
                </button>

            </div>

            <!-- Texto descriptivo -->
            <span class="text-xs text-gray-500 text-center">
                Click en la foto para subir nueva imagen (JPG/PNG máx 2MB)
            </span>

            <!-- Input oculto -->
            <input type="file" id="inputFoto" class="hidden" accept="image/png, image/jpeg">
        </div>
        <!-- ============================================================ -->

        <!-- FORMULARIO -->
        <form class="space-y-4">

            <div>
                <label class="text-sm font-medium">Nombre completo*</label>
                <input type="text" class="mt-1 w-full border border-gray-300 rounded-md p-2 h-11"
                    value="<?php echo $currentUser["nombre_completo"]; ?>">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Tipo de documento*</label>
                    <select class="mt-1 w-full border border-gray-300 rounded-md p-2 h-11">
                        <option>Cédula de ciudadanía</option>
                        <option>Tarjeta de identidad</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Número de documento*</label>
                    <input type="text" class="mt-1 w-full border border-gray-300 rounded-md p-2 h-11"
                        value="1098765432">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Teléfono*</label>
                    <input type="text" class="mt-1 w-full border border-gray-300 rounded-md p-2 h-11"
                        value="3101234567">
                </div>

                <div>
                    <label class="text-sm font-medium">Correo electrónico*</label>
                    <input type="email" class="mt-1 w-full border border-gray-300 rounded-md p-2 h-11"
                        value="usuario@sena.edu.co">
                </div>
            </div>

            <div>
                <label class="text-sm font-medium">Dirección*</label>
                <input type="text" class="mt-1 w-full border border-gray-300 rounded-md p-2 h-11"
                    value="Calle 45 #23-10, Bogotá">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" id="cancelarEditarPerfil"
                    class="px-4 py-2 bg-gray-200 rounded-md h-11">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-primary text-white rounded-md h-11">
                    Guardar cambios
                </button>
            </div>

        </form>

    </div>
</div>


<!-- =============================== -->
<!-- MODAL CAMBIAR CONTRASEÑA -->
<!-- =============================== -->
<div id="modalPassword" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-[420px] p-6 relative">

        <button id="cerrarPassword" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
            <i data-lucide="x"></i>
        </button>

        <h2 class="text-xl font-semibold text-center mb-4">Cambiar contraseña</h2>

        <form class="space-y-4">

            <div>
                <label class="text-sm font-medium">Nueva contraseña*</label>
                <input type="password" class="mt-1 w-full border border-gray-300 rounded-md p-2 h-11">
            </div>

            <div>
                <label class="text-sm font-medium">Confirmar contraseña*</label>
                <input type="password" class="mt-1 w-full border border-gray-300 rounded-md p-2 h-11">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" id="cancelarPassword"
                    class="px-4 py-2 bg-gray-200 rounded-md h-11">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-primary text-white rounded-md h-11">
                    Guardar
                </button>
            </div>

        </form>

    </div>
</div>

<!-- LUCIDE -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  lucide.createIcons();
});
</script>

<!-- JS DE PERFIL -->
<script src="/Gestion-inventario/src/assets/js/perfil/perfil.js" defer></script>
