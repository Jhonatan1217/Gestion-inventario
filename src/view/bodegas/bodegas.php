<?php
$collapsed = isset($_GET["coll"]) && $_GET["coll"] == "1";
$sidebarWidth = $collapsed ? "70px" : "260px";

$bodegas = [
    [1, "Bodega Principal - Eléctrico", "Eléctrico", "Bloque A, Piso 1", "Bodega", "Activo", 1],
    [2, "Bodega Construcción", "Construcción", "Bloque A, Piso 2", "Bodega", "Activo", 3],
    [3, "Sub-bodega Sanitario", "Sanitario", "Bloque B, Piso 1", "Sub-Bodega", "Activo", 4],
    [4, "Bodega Herramientas", "Herramientas", "Bloque C, Piso 1", "Bodega", "Activo", 2],
    [5, "Ejemplo", "Ejemplo", "Bloque A, Piso 3", "Bodega", "Inactivo", 1],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestión Bodegas</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>

  <link rel="stylesheet" href="../../assets/css/globals.css" />
  <script src="../../assets/js/bodegas.js" defer></script>
</head>

<body class="bg-gray-50 text-gray-900">
<main class="p-6 transition-all duration-300"
  style="margin-left: <?= isset($_GET['coll']) && $_GET['coll'] == "1" ? '70px' : '260px' ?>;"
>

  <!-- HEADER: TÍTULO (izq) + BOTONES (der) -->
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
  <div>
    <h1 class="text-3xl font-bold">Gestión Bodegas</h1>
    <p class="text-sm text-gray-500">
      Administra las bodegas y sub-bodegas del inventario
    </p>
  </div>

  <!-- Botones al frente del título (arriba a la derecha) -->
  <div class="flex items-center gap-3">
    <!-- Switch Lista/Grid -->
    <div class="inline-flex rounded-lg border border-border bg-card shadow-sm overflow-hidden">
      <button
        type="button"
        id="btnVistaTabla"
        class="px-3 py-2 text-xs sm:text-sm flex items-center gap-1 bg-muted text-foreground"
      >
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
          viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

      <button
        type="button"
        id="btnVistaTarjetas"
        class="px-3 py-2 text-xs sm:text-sm flex items-center gap-1 text-muted-foreground"
      >
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
          viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <rect x="4" y="4" width="7" height="7" rx="1"></rect>
          <rect x="13" y="4" width="7" height="7" rx="1"></rect>
          <rect x="4" y="13" width="7" height="7" rx="1"></rect>
          <rect x="13" y="13" width="7" height="7" rx="1"></rect>
        </svg>
      </button>
    </div>

    <!-- Nueva Bodega -->
    <button
      id="btnNuevaBodega"
      type="button"
      class="inline-flex items-center justify-center rounded-md bg-secondary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm hover:opacity-90 gap-2"
    >
      <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
        viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
      </svg>
      Nueva Bodega
    </button>
  </div>
</div>

<!-- CONTROLES (abajo): BUSCADOR (izq) + FILTRO (der) -->
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
  <!-- Buscador -->
  <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-2xl px-4 py-2 shadow-sm w-full sm:w-[360px] bg-gray-100">
    <i data-lucide="search" class="w-4 h-4 text-gray-500"></i>
    <input
      type="text"
      placeholder="Buscar por nombre o ID"
      class="w-full bg-transparent outline-none text-sm"
    />
  </div>

  <!-- Filtro -->
  <div class="flex items-center gap-2 sm:justify-end">
    <i data-lucide="filter" class="w-4 h-4 text-gray-600"></i>

    <div class="relative bg-white border border-gray-200 rounded-2xl px-4 py-2 shadow-sm min-w-[160px]">
      <select
        id="bodegasFilter"
        class="w-full appearance-none bg-transparent outline-none text-sm text-gray-700 pr-6"
      >
        <option value="Todos">Todos</option>
        <option value="Activo">Activos</option>
        <option value="Inactivo">Inactivos</option>
      </select>
      <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
    </div>
  </div>
</div>


  <!-- ========= VISTA LISTA ========= -->
  <div id="view-list">
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-100 text-gray-600">
          <tr>
            <th class="px-4 py-3 text-left font-semibold">ID</th>
            <th class="px-4 py-3 text-left font-semibold">Nombre</th>
            <th class="px-4 py-3 text-left font-semibold">Clasificación</th>
            <th class="px-4 py-3 text-left font-semibold">Ubicación</th>
            <th class="px-4 py-3 text-left font-semibold">Tipo</th>
            <th class="px-4 py-3 text-left font-semibold">Estado</th>
            <th class="px-4 py-3 text-left font-semibold">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
          <?php foreach ($bodegas as $b): ?>
            <?php $activo = $b[5] === "Activo"; ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">#<?= $b[0] ?></td>

              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="warehouse" class="w-4 h-4"></i>
                  </div>
                  <span class="font-medium text-gray-900"><?= htmlspecialchars($b[1]) ?></span>
                </div>
              </td>

              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700">
                  <?= htmlspecialchars($b[2]) ?>
                </span>
              </td>

              <td class="px-4 py-3 text-gray-700">
                <i data-lucide="map-pin" class="w-4 h-4 inline-block text-gray-500 mr-1"></i>
                <?= htmlspecialchars($b[3]) ?>
              </td>

              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700">
                  <?= htmlspecialchars($b[4]) ?>
                </span>
              </td>

              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                  <?= $activo ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100' ?>">
                  <?= htmlspecialchars($b[5]) ?>
                </span>
              </td>

              <td class="px-4 py-3">
                <button
                  class="w-8 h-8 rounded-full flex items-center justify-center bodegas-btn-dots"
                  data-id="<?= $b[0] ?>"
                  data-nombre="<?= htmlspecialchars($b[1]) ?>"
                  data-clasificacion="<?= htmlspecialchars($b[2]) ?>"
                  data-ubicacion="<?= htmlspecialchars($b[3]) ?>"
                  data-tipo="<?= htmlspecialchars($b[4]) ?>"
                  data-estado="<?= htmlspecialchars($b[5]) ?>"
                >
                  <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ========= VISTA GRID ========= -->
  <div id="view-grid" class="hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

      <?php foreach ($bodegas as $b):
        $estadoActivo = $b[5] === "Activo";
      ?>
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 flex flex-col gap-3 relative">

          <!-- Header -->
          <div class="flex items-start justify-between gap-4">
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
              <i data-lucide="warehouse" class="w-5 h-5"></i>
            </div>

            <button
              class="w-8 h-8 rounded-full flex items-center justify-center bodegas-btn-dots"
              data-id="<?= $b[0] ?>"
              data-nombre="<?= htmlspecialchars($b[1]) ?>"
              data-clasificacion="<?= htmlspecialchars($b[2]) ?>"
              data-ubicacion="<?= htmlspecialchars($b[3]) ?>"
              data-tipo="<?= htmlspecialchars($b[4]) ?>"
              data-estado="<?= htmlspecialchars($b[5]) ?>"
            >
              <i data-lucide="more-horizontal" class="w-4 h-4"></i><p class="text-xs text-gray-500">ID: <?= $b[0] ?></p>
            </button>
          </div>

          <!-- Main -->
          <div>
            <h2 class="text-base font-semibold text-gray-900"><?= htmlspecialchars($b[1]) ?></h2>
          </div>

          <!-- Location -->
          <div class="flex items-center gap-2 text-sm text-gray-700">
            <i data-lucide="map-pin" class="w-4 h-4 text-gray-500"></i>
            <span><?= htmlspecialchars($b[3]) ?></span>
          </div>

          <!-- Tags -->
          <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs bg-blue-100 text-blue-700">
              <?= htmlspecialchars($b[2]) ?>
            </span>
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs bg-gray-100 text-gray-700">
              <?= htmlspecialchars($b[4]) ?>
            </span>
          </div>

          <hr class="border-gray-200">

          <!-- Footer -->
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-700">
              <i data-lucide="package" class="w-4 h-4 text-gray-500"></i>
              <span><?= $b[6] ?> Materiales</span>
            </div>

            <div class="flex items-center gap-3">
              <span
                class="estado-text text-sm font-medium <?= $estadoActivo  ?>"
              >
                <?= $estadoActivo ? "Activa" : "Inactiva" ?>
              </span>

              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  type="checkbox"
                  class="sr-only peer estado-switch"
                  data-id="<?= $b[0] ?>"
                  data-estado="<?= $estadoActivo ? 'Activo' : 'Inactivo' ?>"
                  <?= $estadoActivo ? "checked" : "" ?>
                >

                <div class="w-10 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:bg-emerald-600 transition">
                  <div class="w-4 h-4 bg-white rounded-full absolute top-0.5 left-0.5 peer-checked:translate-x-5 transition"></div>
                </div>
              </label>
            </div>
          </div>

        </div>
      <?php endforeach; ?>

    </div>
  </div>

  <!-- ========= MENÚ CONTEXTUAL ========= -->
  <div id="context-menu" class="hidden absolute bg-white border border-gray-200 rounded-xl shadow-lg p-2 w-52 z-50">
    <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-sm bodegas-ctx-btn" data-action="ver">
      <i data-lucide="eye" class="w-4 h-4"></i> <span>Ver detalles</span>
    </button>
    <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-sm bodegas-ctx-btn" data-action="editar">
      <i data-lucide="square-pen" class="w-4 h-4"></i> <span>Editar</span>
    </button>
    <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-sm bodegas-ctx-btn" data-action="deshabilitar">
      <i data-lucide="power" class="w-4 h-4"></i> <span>Deshabilitar</span>
    </button>
  </div>

  <!-- ========= MODAL CREAR (DISEÑO TIPO MOVIMIENTOS) ========= -->
<div id="modalCrear" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
  <!-- Fondo clicable para cerrar -->
  <div class="absolute inset-0" id="backdropCrear"></div>

  <!-- Contenido del modal -->
  <div class="relative mx-4 w-full max-w-2xl rounded-2xl bg-white shadow-xl p-6 sm:p-8">
    <!-- Header -->
    <div class="flex items-start justify-between mb-4">
      <div>
        <h2 class="text-xl font-semibold text-gray-900">Crear Nueva Bodega</h2>
        <p class="text-sm text-gray-500">Complete los datos para registrar una nueva bodega</p>
      </div>

      <button
        type="button"
        id="cerrarModal"
        class="inline-flex h-8 w-8 items-center justify-center rounded-full hover:bg-gray-100">
        <i data-lucide="x" class="h-4 w-4"></i>
      </button>
    </div>

    <!-- FORM -->
    <form class="space-y-4">
      <div class="grid gap-4 sm:grid-cols-2">

        <!-- ID -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">ID *</label>
          <input
            type="text"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]"
            placeholder="C-1">
        </div>

        <!-- Tipo -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
          <select
            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
            <option>Bodega</option>
            <option>Sub-Bodega</option>
          </select>
        </div>

        <!-- Clasificación -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Clasificación *</label>
          <select
            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
            <option>Eléctrico</option>
            <option>Construcción</option>
            <option>Sanitario</option>
            <option>Herramientas</option>
            <option>Ejemplo</option>
          </select>
        </div>

        <!-- Ubicación -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación *</label>
          <input
            type="text"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]"
            placeholder="Ej: Bloque A, Piso 1">
        </div>
      </div>

      <!-- Nombre -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Bodega *</label>
        <input
          type="text"
          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]"
          placeholder="Ej: Bodega Principal - Eléctrico">
      </div>

      <!-- Footer botones -->
      <div class="mt-4 flex items-center justify-end gap-2">
        <button
          type="button"
          id="cancelarModal"
          class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 border border-gray-200">
          Cancelar
        </button>

        <button
          type="button"
          class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-secondary hover:opacity-90">
          Crear Bodega
        </button>
      </div>
    </form>
  </div>
</div>


  <!-- ========= MODAL EDITAR (DISEÑO TIPO MOVIMIENTOS) ========= -->
<div id="modalEditar" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
  <!-- Fondo clicable para cerrar -->
  <div class="absolute inset-0" id="backdropEditar"></div>

  <!-- Contenido del modal -->
  <div class="relative mx-4 w-full max-w-2xl rounded-2xl bg-white shadow-xl p-6 sm:p-8">
    <!-- Header -->
    <div class="flex items-start justify-between mb-4">
      <div>
        <h2 class="text-xl font-semibold text-gray-900">Editar Bodega</h2>
        <p class="text-sm text-gray-500">Modifica la información de la bodega</p>
      </div>

      <button
        type="button"
        id="cerrarEditar"
        class="inline-flex h-8 w-8 items-center justify-center rounded-full hover:bg-gray-100">
        <i data-lucide="x" class="h-4 w-4"></i>
      </button>
    </div>

    <!-- FORM -->
    <form class="space-y-4">
      <div class="grid gap-4 sm:grid-cols-2">
        <!-- ID -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">ID *</label>
          <input
            id="editId"
            type="text"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
        </div>

        <!-- Tipo -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
          <select
            id="editTipo"
            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
            <option>Bodega</option>
            <option>Sub-Bodega</option>
          </select>
        </div>

        <!-- Clasificación -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Clasificación *</label>
          <select
            id="editClasificacion"
            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
            <option>Eléctrico</option>
            <option>Construcción</option>
            <option>Sanitario</option>
            <option>Herramientas</option>
            <option>Ejemplo</option>
          </select>
        </div>

        <!-- Ubicación -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación *</label>
          <input
            id="editUbicacion"
            type="text"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
        </div>
      </div>

      <!-- Nombre -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Bodega *</label>
        <input
          id="editNombre"
          type="text"
          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
      </div>

      <!-- Footer botones -->
      <div class="mt-4 flex items-center justify-end gap-2">
        <button
          type="button"
          id="cancelarEditar"
          class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 border border-gray-200">
          Cancelar
        </button>

        <button
          type="button"
          id="guardarEditar"
          class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-secondary hover:opacity-90">
          Guardar cambios
        </button>
      </div>
    </form>
  </div>
</div>


  <!-- ========= MODAL DETALLE ========= -->
  <div id="modalDetalle" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 items-center justify-center bodegas-modal">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Detalles de la bodega</h2>
        <button id="cerrarDetalle" class="h-8 w-8 rounded-full hover:bg-gray-100 flex items-center justify-center">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <div class="flex items-center gap-3 mt-4">
        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
          <i data-lucide="warehouse" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 id="detalleNombre" class="text-base font-semibold"></h3>
          <p class="text-xs text-gray-500">ID: <span id="detalleId"></span></p>
        </div>
      </div>

      <div class="mt-4 space-y-3 text-sm">
        <div class="flex items-center justify-between gap-3">
          <span class="font-medium text-gray-700">Clasificación:</span>
          <span id="detalleClasificacion" class="inline-flex items-center rounded-full px-3 py-1 text-xs bg-blue-100 text-blue-700"></span>
        </div>

        <div class="flex items-center justify-between gap-3">
          <span class="font-medium text-gray-700">Tipo:</span>
          <span id="detalleTipo" class="inline-flex items-center rounded-full px-3 py-1 text-xs bg-gray-100 text-gray-700"></span>
        </div>

        <div class="flex items-center justify-between gap-3">
          <span class="font-medium text-gray-700">Ubicación:</span>
          <span id="detalleUbicacion" class="text-gray-700"></span>
        </div>

        <div class="flex items-center justify-between gap-3">
          <span class="font-medium text-gray-700">Estado:</span>
          <span id="detalleEstado" class="inline-flex items-center rounded-full px-3 py-1 text-xs bg-emerald-100 text-emerald-700"></span>
        </div>
      </div>
    </div>
  </div>

</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // Lucide
  if (window.lucide && typeof lucide.createIcons === "function") {
    lucide.createIcons();
  }

  /* ============================================================
     ========== CAMBIO LISTA / GRID (TARJETAS) ==========
  ============================================================ */
  const btnVistaTabla    = document.getElementById("btnVistaTabla");
  const btnVistaTarjetas = document.getElementById("btnVistaTarjetas");
  const viewList         = document.getElementById("view-list");
  const viewGrid         = document.getElementById("view-grid");

  const setActiveBtn = (active, inactive) => {
    active.classList.add("bg-muted", "text-foreground");
    active.classList.remove("text-muted-foreground");

    inactive.classList.remove("bg-muted", "text-foreground");
    inactive.classList.add("text-muted-foreground");
  };

  const showList = () => {
    viewList?.classList.remove("hidden");
    viewGrid?.classList.add("hidden");
    if (btnVistaTabla && btnVistaTarjetas) setActiveBtn(btnVistaTabla, btnVistaTarjetas);
  };

  const showGrid = () => {
    viewGrid?.classList.remove("hidden");
    viewList?.classList.add("hidden");
    if (btnVistaTabla && btnVistaTarjetas) setActiveBtn(btnVistaTarjetas, btnVistaTabla);

    if (window.lucide && typeof lucide.createIcons === "function") {
      lucide.createIcons();
    }
  };

  if (btnVistaTabla && btnVistaTarjetas) {
    btnVistaTabla.addEventListener("click", showList);
    btnVistaTarjetas.addEventListener("click", showGrid);
    showList(); // inicial
  }

  /* ============================================================
     ========== MODAL CREAR (NUEVA BODEGA) ==========
  ============================================================ */
  const btnNuevaBodega = document.getElementById("btnNuevaBodega");
  const modalCrear     = document.getElementById("modalCrear");
  const cerrarModal    = document.getElementById("cerrarModal");
  const cancelarModal  = document.getElementById("cancelarModal");

  const openModal = (modal) => {
    if (!modal) return;
    modal.classList.remove("hidden");
    modal.classList.add("flex");
    document.body.classList.add("overflow-hidden");
    if (window.lucide && typeof lucide.createIcons === "function") lucide.createIcons();
  };

  const closeModal = (modal) => {
    if (!modal) return;
    modal.classList.add("hidden");
    modal.classList.remove("flex");
    document.body.classList.remove("overflow-hidden");
  };

  btnNuevaBodega?.addEventListener("click", () => openModal(modalCrear));
  cerrarModal?.addEventListener("click", () => closeModal(modalCrear));
  cancelarModal?.addEventListener("click", () => closeModal(modalCrear));

  // Cerrar modal crear clic fuera
  modalCrear?.addEventListener("click", (e) => {
    if (e.target === modalCrear) closeModal(modalCrear);
  });

  /* ============================================================
     ========== MENÚ CONTEXTUAL (3 PUNTOS) ==========
  ============================================================ */
  const contextMenu  = document.getElementById("context-menu");
  const modalDetalle = document.getElementById("modalDetalle");
  const modalEditar  = document.getElementById("modalEditar");

  let selectedData = null;

  function openContextMenu(btn) {
    if (!contextMenu) return;

    selectedData = {
      id: btn.dataset.id,
      nombre: btn.dataset.nombre,
      clasificacion: btn.dataset.clasificacion,
      ubicacion: btn.dataset.ubicacion,
      tipo: btn.dataset.tipo,
      estado: btn.dataset.estado
    };

    const r = btn.getBoundingClientRect();
    const menuWidth = 208; // w-52
    const x = r.right + window.scrollX - menuWidth;
    const y = r.bottom + window.scrollY + 8;

    contextMenu.style.left = `${Math.max(8, x)}px`;
    contextMenu.style.top  = `${y}px`;
    contextMenu.classList.remove("hidden");

    if (window.lucide && typeof lucide.createIcons === "function") {
      lucide.createIcons();
    }
  }

  function closeContextMenu() {
    contextMenu?.classList.add("hidden");
  }

  // Delegación para lista y grid
  document.addEventListener("click", (e) => {
    const btnDots = e.target.closest(".bodegas-btn-dots");
    if (btnDots) {
      e.preventDefault();
      e.stopPropagation();
      openContextMenu(btnDots);
      return;
    }

    if (contextMenu && !contextMenu.contains(e.target)) {
      closeContextMenu();
    }
  });

  /* ============================================================
     ========== ACCIONES DEL MENÚ ==========
  ============================================================ */
  if (contextMenu) {
    const btnVer  = contextMenu.querySelector("[data-action='ver']");
    const btnEdit = contextMenu.querySelector("[data-action='editar']");
    const btnOff  = contextMenu.querySelector("[data-action='deshabilitar']");

    btnVer?.addEventListener("click", () => {
      if (!selectedData || !modalDetalle) return;

      document.getElementById("detalleId").textContent = selectedData.id;
      document.getElementById("detalleNombre").textContent = selectedData.nombre;
      document.getElementById("detalleClasificacion").textContent = selectedData.clasificacion;
      document.getElementById("detalleTipo").textContent = selectedData.tipo;
      document.getElementById("detalleUbicacion").textContent = selectedData.ubicacion;

      const est = document.getElementById("detalleEstado");
      est.textContent = selectedData.estado;
      est.className =
        "inline-flex items-center rounded-full px-3 py-1 text-xs " +
        (selectedData.estado === "Activo");

      openModal(modalDetalle);
      closeContextMenu();
    });

    btnEdit?.addEventListener("click", () => {
      if (!selectedData || !modalEditar) return;

      document.getElementById("editId").value = selectedData.id;
      document.getElementById("editNombre").value = selectedData.nombre;
      document.getElementById("editClasificacion").value = selectedData.clasificacion;
      document.getElementById("editUbicacion").value = selectedData.ubicacion;
      document.getElementById("editTipo").value = selectedData.tipo;

      openModal(modalEditar);
      closeContextMenu();
    });

    btnOff?.addEventListener("click", () => {
      if (!selectedData) return;
      alert(`Bodega #${selectedData.id} deshabilitada`);
      closeContextMenu();
    });
  }

  /* ============================================================
     ========== CERRAR MODALES DETALLE / EDITAR ==========
  ============================================================ */
  document.getElementById("cerrarDetalle")?.addEventListener("click", () => closeModal(modalDetalle));
  document.getElementById("cerrarEditar")?.addEventListener("click", () => closeModal(modalEditar));
  document.getElementById("cancelarEditar")?.addEventListener("click", () => closeModal(modalEditar));

  modalDetalle?.addEventListener("click", (e) => {
    if (e.target === modalDetalle) closeModal(modalDetalle);
  });
  modalEditar?.addEventListener("click", (e) => {
    if (e.target === modalEditar) closeModal(modalEditar);
  });

  /* ============================================================
     ========== SWITCH ACTIVA / INACTIVA (GRID) ==========
  ============================================================ */
  document.addEventListener("change", (e) => {
    const sw = e.target.closest(".estado-switch");
    if (!sw) return;

    const card = sw.closest(".bg-white.border.border-gray-200"); // la card actual
    if (!card) return;

    const estadoNuevo = sw.checked ? "Activo" : "Inactivo";
    const textoNuevo  = sw.checked ? "Activa" : "Inactiva";

    const estadoText = card.querySelector(".estado-text");
    if (estadoText) {
      estadoText.textContent = textoNuevo;
      estadoText.classList.toggle(sw.checked);
      estadoText.classList.toggle(!sw.checked);
    }

    // Guardar estado
    sw.dataset.estado = estadoNuevo;

    // Actualizar data-estado del botón de 3 puntos para que el modal salga bien
    const dotsBtn = card.querySelector(".bodegas-btn-dots");
    if (dotsBtn) {
      dotsBtn.dataset.estado = estadoNuevo;
    }
  });

  /* ============================================================
     ========== ESC PARA CERRAR ==========
  ============================================================ */
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeModal(modalCrear);
      closeModal(modalDetalle);
      closeModal(modalEditar);
      closeContextMenu();
    }
  });
});
</script>


</body>
</html>
