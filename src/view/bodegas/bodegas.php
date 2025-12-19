<?php
$collapsed = isset($_GET["coll"]) && $_GET["coll"] == "1";
$sidebarWidth = $collapsed ? "70px" : "260px";

include_once BASE_PATH . '/Config/database.php';
include_once BASE_PATH . '/src/models/bodega.php';


$model = new BodegaModel($conn);
$bodegas = $model->listar();

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Gestión Bodegas</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- CSS globals -->
  <link rel="stylesheet" href="src/assets/css/globals.css" />

  <!-- CSS bodegas -->
  <link rel="stylesheet" href="src/assets/css/bodegas/bodegas.css" />

  <!-- JS bodegas -->
  <script src="src/assets/js/bodegas/bodegas.js" defer></script>
</head>

<body class="bg-gray-50 text-gray-900">
<main class="p-6 transition-all duration-300"
  style="margin-left: <?= isset($_GET['coll']) && $_GET['coll'] == "1" ? '70px' : '260px' ?>;"
>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

  <!-- TÍTULO -->
  <div>
    <h1 class="text-3xl font-bold">Gestión Bodegas</h1>
    <p class="text-sm text-gray-500">
      Administra las bodegas y sub-bodegas del inventario
    </p>
  </div>

  <!-- CONTROLES DERECHA -->
<div class="flex items-center gap-2">

  <!-- Grupo: Switch Lista / Grid -->
  <div class="inline-flex rounded-lg border border-gray-200 bg-white overflow-hidden shadow-sm">

    <!-- Lista -->
    <button
      type="button"
      id="btnVistaTabla"
      class="px-3 py-2 text-sm flex items-center gap-1 bg-muted text-foreground"
    >
      <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
           viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>

    <!-- Grid -->
    <button
      type="button"
      id="btnVistaTarjetas"
      class="px-3 py-2 text-sm flex items-center gap-1 text-muted-foreground"
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

  <!-- Botón Nueva Bodega (grupo independiente) -->
  <button
    id="btnNuevaBodega"
    class="inline-flex items-center justify-center rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm hover:opacity-90 gap-2">
    <i data-lucide="plus" class="w-4 h-4"></i>
    Nueva Bodega
  </button>
  <button
    id="btnNuevaSubBodega"
    class="inline-flex items-center justify-center rounded-lg bg-secondary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm hover:opacity-90 gap-2">
    <i data-lucide="plus" class="w-4 h-4"></i>
    Nueva Sub-bodega
  </button>


</div>

</div>
<!-- BUSCADOR + FILTRO -->
<div class="flex items-center justify-between w-full gap-4 mb-4 -mt-4">

  <!-- BUSCADOR (ESTILO ORIGINAL) -->
  <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-4 py-2 shadow-sm w-full sm:w-[330px] bg-gray-100">
    <i data-lucide="search" class="w-4 h-4 text-gray-500"></i>
    <input
      type="text"
      placeholder="Buscar por nombre o ID"
      class="w-full bg-transparent outline-none text-sm"
    />
  </div>

  <!-- FILTRO -->
  <div class="flex items-center gap-2">

    <i data-lucide="filter" class="w-4 h-4 text-gray-600"></i>

    <div class="relative bg-white border border-gray-200 rounded-lg px-4 py-1 shadow-sm min-w-[150px] bg-gray-100">
      <select
        id="bodegasFilter"
        class="w-full appearance-none bg-transparent outline-none text-sm text-gray-700 pr-6"
      >
        <option value="Todos">Todos</option>
        <option value="Activo">Activos</option>
        <option value="Inactivo">Inactivos</option>
      </select>
    </div>
  </div>

</div>

  <div class="w-full">
    <!-- ========= VISTA LISTA ========= -->
    <div id="view-list">
      <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-100 text-gray-600">
            <tr>
              <th class="px-4 py-3 text-left font-semibold">ID</th>
              <th class="px-4 py-3 text-left font-semibold">Nombre</th>
              <th class="px-4 py-3 text-left font-semibold">Clasificación</th>
              <th class="px-4 py-3 text-left font-semibold">Ubicación</th>
              <th class="px-4 py-3 text-left font-semibold">Estado</th>
              <th class="px-4 py-3 text-left font-semibold">Acciones</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-200">
            <?php foreach ($bodegas as $b): ?>
              <?php $activo = $b['estado'] === "Activo"; ?>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">#<?= $b['codigo_bodega'] ?></td>

                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                      <i data-lucide="warehouse" class="w-4 h-4"></i>
                    </div>
                    <span class="font-medium text-gray-900"><?= htmlspecialchars($b['nombre']) ?></span>
                  </div>
                </td>

                <td class="px-4 py-3">
                  <span class="inline-flex items-center rounded-full border border-gray-300 bg-white px-3 py-1 text-xs font-medium text-gray-700">
                    <?= htmlspecialchars($b['clasificacion_bodega']) ?>
                  </span>
                </td>

                <td class="px-4 py-3 text-gray-700">
                  <i data-lucide="map-pin" class="w-4 h-4 inline-block text-gray-500 mr-1"></i>
                  <?= htmlspecialchars($b['ubicacion']) ?>
                </td>
                
                <td class="px-4 py-3">
                  <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                    <?= $activo ? 'badge-estado-activo' : 'badge-estado-inactivo' ?>">
                    <?= htmlspecialchars($b['estado']) ?>
                  </span>
                </td>

                <td class="px-4 py-3">
                  <button
                    class="w-8 h-8 rounded-full flex items-center justify-center bodegas-btn-dots"
                    data-id="<?= $b['id_bodega'] ?>"
                    data-codigo="<?= htmlspecialchars($b['codigo_bodega']) ?>"
                    data-nombre="<?= htmlspecialchars($b['nombre']) ?>"
                    data-clasificacion="<?= htmlspecialchars($b['clasificacion_bodega']) ?>"
                    data-ubicacion="<?= htmlspecialchars($b['ubicacion']) ?>"
                    data-estado="<?= htmlspecialchars($b['estado']) ?>"
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

  </div>

  <!-- ========= VISTA GRID ========= -->
  <div id="view-grid" class="hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

      <?php foreach ($bodegas as $b):
        $estadoActivo = $b['estado'] === "Activo";
      ?>
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 flex flex-col gap-3 relative">
          <div class="flex items-start justify-between gap-4">
            <div>
              <h2 class="text-base font-semibold text-gray-900"><?= htmlspecialchars($b['nombre']) ?></h2>
              <p class="text-xs text-gray-500">ID: <?= $b['codigo_bodega'] ?></p>
            </div>

            <button
              class="w-8 h-8 rounded-full flex items-center justify-center bodegas-btn-dots"
                data-id="<?= $b['id_bodega'] ?>"
                data-codigo="<?= htmlspecialchars($b['codigo_bodega']) ?>"
                data-nombre="<?= htmlspecialchars($b['nombre']) ?>"
                data-clasificacion="<?= htmlspecialchars($b['clasificacion_bodega']) ?>"
                data-ubicacion="<?= htmlspecialchars($b['ubicacion']) ?>"
                data-estado="<?= htmlspecialchars($b['estado']) ?>"
            >
              <i data-lucide="more-horizontal" class="w-4 h-4"></i>
            </button>
          </div>

          <div class="flex items-center gap-2 text-sm text-gray-700">
            <i data-lucide="map-pin" class="w-4 h-4 text-gray-500"></i>
            <span><?= htmlspecialchars($b['ubicacion']) ?></span>
          </div>

          <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs bg-blue-100 text-blue-700">
              <?= htmlspecialchars($b['clasificacion_bodega']) ?>
            </span>
          </div>

          <hr class="border-gray-200">

          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-700">
              <i data-lucide="package" class="w-4 h-4 text-gray-500"></i>
              <span><?= $b['materiales'] ?> Materiales</span>
            </div>

            <div class="flex items-center gap-3">
              <span class="estado-text text-sm font-medium <?= $estadoActivo ? 'text-emerald-700' : 'text-red-700' ?>">
                <?= $estadoActivo ? "Activa" : "Inactiva" ?>
              </span>

              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  type="checkbox"
                  class="sr-only peer estado-switch"
                  data-codigo="<?= htmlspecialchars($b['codigo_bodega']) ?>"
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
<div
  id="context-menu"
  class="hidden absolute z-50 w-56 rounded-lg bg-white border border-gray-200 shadow-md"
>
  <ul class="py-2 text-sm text-gray-800">
    
    <!-- Ver detalles -->
    <li>
      <button
        class="w-full flex items-center gap-3 px-4 py-2 hover:bg-gray-50 bodegas-ctx-btn"
        data-action="ver"
      >
        <i data-lucide="eye" class="w-5 h-5 text-gray-600"></i>
        <span>Ver detalles</span>
      </button>
    </li>

    <!-- Editar -->
    <li>
      <button
        class="w-full flex items-center gap-3 px-4 py-2 hover:bg-gray-50 bodegas-ctx-btn"
        data-action="editar"
      >
        <i data-lucide="square-pen" class="w-5 h-5 text-gray-600"></i>
        <span>Editar</span>
      </button>
    </li>

    <!-- Separador -->
    <li class="my-2 border-t border-gray-200"></li>

    <!-- Desactivar -->
    <li>
      <button
        class="w-full flex items-center gap-3 px-4 py-2 hover:bg-gray-50 bodegas-ctx-btn"
        data-action="deshabilitar"
      >
        <i data-lucide="power" class="w-5 h-5 text-gray-600"></i>
        <span>Desactivar</span>
      </button>
    </li>

  </ul>
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
    <form id="formCrearBodega" class="space-y-4">
      <div class="grid gap-4 sm:grid-cols-2">

        <!-- ID -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">ID *</label>
          <input 
            id="crearCodigo"
            type="text"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]"
            placeholder="C-1">
        </div>    

        <!-- Clasificación -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Clasificación *</label>
          <select
            id="crearClasificacion"
            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
            <option value="Insumos">Insumos</option>
            <option value="Equipos">Equipos</option>
          </select>
        </div>

        <!-- Ubicación -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación *</label>
          <input
            id="crearUbicacion"
            type="text"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]"
            placeholder="Ej: Bloque A, Piso 1">
        </div>
      </div>

      <!-- Nombre -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Bodega *</label>
        <input
          id="crearNombre"
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
          type="submit"
          class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-secondary hover:opacity-90">
          Crear Bodega
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ========= MODAL CREAR SUB-BODEGA ========= -->
<div id="modalCrearSubBodega" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
  
  <!-- Fondo clicable -->
  <div class="absolute inset-0" id="backdropCrearSub"></div>

  <!-- Contenido -->
  <div class="relative mx-4 w-full max-w-2xl rounded-2xl bg-white shadow-xl p-6 sm:p-8">

    <!-- Header -->
    <div class="flex items-start justify-between mb-4">
      <div>
        <h2 class="text-xl font-semibold text-gray-900">Crear Nueva Sub-bodega</h2>
        <p class="text-sm text-gray-500">
          Complete los datos para registrar una sub-bodega
        </p>
      </div>

      <button
        type="button"
        id="cerrarModalSub"
        class="inline-flex h-8 w-8 items-center justify-center rounded-full hover:bg-gray-100">
        <i data-lucide="x" class="h-4 w-4"></i>
      </button>
    </div>

    <!-- FORM -->
    <form id="formCrearSubBodega" class="space-y-4">

      <div class="grid gap-4 sm:grid-cols-2">

        <!-- BODEGA PADRE -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Bodega padre *
          </label>
          <select
            id="subIdBodega"
            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]"
            required>
            <!-- Se llena desde PHP o JS -->
            <?php foreach ($bodegas as $b): ?>
              <option value="<?= htmlspecialchars($b['codigo_bodega']) ?>">
                <?= htmlspecialchars($b['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- CÓDIGO -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Código *
          </label>
          <input
            id="subCodigo"
            type="text"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]"
            placeholder="SB-01"
            required>
        </div>

        <!-- CLASIFICACIÓN -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Clasificación *
          </label>
          <select
            id="subClasificacion"
            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]"
            required>
            <option value="Insumos">Insumos</option>
            <option value="Equipos">Equipos</option>
          </select>
        </div>
      </div>

      <!-- NOMBRE -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Nombre de la Sub-bodega *
        </label>
        <input
          id="subNombre"
          type="text"
          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                 focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]"
          placeholder="Sub-bodega Eléctrica"
          required>
      </div>

      <!-- DESCRIPCIÓN -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Descripción
        </label>
        <textarea
          id="subDescripcion"
          rows="3"
          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                 focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]"
          placeholder="Opcional"></textarea>
      </div>

      <!-- FOOTER -->
      <div class="mt-4 flex items-center justify-end gap-2">
        <button
          type="button"
          id="cancelarModalSub"
          class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 border border-gray-200">
          Cancelar
        </button>

        <button
          type="submit"
          class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-secondary hover:opacity-90">
          Crear Sub-bodega
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
    <form id="formEditarBodega" class="space-y-4">

    <input type="hidden" id="editIdBodega">

      <div class="grid gap-4 sm:grid-cols-2">
        
        <!-- ID visible (solo informativo) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">ID *</label>
          <input
            id="editCodigoBodega"
            type="text"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm ">
        </div>

        <!-- Clasificación -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Clasificación *</label>
          <select
            id="editClasificacion"
            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
            <option value="Insumos">Insumos</option>
            <option value="Equipos">Equipos</option>
          </select>
        </div>

        <!-- Ubicación -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación *</label>
          <input
            id="editUbicacion"
            type="text"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
        </div>
      </div>

      <!-- Nombre -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Bodega *</label>
        <input
          id="editNombre"
          type="text"
          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                 focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
      </div>

      <!-- Footer botones -->
      <div class="mt-4 flex items-center justify-end gap-2">
        <button
          type="button"
          id="cancelarEditar"
          class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700
                 hover:bg-gray-100 border border-gray-200">
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
<div
  id="modalDetalle"
  class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center"
>
  <div
    class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[85vh] flex flex-col"
  >

    <!-- HEADER -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
      <h2 class="text-lg font-semibold text-gray-900">
        Detalles de la Bodega
      </h2>

      <button
        id="cerrarDetalle"
        class="h-8 w-8 rounded-full hover:bg-gray-100 flex items-center justify-center"
      >
        <i data-lucide="x" class="w-4 h-4 text-gray-600"></i>
      </button>
    </div>

    <!-- CONTENIDO CON SCROLL -->
    <div class="overflow-y-auto px-6 py-5 space-y-6">

      <!-- INFO PRINCIPAL -->
      <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
          <i data-lucide="warehouse" class="w-6 h-6"></i>
        </div>

        <div>
          <h3 id="detalleNombre" class="text-base font-semibold text-gray-900">
            Bodega Principal - Eléctrico
          </h3>
          <p class="text-xs text-gray-500">
            ID: <span id="detalleId">1</span>
          </p>
        </div>
      </div>

      <!-- DATOS BODEGA -->
      <div class="grid gap-4 text-sm">

        <!-- FILA 1: CLASIFICACIÓN + TIPO -->
        <div class="grid grid-cols-2 gap-6 items-center">
          <div class="grid grid-cols-[120px_auto] gap-3 items-center">
            <span class="text-gray-600">Clasificación:</span>
            <span id="detalleClasificacion" class="detalle-chip">Insumos</span>
          </div>
        </div>

        <!-- FILA 2: UBICACIÓN + ESTADO -->
        <div class="grid grid-cols-2 gap-6 items-center">
          <div class="grid grid-cols-[120px_auto] gap-3 items-center">
            <span class="text-gray-600">Ubicación:</span>
            <span
              id="detalleUbicacion"
              class="font-medium text-gray-800"
            >
              Bloque A, Piso 1
            </span>
          </div>

          <div class="grid grid-cols-[120px_auto] gap-3 items-center">
            <span class="text-gray-600">Estado:</span>
            <span
              id="detalleEstado"
              class="badge-estado-activo"
            >
              Activo
            </span>
          </div>
        </div>

      </div>

      <!-- SECCIÓN MATERIALES -->
      <div class="pt-5 border-t border-gray-200">

        <div class="flex items-center gap-2 mb-2">
          <i data-lucide="box" class="w-4 h-4 text-gray-600"></i>
          <h4 class="font-semibold text-gray-900">
            Materiales en esta Bodega
          </h4>
        </div>

        <p class="text-sm text-gray-500 mb-4">
          Total: <strong>3</strong> material(es)
        </p>

        <!-- LISTA MATERIALES -->
        <div class="border border-gray-200 rounded-2xl p-4 bg-gray-50">

          <div class="flex gap-4">

            <!-- ICONO / IMAGEN DEL MATERIAL -->
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
              <i data-lucide="box" class="w-5 h-5"></i>
              <!-- si luego usas imagen real: <img src="ruta-imagen.png" class="w-6 h-6" /> -->
            </div>

            <!-- CONTENIDO -->
            <div class="flex-1">

              <div class="flex justify-between items-start">
              <div>
                <h5 class="font-medium text-gray-900">Taladro Percutor</h5>
                <p class="text-xs text-gray-500">HER-001 • Herramientas</p>
              </div>
              <span class="badge-material-disponible">Disponible</span>
            </div>

            <div class="mt-3 text-sm font-medium text-gray-900">
              <strong>5</strong> unidad
            </div>

            <div class="mt-2">
              <!-- BARRA -->
              <div class="mt-2">
                <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                  <div class="h-full rounded-full bg-emerald-600 w-full"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Min: 3</p>
              </div>
            </div>

          </div>

        </div>
      </div>
    </div>
  </div>
</div>

</main>
</body>
</html>
