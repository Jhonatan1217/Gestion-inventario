<?php

$collapsed = isset($_GET["coll"]) && $_GET["coll"] == "1";
$sidebarWidth = $collapsed ? "70px" : "260px";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Materiales</title>

  <!-- Tailwind desde CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script>

  <!-- CSS global con paleta y utilidades -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/src/assets/css/globals.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/src/assets/css/materiales/materiales.css">
  <script src="<?= BASE_URL ?>/src/assets/js/materiales.js"></script>

</head>
<body
  class="min-h-screen bg-background text-foreground transition-all duration-300
    <?php echo $collapsed ? 'lg:pl-[70px]' : 'lg:pl-[260px]'; ?>"
>

    <main class="page-with-sidebar max-w-7xl mx-auto px-4 py-8">
      <div class="space-y-6 animate-fade-in-up">

        <!-- HEADER -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-2xl font-bold tracking-tight">Gestión de Materiales</h1>
            <p class="text-muted-foreground">Gestiona el inventario de materiales y herramientas</p>
          </div>

          <!-- Updated right controls with consistent spacing and styling -->
          <div class="flex items-center gap-3">
            <!-- View toggle buttons -->
            <div class="inline-flex rounded-lg border border-border bg-card shadow-sm overflow-hidden">
              <!-- Table view -->
              <button
                id="tableViewBtn"
                type="button"
                class="px-3 py-2 text-xs sm:text-sm flex items-center gap-1 bg-muted text-foreground view-toggle-btn active"
                title="Vista de tabla"
                onclick="switchView('table')"
              >
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
              </button>

              <!-- Card view -->
              <button
                id="cardViewBtn"
                type="button"
                class="px-3 py-2 text-xs sm:text-sm flex items-center gap-1 text-muted-foreground view-toggle-btn"
                title="Vista de tarjetas"
                onclick="switchView('card')"
              >
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <rect x="4" y="4" width="7" height="7" rx="1"></rect>
                  <rect x="13" y="4" width="7" height="7" rx="1"></rect>
                  <rect x="4" y="13" width="7" height="7" rx="1"></rect>
                  <rect x="13" y="13" width="7" height="7" rx="1"></rect>
                </svg>
              </button>
            </div>

            <!-- New material button -->
            <button
              id="btnNuevoMaterial"
              type="button"
              onclick="openCreateModal()"
              class="inline-flex items-center justify-center rounded-md bg-secondary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm hover:opacity-90 gap-2"
            >
              <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Nuevo Material
            </button>
          </div>
        </div>

<!-- Contenedor principal -->
<div class="flex justify-between items-center w-full">

  <!-- BUSCADOR A LA IZQUIERDA -->
  <div class="relative w-full max-w-xs">
    <input
      type="text"
      id="inputBuscar"
      placeholder="Buscar por nombre..."
      class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
    />
  </div>

  <!-- FILTRO A LA DERECHA -->
  <div class="flex items-center gap-2 ml-4">
    <!-- Ícono de filtro -->
    <svg class="h-5 w-5 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
      <path
        d="M5 5h14a1 1 0 0 1 .8 1.6L15 12v4.5a1 1 0 0 1-.553.894l-3 1.5A1 1 0 0 1 10 18v-6L4.2 6.6A1 1 0 0 1 5 5z"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
      />
    </svg>

    <!-- Select -->
    <select
      id="selectFiltroRol"
      class="rounded-md border border-input bg-background px-3 pr-10 py-2 text-sm"
    >
      <option value="">Todos</option>
      <option value="Coordinador">Coordinador</option>
      <option value="Subcoordinador">Subcoordinador</option>
      <option value="Instructor">Instructor</option>
      <option value="Pasante">Pasante</option>
      <option value="Aprendiz">Aprendiz</option>
    </select>
  </div>

</div>

        <!-- Updated materials table with consistent styling -->
        <div id="tableView" class="overflow-visible rounded-xl border border-border bg-card relative">
          <table class="min-w-full divide-y divide-border text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">Código</th>
                <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">Material</th>
                <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">Stock</th>
                <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">Unidad</th>
                <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">Bodega</th>
                <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">Estado</th>
                <th class="px-4 py-3 text-right font-medium text-xs text-muted-foreground">Acciones</th>
              </tr>
            </thead>
            <tbody id="tableBody" class="divide-y divide-border bg-card">
              <!-- Se llena dinámicamente con JS -->
            </tbody>
          </table>
          <div class="pagination" id="pagination"></div>
        </div>

        <!-- Card view container -->
        <div id="cardView" class="hidden">
          <div
            id="cardsContainer"
            class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3"
          >
            <!-- Se llena dinámicamente con JS -->
          </div>
          <div class="pagination" id="cardPagination"></div>
        </div>

      </div>
    </main>

    <!-- CREATE MODAL -->
    <div id="createModal" class="modal-overlay">
        <div class="relative w-full max-w-2xl rounded-xl border border-border bg-card p-6 shadow-lg">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-lg font-semibold">Crear Nuevo Material</h2>
                    <p class="text-sm text-muted-foreground">
                        Complete los datos para registrar un nuevo material
                    </p>
                </div>
                <button
                    type="button"
                    onclick="closeCreateModal()"
                    class="rounded-full p-1 hover:bg-muted"
                >
                    <span class="sr-only">Cerrar</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form class="space-y-4" onsubmit="event.preventDefault();">
                <!-- Código y Nombre -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Código *</label>
                        <input id="codigo" type="text" placeholder="TEC-001" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Nombre *</label>
                        <input id="nombre" type="text" placeholder="Ej: cemento gris" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" required>
                    </div>
                </div>
                
                <!-- Descripción -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">Descripción *</label>
                    <textarea id="descripcion" placeholder="Descripción técnica del material" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm h-20" required></textarea>
                </div>
                
                <!-- Categoría, Tipo de bien, Unidad -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Categoría *</label>
                        <select id="categoria" class="w-full rounded-md border border-input bg-background px-3 pr-10 py-2 text-sm input-siga" required>
                            <option>Construcción</option>
                            <option>Herramientas</option>
                            <option>Eléctrico</option>
                            <option>Pintura</option>
                            <option>Sanitario</option>
                            <option>Maquinaria</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Tipo de bien *</label>
                        <select id="tipo" class="w-full rounded-md border border-input bg-background px-3 pr-10 py-2 text-sm input-siga" required>
                            <option>Consumible</option>
                            <option>Herramienta</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Unidad *</label>
                        <select id="unidad" class="w-full rounded-md border border-input bg-background px-3 pr-10 py-2 text-sm input-siga" required>
                            <option>Bolsa</option>
                            <option>Unidad</option>
                            <option>Metro</option>
                            <option>Kg</option>
                        </select>
                    </div>
                </div>
                
                <!-- Stock Actual, Stock Mínimo, Bodega -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Stock Actual *</label>
                        <input id="stock_actual" type="number" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Stock Mínimo *</label>
                        <input id="stock_minimo" type="number" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Bodega *</label>
                        <select id="bodega" class="w-full rounded-md border border-input bg-background px-3 pr-10 py-2 text-sm input-siga" required>
                            <option>Bodega Principal</option>
                            <option>Sub-bodega Construcción</option>
                            <option>Sub-bodega Herramientas</option>
                            <option>Sub-bodega Eléctrico</option>
                            <option>Sub-bodega Pintura</option>
                            <option>Sub-bodega Sanitario</option>
                            <option>Sub-bodega Maquinaria</option>
                        </select>
                    </div>
                </div>
                
                <!-- Observación -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">Observación *</label>
                    <textarea id="observacion" placeholder="Formación técnica de procesos constructivos" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm h-16" required></textarea>
                </div>
                
                <!-- Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="closeCreateModal()" class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-muted">
                        Cancelar
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:opacity-90">
                        Crear Material
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DETAILS MODAL -->
    <div id="detailsModal" class="modal-overlay">
        <div class="relative w-full max-w-lg rounded-xl border border-border bg-card p-6 shadow-lg">
            <div class="flex items-start justify-between gap-4 mb-4">
                <h2 class="text-lg font-semibold">Detalles del Material</h2>
                <button
                    type="button"
                    onclick="closeDetailsModal()"
                    class="rounded-full p-1 hover:bg-muted"
                >
                    <span class="sr-only">Cerrar</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div id="detailsContent" class="space-y-4">
                <div class="flex items-center gap-3 pb-3 border-b border-border">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: rgba(57, 169, 0, 0.1);">
                        <svg class="w-6 h-6 text-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    </div>
                    <div>
                        <p class="font-bold text-foreground text-sm" id="detailName">Cemento gris</p>
                        <p class="text-xs text-muted-foreground" id="detailCode">MAT-001</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold">Categoría:</p>
                        <p class="text-foreground" id="detailCategory">Construcción</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold">Tipo:</p>
                        <p class="text-foreground" id="detailType">Consumo</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold">Stock:</p>
                        <p class="text-foreground" id="detailStock">45 bolsa</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold">Stock mínimo:</p>
                        <p class="text-foreground" id="detailMinStock">20 bolsa</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold">Bodega:</p>
                        <p class="text-foreground" id="detailWarehouse">Bodega Construcción</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold">Estado:</p>
                        <span class="inline-block px-2 py-1 rounded text-xs font-medium bg-success text-success-foreground" id="detailStatus">Disponible</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="modal-overlay">
        <div class="relative w-full max-w-2xl rounded-xl border border-border bg-card p-6 shadow-lg">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-lg font-semibold">Editar Material</h2>
                    <p class="text-sm text-muted-foreground">
                        Modifica la información del material
                    </p>
                </div>
                <button
                    type="button"
                    onclick="closeEditModal()"
                    class="rounded-full p-1 hover:bg-muted"
                >
                    <span class="sr-only">Cerrar</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form class="space-y-4" onsubmit="event.preventDefault();">
                <!-- Código y Nombre -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Código *</label>
                        <input id="editCodigo" type="text" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" disabled>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Nombre *</label>
                        <input id="editNombre" type="text" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" required>
                    </div>
                </div>
                
                <!-- Descripción -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">Descripción *</label>
                    <textarea id="editDescripcion" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm h-20" required></textarea>
                </div>
                
                <!-- Categoría, Tipo de bien, Unidad -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Categoría *</label>
                        <select id="editCategoria" class="w-full rounded-md border border-input bg-background px-3 pr-10 py-2 text-sm input-siga" required>
                            <option>Construcción</option>
                            <option>Herramientas</option>
                            <option>Eléctrico</option>
                            <option>Pintura</option>
                            <option>Sanitario</option>
                            <option>Maquinaria</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Tipo de bien *</label>
                        <select id="editTipo" class="w-full rounded-md border border-input bg-background px-3 pr-10 py-2 text-sm input-siga" required>
                            <option>Consumible</option>
                            <option>Herramienta</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Unidad *</label>
                        <select id="editUnidad" class="w-full rounded-md border border-input bg-background px-3 pr-10 py-2 text-sm input-siga" required>
                            <option>Bolsa</option>
                            <option>Unidad</option>
                            <option>Metro</option>
                            <option>Kg</option>
                        </select>
                    </div>
                </div>
                
                <!-- Stock Actual, Stock Mínimo, Bodega -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Stock Actual *</label>
                        <input id="editStockActual" type="number" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Stock Mínimo *</label>
                        <input id="editStockMinimo" type="number" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Bodega *</label>
                        <select id="editBodega" class="w-full rounded-md border border-input bg-background px-3 pr-10 py-2 text-sm input-siga" required>
                            <option>Bodega Principal</option>
                            <option>Sub-bodega Construcción</option>
                            <option>Sub-bodega Herramientas</option>
                            <option>Sub-bodega Eléctrico</option>
                            <option>Sub-bodega Pintura</option>
                            <option>Sub-bodega Sanitario</option>
                            <option>Sub-bodega Maquinaria</option>
                        </select>
                    </div>
                </div>
                
                <!-- Observación -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">Observación *</label>
                    <textarea id="editObservacion" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm h-16" required></textarea>
                </div>
                
                <!-- Buttons -->
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="closeEditModal()" class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-muted">
                        Cancelar
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:opacity-90">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Contenedor para las alertas tipo Flowbite -->
    <div
      id="flowbite-alert-container"
      class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 w-full max-w-md"
    ></div>

</body>
</html>
