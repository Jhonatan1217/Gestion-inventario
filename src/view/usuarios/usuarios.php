<?php
// =====================================
// GESTIÓN DE USUARIOS – VERSIÓN PHP
// (Diseño basado en tu componente React)
// =====================================

// 👇 leer el estado del sidebar desde la URL (?coll=1)
$collapsed = isset($_GET['coll']) && $_GET['coll'] == '1';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script>
  <link rel="stylesheet" href="src/assets/css/usuarios/usuarios.css" />
  <script>
  const AUTH_USER_ID = <?= $_SESSION['usuario_id']; ?>;
  </script>

</head>

<body
  class="min-h-screen bg-background text-foreground transition-all duration-300
    <?php echo $collapsed ? 'lg:pl-[70px]' : 'lg:pl-[260px]'; ?>"
>

  <!-- Si tienes header/sidebar de dashboard, los puedes incluir aquí -->
  <!-- <?php include '../partials/dashboard-header.php'; ?> -->

  <main class="page-with-sidebar max-w-7xl mx-auto px-4 py-8">
    <div class="space-y-6 animate-fade-in-up">

      <!-- HEADER -->
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight">Gestión de Usuarios</h1>
          <p class="text-muted-foreground">Administra los usuarios y sus roles en el sistema</p>
        </div>

        <!-- CONTROLES DERECHA: SWITCH VISTA + BOTÓN NUEVO -->
        <div class="flex items-center gap-3">
          <!-- Switch de lista / tarjetas -->
          <div class="inline-flex rounded-lg border border-border bg-card shadow-sm overflow-hidden">
            <!-- Lista -->
            <button
              type="button"
              id="btnVistaTabla"
              class="px-3 py-2 text-xs sm:text-sm flex items-center gap-1 bg-muted text-foreground"
            >
              <!-- Icono lista -->
              <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>

            <!-- Tarjetas -->
            <button
              type="button"
              id="btnVistaTarjetas"
              class="px-3 py-2 text-xs sm:text-sm flex items-center gap-1 text-muted-foreground"
            >
              <!-- Icono grid -->
              <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <rect x="4" y="4" width="7" height="7" rx="1"></rect>
                <rect x="13" y="4" width="7" height="7" rx="1"></rect>
                <rect x="4" y="13" width="7" height="7" rx="1"></rect>
                <rect x="13" y="13" width="7" height="7" rx="1"></rect>
              </svg>
            </button>
          </div>

          <!-- Botón nuevo usuario -->
          <button
            id="btnNuevoUsuario"
            class="inline-flex items-center justify-center rounded-md bg-secondary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm hover:opacity-90 gap-2"
            type="button"
          >
            <!-- Icono Plus -->
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Usuario
          </button>
        </div>
      </div>

      <!-- FILTROS SUPERIORES (Buscar + Filtro rol) -->
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="w-full sm:max-w-xs">
          <input
            id="inputBuscar"
            type="text"
            placeholder="Buscar por nombre..."
            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
          />
        </div>
        <div class="flex items-center gap-2">
          <!-- Ícono de filtro en lugar de texto "Filtrar por rol" --> 
          <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
            <path
              d="M5 5h14a1 1 0 0 1 .8 1.6L15 12v4.5a1 1 0 0 1-.553.894l-3 1.5A1 1 0 0 1 10 18v-6L4.2 6.6A1 1 0 0 1 5 5z"
              stroke="currentColor"
              stroke-width="1.8"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>

          <select
            id="selectFiltroRol"
            class="rounded-md border border-input bg-background px-3 pr-10 py-2 text-sm"
          >
            <option value="">Todos</option>
            <!-- CARGOS EXACTOS QUE EXISTEN EN LA BD -->
            <option value="Coordinador">Coordinador</option>
            <option value="Subcoordinador">Subcoordinador</option>
            <option value="Instructor">Instructor</option>
            <option value="Pasante">Pasante</option>
            <option value="Aprendiz">Aprendiz</option>
          </select>
        </div>
      </div>

      <!-- CONTENEDOR VISTA TABLA -->
      <!-- overflow-visible + relative para que el dropdown no se corte -->
      <div id="vistaTabla" class="overflow-visible rounded-xl border border-border bg-card relative">
        <table class="min-w-full divide-y divide-border text-sm">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">Usuario</th>
              <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">Documento</th>
              <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">Rol</th>
              <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">Teléfono</th>
              <th class="px-4 py-3 text-left font-medium text-xs text-muted-foreground">Estado</th>
              <th class="px-4 py-3 text-right font-medium text-xs text-muted-foreground">Acciones</th>
            </tr>
          </thead>
          <tbody id="tbodyUsuarios" class="divide-y divide-border bg-card">
            <!-- Se llena dinámicamente con JS -->
          </tbody>
        </table>
      </div>

      <!-- CONTENEDOR VISTA TARJETAS -->
      <div id="vistaTarjetas" class="hidden">
        <div
          id="cardsContainer"
          class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3"
        >
          <!-- Se llena dinámicamente con JS -->
        </div>
      </div>

    </div>
  </main>

  <!-- ========================================= -->
  <!-- MODAL CREAR / EDITAR USUARIO (Dialog)    -->
  <!-- ========================================= -->
  <div id="modalUsuario" class="modal-overlay">
    <div class="relative w-full max-w-2xl rounded-xl border border-border bg-card p-6 shadow-lg">
      <div class="flex items-start justify-between gap-4 mb-4">
        <div>
          <h2 id="modalUsuarioTitulo" class="text-lg font-semibold">Crear Nuevo Usuario</h2>
          <p id="modalUsuarioDescripcion" class="text-sm text-muted-foreground">
            Complete los datos para registrar un nuevo usuario
          </p>
        </div>
        <button
          type="button"
          id="btnCerrarModalUsuario"
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

      <form id="formUsuario" class="space-y-4" novalidate>
        <input type="hidden" id="hiddenUserId" value="">

        <div class="grid gap-4 sm:grid-cols-2">
          <!-- Nombre completo full width -->
          <div class="space-y-2 sm:col-span-2">
            <label for="nombre_completo" class="text-sm font-medium">Nombre completo *</label>
            <input
              id="nombre_completo"
              type="text"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm input-siga"
              placeholder="Ej: Juan Pablo Hernández Castro"
            />
          </div>

          <!-- Tipo documento -->
          <div class="space-y-2">
            <label for="tipo_documento" class="text-sm font-medium">Tipo de documento *</label>
            <select
              id="tipo_documento"
              class="w-full rounded-md border border-input bg-background px-3 pr-10 py-2 text-sm input-siga"
            >
              <!-- TIPOS DE DOCUMENTO EXACTOS QUE EXISTEN EN LA BD -->
              <option value="CC">CC</option>
              <option value="TI">TI</option>
              <option value="CE">CE</option>
            </select>
          </div>

          <!-- Número documento -->
          <div class="space-y-2">
            <label for="numero_documento" class="text-sm font-medium">Número de documento *</label>
            <input
              id="numero_documento"
              type="text"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm input-siga"
              placeholder="1098765432"
            />
          </div>

          <!-- Teléfono -->
          <div class="space-y-2">
            <label for="telefono" class="text-sm font-medium">Teléfono *</label>
            <input
              id="telefono"
              type="text"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm input-siga"
              placeholder="3101234567"
            />
          </div>

          <!-- Cargo / Rol -->
          <div class="space-y-2">
            <label for="cargo" class="text-sm font-medium">Cargo / Rol *</label>
            <select
              id="cargo"
              class="w-full rounded-md border border-input bg-background px-3 pr-10 py-2 text-sm input-siga"
            >
              <!-- CARGOS EXACTOS QUE EXISTEN EN LA BD -->
              <option value="Coordinador">Coordinador</option>
              <option value="Subcoordinador">Subcoordinador</option>
              <option value="Instructor">Instructor</option>
              <option value="Pasante">Pasante</option>
              <option value="Aprendiz">Aprendiz</option>
            </select>
          </div>

          <!-- Programa de formación (solo para Instructor) -->
          <div class="space-y-2 sm:col-span-2 hidden" id="wrapper_programa">
            <label for="id_programa" class="text-sm font-medium">Programa de formación *</label>
            <select
              id="id_programa"
              class="w-full rounded-md border border-input bg-background px-3 pr-10 py-2 text-sm input-siga"
            >
              <option value="">Seleccione un programa</option>
            </select>
          </div>

          <!-- Correo (full) -->
          <div class="space-y-2 sm:col-span-2">
            <label for="correo" class="text-sm font-medium">Correo electrónico *</label>
            <input
              id="correo"
              type="email"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm input-siga"
              placeholder="usuario@sena.edu.co"
            />
          </div>

          <!-- Contraseña (full) -->
          <div class="space-y-2 sm:col-span-2">
            <label for="password" class="text-sm font-medium">Contraseña *</label>
            <input
              id="password"
              type="password"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm input-siga"
              placeholder="Ingrese una contraseña segura"
            />
          </div>

          <!-- Dirección (full) -->
          <div class="space-y-2 sm:col-span-2">
            <label for="direccion" class="text-sm font-medium">Dirección *</label>
            <input
              id="direccion"
              type="text"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm input-siga"
              placeholder="Calle 45 #23-10, Bogotá"
            />
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-4">
          <button
            type="button"
            id="btnCancelarModalUsuario"
            class="inline-flex items-center justify-center rounded-md border border-input bg-background px-4 py-2 text-sm font-medium hover:bg-muted"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:opacity-90"
          >
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL VER DETALLES USUARIO -->
  <div id="modalVerUsuario" class="modal-overlay">
    <div class="relative w-full max-w-lg rounded-xl border border-border bg-card p-6 shadow-lg">
      <div class="flex items-start justify-between gap-4 mb-4">
        <h2 class="text-lg font-semibold">Detalles del Usuario</h2>
        <button
          type="button"
          id="btnCerrarModalVerUsuario"
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

      <div id="detalleUsuarioContent" class="space-y-4">
        <!-- Se llena con JS cuando se selecciona un usuario -->
      </div>
    </div>
  </div>

  <!-- Contenedor para las alertas tipo Flowbite -->
  <div
    id="flowbite-alert-container"
    class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 w-full max-w-md"
  ></div>

  <!-- JS – LÓGICA EQUIVALENTE A useState React -->
  <script src="src/assets/js/usuarios/usuarios.js"></script>

</body>
</html>
