<?php
// =====================================
// GESTIÓN DE USUARIOS – VERSIÓN PHP
// (Diseño basado en tu componente React)
// =====================================
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Tus estilos globales (variables, colores, etc.) -->
  <link rel="stylesheet" href="../../assets/css/globals.css">
  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    /* Animación similar a animate-fade-in-up */
    .animate-fade-in-up {
      opacity: 0;
      transform: translateY(8px);
      animation: fadeInUp 0.4s ease-out forwards;
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Overlay de los modales */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgb(15 23 42 / 0.6);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 50;
    }

    .modal-overlay.active {
      display: flex;
    }
  </style>
</head>
<body class="min-h-screen bg-background text-foreground">

  <!-- Si tienes header/sidebar de dashboard, los puedes incluir aquí -->
  <!-- <?php include '../partials/dashboard-header.php'; ?> -->

  <main class="max-w-7xl mx-auto px-4 py-8">
    <div class="space-y-6 animate-fade-in-up">

      <!-- HEADER -->
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight">Gestión de Usuarios</h1>
          <p class="text-muted-foreground">Administra los usuarios y sus roles en el sistema</p>
        </div>

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
          <span class="text-sm text-muted-foreground">Filtrar por rol</span>
          <select
            id="selectFiltroRol"
            class="rounded-md border border-input bg-background px-3 py-2 text-sm"
          >
            <option value="">Todos</option>
            <option value="coordinador">Coordinador</option>
            <option value="instructor">Instructor</option>
            <option value="pasante">Pasante</option>
            <option value="encargado_inventario">Encargado Inventario</option>
            <option value="encargado_bodega">Encargado Bodega</option>
          </select>
        </div>
      </div>

      <!-- TABLA DE USUARIOS (equivalente a DataTable) -->
      <div class="overflow-hidden rounded-xl border border-border bg-card">
        <table class="min-w-full divide-y divide-border text-sm">
          <thead class="bg-muted/40">
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

      <form id="formUsuario" class="space-y-4">
        <input type="hidden" id="hiddenUserId" value="">

        <div class="grid gap-4 sm:grid-cols-2">
          <div class="space-y-2 sm:col-span-2">
            <label for="nombre_completo" class="text-sm font-medium">Nombre completo *</label>
            <input
              id="nombre_completo"
              type="text"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
              placeholder="Ej: Juan Pablo Hernández Castro"
              required
            />
          </div>

          <div class="space-y-2">
            <label for="tipo_documento" class="text-sm font-medium">Tipo de documento *</label>
            <select
              id="tipo_documento"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
              required
            >
              <option value="CC">Cédula de Ciudadanía</option>
              <option value="TI">Tarjeta de Identidad</option>
              <option value="CE">Cédula de Extranjería</option>
              <option value="PAS">Pasaporte</option>
            </select>
          </div>

          <div class="space-y-2">
            <label for="numero_documento" class="text-sm font-medium">Número de documento *</label>
            <input
              id="numero_documento"
              type="text"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
              placeholder="1098765432"
              required
            />
          </div>

          <div class="space-y-2">
            <label for="telefono" class="text-sm font-medium">Teléfono *</label>
            <input
              id="telefono"
              type="text"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
              placeholder="3101234567"
              required
            />
          </div>

          <div class="space-y-2">
            <label for="cargo" class="text-sm font-medium">Cargo / Rol *</label>
            <select
              id="cargo"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
              required
            >
              <option value="coordinador">Coordinador</option>
              <option value="instructor">Instructor</option>
              <option value="pasante">Pasante</option>
              <option value="encargado_inventario">Encargado de Inventario</option>
              <option value="encargado_bodega">Encargado de Bodega</option>
            </select>
          </div>

          <div class="space-y-2 sm:col-span-2">
            <label for="correo" class="text-sm font-medium">Correo electrónico *</label>
            <input
              id="correo"
              type="email"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
              placeholder="usuario@sena.edu.co"
              required
            />
          </div>

          <div class="space-y-2 sm:col-span-2">
            <label for="direccion" class="text-sm font-medium">Dirección *</label>
            <input
              id="direccion"
              type="text"
              class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
              placeholder="Calle 45 #23-10, Bogotá"
              required
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

  <!-- ========================================= -->
  <!-- MODAL VER DETALLES USUARIO               -->
  <!-- ========================================= -->
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

  <!-- ========================================= -->
  <!-- JS – LÓGICA EQUIVALENTE A useState React -->
  <!-- ========================================= -->
  <script>
    // ====== Configuración de roles (equivalente a roleLabels / roleBadgeStyles) ======
    const roleLabels = {
      coordinador: "Coordinador",
      instructor: "Instructor",
      pasante: "Pasante",
      encargado_inventario: "Encargado de Inventario",
      encargado_bodega: "Encargado de Bodega",
    };

    const roleBadgeStyles = {
      coordinador: "bg-chart-4/10 text-chart-4",
      instructor: "bg-primary/10 text-primary",
      pasante: "bg-chart-3/10 text-chart-3",
      encargado_inventario: "bg-chart-2/10 text-chart-2",
      encargado_bodega: "bg-chart-1/10 text-chart-1",
    };

    // ====== Mock users (equivalente a mockUsers) ======
    const mockUsers = [
      {
        id: "1",
        nombre_completo: "Ana María Rodríguez",
        tipo_documento: "CC",
        numero_documento: "1098765432",
        telefono: "3101234567",
        cargo: "coordinador",
        correo: "ana.rodriguez@sena.edu.co",
        direccion: "Calle 10 #12-34, Bogotá",
        estado: true,
        created_at: "2025-01-10",
      },
      {
        id: "2",
        nombre_completo: "Carlos Pérez",
        tipo_documento: "CC",
        numero_documento: "1022334455",
        telefono: "3209876543",
        cargo: "instructor",
        correo: "carlos.perez@sena.edu.co",
        direccion: "Carrera 7 #45-20, Bogotá",
        estado: true,
        created_at: "2025-01-12",
      },
      {
        id: "3",
        nombre_completo: "Laura Gómez",
        tipo_documento: "TI",
        numero_documento: "1002003004",
        telefono: "3007654321",
        cargo: "pasante",
        correo: "laura.gomez@sena.edu.co",
        direccion: "Av. Siempre Viva 123",
        estado: false,
        created_at: "2025-01-15",
      },
    ];

    let users = [...mockUsers];
    let selectedUser = null;

    // ====== Referencias DOM ======
    const tbodyUsuarios = document.getElementById("tbodyUsuarios");
    const inputBuscar = document.getElementById("inputBuscar");
    const selectFiltroRol = document.getElementById("selectFiltroRol");

    const modalUsuario = document.getElementById("modalUsuario");
    const btnNuevoUsuario = document.getElementById("btnNuevoUsuario");
    const btnCerrarModalUsuario = document.getElementById("btnCerrarModalUsuario");
    const btnCancelarModalUsuario = document.getElementById("btnCancelarModalUsuario");

    const formUsuario = document.getElementById("formUsuario");
    const hiddenUserId = document.getElementById("hiddenUserId");
    const modalUsuarioTitulo = document.getElementById("modalUsuarioTitulo");
    const modalUsuarioDescripcion = document.getElementById("modalUsuarioDescripcion");

    const inputNombreCompleto = document.getElementById("nombre_completo");
    const inputTipoDocumento = document.getElementById("tipo_documento");
    const inputNumeroDocumento = document.getElementById("numero_documento");
    const inputTelefono = document.getElementById("telefono");
    const inputCargo = document.getElementById("cargo");
    const inputCorreo = document.getElementById("correo");
    const inputDireccion = document.getElementById("direccion");

    const modalVerUsuario = document.getElementById("modalVerUsuario");
    const btnCerrarModalVerUsuario = document.getElementById("btnCerrarModalVerUsuario");
    const detalleUsuarioContent = document.getElementById("detalleUsuarioContent");

    // ====== Funciones helpers ======
    function getInitials(nombre) {
      return nombre
        .split(" ")
        .filter(Boolean)
        .map(n => n[0])
        .slice(0, 2)
        .join("")
        .toUpperCase();
    }

    function openModalUsuario(editUser = null) {
      selectedUser = editUser;
      modalUsuario.classList.add("active");

      if (editUser) {
        modalUsuarioTitulo.textContent = "Editar Usuario";
        modalUsuarioDescripcion.textContent = "Modifica la información del usuario";
        hiddenUserId.value = editUser.id;

        inputNombreCompleto.value = editUser.nombre_completo;
        inputTipoDocumento.value = editUser.tipo_documento;
        inputNumeroDocumento.value = editUser.numero_documento;
        inputTelefono.value = editUser.telefono;
        inputCargo.value = editUser.cargo;
        inputCorreo.value = editUser.correo;
        inputDireccion.value = editUser.direccion;
      } else {
        modalUsuarioTitulo.textContent = "Crear Nuevo Usuario";
        modalUsuarioDescripcion.textContent = "Complete los datos para registrar un nuevo usuario";
        hiddenUserId.value = "";
        formUsuario.reset();
        inputTipoDocumento.value = "CC";
        inputCargo.value = "instructor";
      }
    }

    function closeModalUsuario() {
      modalUsuario.classList.remove("active");
      selectedUser = null;
      hiddenUserId.value = "";
    }

    function openModalVerUsuario(user) {
      selectedUser = user;
      modalVerUsuario.classList.add("active");

      const estadoBadgeClass = user.estado
        ? "bg-success/10 text-success"
        : "bg-destructive/10 text-destructive";

      detalleUsuarioContent.innerHTML = `
        <div class="flex items-center gap-4">
          <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-primary text-xl">
            ${getInitials(user.nombre_completo)}
          </div>
          <div>
            <h3 class="font-semibold text-lg">${user.nombre_completo}</h3>
            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-secondary/10 ${roleBadgeStyles[user.cargo] || ""}">
              ${roleLabels[user.cargo] || user.cargo}
            </span>
          </div>
        </div>
        <div class="grid gap-3 text-sm">
          <div class="grid grid-cols-3 gap-2">
            <span class="text-muted-foreground">Documento:</span>
            <span class="col-span-2">${user.tipo_documento} ${user.numero_documento}</span>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <span class="text-muted-foreground">Teléfono:</span>
            <span class="col-span-2">${user.telefono}</span>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <span class="text-muted-foreground">Correo:</span>
            <span class="col-span-2">${user.correo}</span>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <span class="text-muted-foreground">Dirección:</span>
            <span class="col-span-2">${user.direccion}</span>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <span class="text-muted-foreground">Estado:</span>
            <span class="col-span-2 inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ${estadoBadgeClass}">
              ${user.estado ? "Activo" : "Inactivo"}
            </span>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <span class="text-muted-foreground">Registrado:</span>
            <span class="col-span-2">${user.created_at}</span>
          </div>
        </div>
      `;
    }

    function closeModalVerUsuario() {
      modalVerUsuario.classList.remove("active");
      selectedUser = null;
    }

    function toggleStatus(userId) {
      users = users.map(u =>
        u.id === userId ? { ...u, estado: !u.estado } : u
      );
      renderTable();
    }

    // ====== Render de la tabla ======
    function renderTable() {
      const search = inputBuscar.value.trim().toLowerCase();
      const rol = selectFiltroRol.value;

      const filtered = users.filter(u => {
        const matchName = u.nombre_completo.toLowerCase().includes(search);
        const matchRol = rol ? u.cargo === rol : true;
        return matchName && matchRol;
      });

      tbodyUsuarios.innerHTML = "";

      filtered.forEach(user => {
        const tr = document.createElement("tr");
        tr.className = "hover:bg-muted/40";

        const estadoBadgeClass = user.estado
          ? "bg-success/10 text-success"
          : "bg-destructive/10 text-destructive";

        tr.innerHTML = `
          <td class="px-4 py-3 align-middle">
            <div class="flex items-center gap-3">
              <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-primary text-sm">
                ${getInitials(user.nombre_completo)}
              </div>
              <div>
                <p class="font-medium text-sm">${user.nombre_completo}</p>
                <p class="text-xs text-muted-foreground">${user.correo}</p>
              </div>
            </div>
          </td>
          <td class="px-4 py-3 align-middle">
            <span class="text-sm">${user.tipo_documento} ${user.numero_documento}</span>
          </td>
          <td class="px-4 py-3 align-middle">
            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-secondary/10 ${roleBadgeStyles[user.cargo] || ""}">
              ${roleLabels[user.cargo] || user.cargo}
            </span>
          </td>
          <td class="px-4 py-3 align-middle">
            <span class="text-sm">${user.telefono}</span>
          </td>
          <td class="px-4 py-3 align-middle">
            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ${estadoBadgeClass}">
              ${user.estado ? "Activo" : "Inactivo"}
            </span>
          </td>
          <td class="px-4 py-3 align-middle text-right">
            <div class="relative inline-block text-left">
              <button
                type="button"
                class="inline-flex h-8 w-8 items-center justify-center rounded-md hover:bg-muted"
                data-menu-trigger="${user.id}"
              >
                <!-- Icono MoreHorizontal -->
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.75a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm0 4.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm0 4.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                </svg>
              </button>
              <div
                class="hidden absolute right-0 mt-2 w-48 rounded-md border border-border bg-popover shadow-md z-20"
                data-menu="${user.id}"
              >
                <button
                  type="button"
                  class="flex w-full items-center px-3 py-2 text-sm hover:bg-muted"
                  data-action="ver"
                  data-id="${user.id}"
                >
                  <!-- Eye -->
                  <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                       viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  Ver detalles
                </button>
                <button
                  type="button"
                  class="flex w-full items-center px-3 py-2 text-sm hover:bg-muted"
                  data-action="editar"
                  data-id="${user.id}"
                >
                  <!-- Edit -->
                  <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                       viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                  Editar
                </button>
                <hr class="border-border">
                <button
                  type="button"
                  class="flex w-full items-center px-3 py-2 text-sm hover:bg-muted"
                  data-action="toggle"
                  data-id="${user.id}"
                >
                  ${
                    user.estado
                      ? `
                        <!-- UserX -->
                        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 21v-2a4 4 0 014-4h4m4 0h1a4 4 0 014 4v2M16 3l5 5M21 3l-5 5"/>
                        </svg>
                        Desactivar
                      `
                      : `
                        <!-- UserCheck -->
                        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 21v-2a4 4 0 014-4h4m4 0h1a4 4 0 014 4v2M9 7l2 2 4-4"/>
                        </svg>
                        Activar
                      `
                  }
                </button>
              </div>
            </div>
          </td>
        `;

        tbodyUsuarios.appendChild(tr);
      });

      attachMenuEvents();
    }

    // Manejo de dropdown menú (tres puntitos)
    function attachMenuEvents() {
      // Cerrar todos cuando hago click fuera
      document.addEventListener("click", (e) => {
        if (!e.target.closest("[data-menu-trigger]") && !e.target.closest("[data-menu]")) {
          document.querySelectorAll("[data-menu]").forEach(el => el.classList.add("hidden"));
        }
      }, { once: true });

      // Toggle del menú
      document.querySelectorAll("[data-menu-trigger]").forEach(btn => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const id = btn.getAttribute("data-menu-trigger");
          const menu = document.querySelector(`[data-menu="${id}"]`);
          if (!menu) return;
          const isHidden = menu.classList.contains("hidden");
          document.querySelectorAll("[data-menu]").forEach(el => el.classList.add("hidden"));
          if (isHidden) menu.classList.remove("hidden");
        });
      });

      // Acciones del menú
      document.querySelectorAll("[data-menu] [data-action]").forEach(btn => {
        btn.onclick = () => {
          const action = btn.getAttribute("data-action");
          const id = btn.getAttribute("data-id");
          const user = users.find(u => u.id === id);
          if (!user) return;

          if (action === "ver") {
            openModalVerUsuario(user);
          } else if (action === "editar") {
            openModalUsuario(user);
          } else if (action === "toggle") {
            toggleStatus(id);
          }
        };
      });
    }

    // ====== Eventos globales ======
    inputBuscar.addEventListener("input", renderTable);
    selectFiltroRol.addEventListener("change", renderTable);

    btnNuevoUsuario.addEventListener("click", () => openModalUsuario(null));
    btnCerrarModalUsuario.addEventListener("click", closeModalUsuario);
    btnCancelarModalUsuario.addEventListener("click", closeModalUsuario);

    btnCerrarModalVerUsuario.addEventListener("click", closeModalVerUsuario);

    // Enviar formulario crear/editar
    formUsuario.addEventListener("submit", (e) => {
      e.preventDefault();

      const payload = {
        id: hiddenUserId.value || String(users.length + 1),
        nombre_completo: inputNombreCompleto.value.trim(),
        tipo_documento: inputTipoDocumento.value,
        numero_documento: inputNumeroDocumento.value.trim(),
        telefono: inputTelefono.value.trim(),
        cargo: inputCargo.value,
        correo: inputCorreo.value.trim(),
        direccion: inputDireccion.value.trim(),
      };

      if (hiddenUserId.value) {
        // Editar
        users = users.map(u =>
          u.id === hiddenUserId.value ? { ...u, ...payload } : u
        );
      } else {
        // Nuevo
        users.push({
          ...payload,
          estado: true,
          created_at: new Date().toISOString().split("T")[0],
        });
      }

      closeModalUsuario();
      renderTable();
    });

    // Render inicial
    renderTable();
  </script>
</body>
</html>
