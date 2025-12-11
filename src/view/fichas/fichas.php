<?php
// =====================================
// FICHAS MANAGEMENT – PHP VIEW
// =====================================

$collapsed = isset($_GET["coll"]) && $_GET["coll"] == "1";
$sidebarWidth = $collapsed ? "70px" : "260px";
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Fichas de Formación</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Flowbite components (optional usage in this view) -->
  <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script>

  <!-- Custom styles for the Fichas management module -->
  <link rel="stylesheet" href="src/assets/css/fichas/fichas.css" />

  <!-- Expose authenticated user ID to JavaScript logic -->
  <script>
    const AUTH_USER_ID = <?= $_SESSION['usuario_id'] ?? 0; ?>;
  </script>
</head>

<body class="bg-background p-6">
  <main class="p-6 transition-all duration-300"
      style="margin-left: <?= $sidebarWidth ?>;">
    <div class="space-y-6 animate-fade-in-up">
      <!-- ================================== -->
      <!-- PAGE HEADER                         -->
      <!-- ================================== -->
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight">Fichas de Formación</h1>
          <p class="text-muted-foreground">
            Administra las fichas y su asignación a programas e instructores
          </p>
        </div>

        <!-- Right-side controls: view switch and "Nueva Ficha" button -->
        <div class="flex items-center gap-3">
          <!-- View switch: table / cards -->
          <div class="inline-flex rounded-lg border border-border bg-card shadow-sm overflow-hidden">
            <!-- Table view button -->
            <button
              type="button"
              id="btnVistaTabla"
              class="px-3 py-2 text-xs sm:text-sm flex items-center gap-1 bg-muted text-foreground"
            >
              <!-- List icon -->
              <svg
                class="h-4 w-4"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.8"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M4 6h16M4 12h16M4 18h16"
                />
              </svg>
            </button>

            <!-- Cards view button -->
            <button
              type="button"
              id="btnVistaTarjetas"
              class="px-3 py-2 text-xs sm:text-sm flex items-center gap-1 text-muted-foreground"
            >
              <!-- Grid icon -->
              <svg
                class="h-4 w-4"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.8"
              >
                <rect x="4" y="4" width="7" height="7" rx="1"></rect>
                <rect x="13" y="4" width="7" height="7" rx="1"></rect>
                <rect x="4" y="13" width="7" height="7" rx="1"></rect>
                <rect x="13" y="13" width="7" height="7" rx="1"></rect>
              </svg>
            </button>
          </div>

          <!-- "Nueva Ficha" primary action - botón verde con icono + -->
          <button
            id="btnNuevaFicha"
            class="inline-flex items-center justify-center rounded-md bg-emerald-500 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-600 gap-2"
            type="button"
          >
            <!-- Plus icon -->
            <svg
              class="h-4 w-4"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4v16m8-8H4"
              />
            </svg>
            Nueva Ficha
          </button>
        </div>
      </div>

      <!-- ================================== -->
      <!-- TOP FILTERS (SEARCH + ESTADO FILTER) -->
      <!-- ================================== -->
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between my-6">
        <!-- Search con icono de lupa y fondo gris -->
        <div class="w-full sm:max-w-xs relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
          </div>
          <input
            id="inputBuscar"
            type="text"
            placeholder="Buscar por número..."
            class="w-full rounded-md border-0 bg-gray-100 pl-10 pr-3 py-2 text-sm placeholder:text-muted-foreground focus:ring-2 focus:ring-emerald-500"
          />
        </div>

        <!-- Estado filter (Todos, Activa, Inactiva) -->
        <div class="flex items-center gap-2">
          <svg
            class="h-4 w-4 text-muted-foreground"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
          >
            <path
              d="M5 5h14a1 1 0 0 1 .8 1.6L15 12v4.5a1 1 0 0 1-.553.894l-3 1.5A1 1 0 0 1 10 18v-6L4.2 6.6A1 1 0 0 1 5 5z"
              stroke="currentColor"
              stroke-width="1.8"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>

          <select
            id="selectFiltroEstado"
            class="rounded-md border border-input bg-background px-3 pr-10 py-2 text-sm"
          >
            <option value="">Todos</option>
            <option value="1">Activa</option>
            <option value="0">Inactiva</option>
          </select>
        </div>
      </div>

      <!-- ================================== -->
      <!-- TABLE VIEW CONTAINER               -->
      <!-- ================================== -->
      <div
        id="vistaTabla"
        class="overflow-visible rounded-xl border border-border bg-card relative"
      >
        <table class="min-w-full divide-y divide-border text-sm">
          <thead class="bg-gray-50">
            <!-- Nuevas columnas según diseño -->
            <tr>
              <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">
                Número de Ficha
              </th>
              <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">
                Programa
              </th>
              <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">
                Nivel
              </th>
              <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">
                Instructor
              </th>
              <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">
                Estado
              </th>
              <th class="px-4 py-3 text-right font-medium text-xs text-muted-foreground">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody
            id="tbodyFichas"
            class="divide-y divide-border bg-card"
          >
            <!-- Rows are rendered dynamically via JavaScript -->
          </tbody>
        </table>
      </div>

      <!-- ================================== -->
      <!-- CARDS VIEW CONTAINER               -->
      <!-- ================================== -->
      <div id="vistaTarjetas" class="hidden">
        <div
          id="cardsContainer"
          class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"
        >
          <!-- Cards are rendered dynamically via JavaScript -->
        </div>
      </div>
    </div>
  </main>

  <!-- ========================================= -->
  <!-- MODAL: CREATE / EDIT FICHA                -->
  <!-- ========================================= -->
  <div id="modalFicha" class="modal-overlay">
    <div class="relative w-full max-w-md rounded-xl border border-border bg-card p-6 shadow-lg">
      <!-- Modal header -->
      <div class="flex items-start justify-between gap-4 mb-4">
        <div>
          <h2 id="modalFichaTitulo" class="text-lg font-semibold">
            Crear Nueva Ficha
          </h2>
          <p id="modalFichaDescripcion" class="text-sm text-muted-foreground">
            Complete los datos para registrar una nueva ficha de formación
          </p>
        </div>
        <!-- Close button -->
        <button
          type="button"
          id="btnCerrarModalFicha"
          class="rounded-full p-1 hover:bg-muted"
        >
          <span class="sr-only">Cerrar</span>
          <svg
            class="h-5 w-5"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>

      <!-- Modal form con campos según diseño -->
      <form id="formFicha" class="space-y-4" novalidate>
        <!-- Hidden field used to distinguish between create and edit -->
        <input type="hidden" id="hiddenFichaId" value="">

        <!-- Número de ficha -->
        <div class="space-y-2">
          <label for="numero_ficha" class="text-sm font-medium">
            Número de ficha*
          </label>
          <input
            id="numero_ficha"
            type="text"
            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm input-siga"
            placeholder="2567890"
          />
        </div>

        <!-- Programa ocupa toda la fila -->
        <div class="space-y-2">
          <label for="id_programa" class="text-sm font-medium">
            Programa*
          </label>
          <select
            id="id_programa"
            class="w-full rounded-md border border-input bg-background px-3 pr-8 py-2 text-sm input-siga"
          >
            <option value="">Seleccione</option>
          </select>
        </div>

        <!-- Instructor -->
        <div class="space-y-2">
          <label for="id_instructor" class="text-sm font-medium">
            Instructor*
          </label>
          <select
            id="id_instructor"
            class="w-full rounded-md border border-input bg-background px-3 pr-8 py-2 text-sm input-siga"
          >
            <option value="">Seleccione un instructor</option>
          </select>
        </div>

        <!-- Modal footer actions -->
        <div class="flex justify-end gap-2 pt-4">
          <button
            type="button"
            id="btnCancelarModalFicha"
            class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-muted"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="inline-flex items-center justify-center rounded-md bg-emerald-500 px-4 py-2 text-sm font-medium text-white shadow hover:bg-emerald-600"
          >
            Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ========================================= -->
  <!-- MODAL: VIEW FICHA DETAILS                 -->
  <!-- ========================================= -->
  <div id="modalVerFicha" class="modal-overlay">
    <!-- Modal de detalles con diseño según imagen -->
    <div class="relative w-full max-w-md rounded-xl border border-border bg-gradient-to-br from-white to-emerald-50/30 p-6 shadow-lg">
      <!-- Modal header -->
      <div class="flex items-start justify-between gap-4 mb-4">
        <h2 class="text-lg font-semibold">Detalles de la Ficha</h2>
        <!-- Close button -->
        <button
          type="button"
          id="btnCerrarModalVerFicha"
          class="rounded-full p-1 hover:bg-muted"
        >
          <span class="sr-only">Cerrar</span>
          <svg
            class="h-5 w-5"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>

      <!-- Ficha details content, populated dynamically via JavaScript -->
      <div id="detalleFichaContent" class="space-y-4">
        <!-- Filled when a ficha is selected -->
      </div>
    </div>
  </div>

  <!-- ========================================= -->
  <!-- ALERT CONTAINER (FLOWBITE-LIKE TOASTS)    -->
  <!-- ========================================= -->
  <div
    id="flowbite-alert-container"
    class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 w-full max-w-md"
  ></div>

  <!-- ========================================= -->
  <!-- MODULE SCRIPT: FICHAS MANAGEMENT LOGIC    -->
  <!-- ========================================= -->
  <script src="src/assets/js/fichas/fichas.js"></script>

</body>
</html>
