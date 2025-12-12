// =========================
// CONFIG: URL DEL CONTROLADOR
// =========================
const API_URL = "src/controllers/usuario_controller.php"; // 👈 esta ruta no se toca
const PROGRAMAS_API_URL = "src/controllers/programa_controller.php"; // 👈 ajusta si tu controlador tiene otro nombre

// ====== Configuración de roles (equivalente a roleLabels / roleBadgeStyles) ======
const roleLabels = {
  "Coordinador": "Coordinador",
  "Subcoordinador": "Subcoordinador",
  "Instructor": "Instructor",
  "Pasante": "Pasante",
  "Aprendiz": "Aprendiz",
};

// Clases definidas en tu globals.css
const roleBadgeStyles = {
  "Coordinador": "badge-role-coordinador",
  "Subcoordinador": "badge-role-coordinador",
  "Instructor": "badge-role-instructor",
  "Pasante": "badge-role-pasante",
  // 👇 Aprendiz usa el MISMO estilo que Instructor (clase tuya)
  "Aprendiz": "badge-role-parendiz",
};

// =========================
// LISTAS VÁLIDAS SEGÚN BD
// =========================
const VALID_TIPOS_DOCUMENTO = ["CC", "TI", "CE"];
const VALID_CARGOS = ["Coordinador", "Subcoordinador", "Instructor", "Pasante", "Aprendiz"];

// Aquí vivirá siempre la lista que se usa para pintar la tabla/tarjetas
let users = [];
let originalEditData = null; // guarda los datos originales cuando se edita
let selectedUser = null;
let programas = [];
let programasMap = {}; // id_programa => nombre_programa

// =========================
// PAGINACIÓN
// =========================
const PAGE_SIZE_TABLE = 10; // 👈 10 elementos por página en tabla
const PAGE_SIZE_CARDS = 9;  // 👈 9 elementos por página en tarjetas

let currentPageTable = 1;
let currentPageCards = 1;

// =========================
// ALERTAS TIPO FLOWBITE (FONDO BLANCO, WARNING, SIN BARRA)
// =========================

function getOrCreateFlowbiteContainer() {
  let container = document.getElementById("flowbite-alert-container");

  if (!container) {
    container = document.createElement("div");
    container.id = "flowbite-alert-container";

    container.className =
      "fixed top-6 left-1/2 -translate-x-1/2 z-[9999] flex flex-col gap-3 w-full max-w-md px-4 pointer-events-none";

    document.body.appendChild(container);
  }

  return container;
}

function showFlowbiteAlert(type, message) {
  const container = getOrCreateFlowbiteContainer();
  const wrapper = document.createElement("div");

  // 🔸 Estilo advertencia por defecto (warning)
  let borderColor = "border-amber-500";
  let textColor = "text-amber-900";
  let titleText = "Advertencia";

  // Icono por defecto: triángulo de advertencia
  let iconSVG = `
    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
         fill="currentColor" viewBox="0 0 20 20">
      <path d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l6.518 11.59A1.75 1.75 0 0 1 16.768 17H3.232a1.75 1.75 0 0 1-1.492-2.311L8.257 3.1z"/>
      <path d="M11 13H9V9h2zm0 3H9v-2h2z" fill="#fff"/>
    </svg>
  `;

  if (type === "success") {
    borderColor = "border-emerald-500";
    textColor = "text-emerald-900";
    titleText = "Éxito";
    iconSVG = `
      <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
           fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm-1 15-4-4 1.414-1.414L9 12.172l4.586-4.586L15 9z"/>
      </svg>
    `;
  }

  if (type === "info") {
    borderColor = "border-blue-500";
    textColor = "text-blue-900";
    titleText = "Información";
    iconSVG = `
      <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
           fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm1 15H9v-5h2Zm0-7H9V6h2Z"/>
      </svg>
    `;
  }

  // 👇 AQUÍ VA LA ANIMACIÓN DE ENTRADA
  wrapper.className = `
    relative flex items-center w-full mx-auto pointer-events-auto
    rounded-2xl border-l-4 ${borderColor} bg-white shadow-md
    px-4 py-3 text-sm ${textColor}
    opacity-0 -translate-y-2
    transition-all duration-300 ease-out
    animate-fade-in-up
  `;

  wrapper.innerHTML = `
    <div class="flex-shrink-0 mr-3 text-current">
      ${iconSVG}
    </div>

    <div class="flex-1 min-w-0">
      <p class="font-semibold">${titleText}</p>
      <p class="mt-0.5 text-sm">${message}</p>
    </div>
  `;

  container.appendChild(wrapper);

  // 🔥 Suavizado extra con transición (puedes dejarlo o quitarlo, no rompe el diseño)
  requestAnimationFrame(() => {
    wrapper.classList.remove("opacity-0", "-translate-y-2");
    wrapper.classList.add("opacity-100", "translate-y-0");
  });

  // 🔥 Animación de salida automática (la que ya tenías)
  setTimeout(() => {
    wrapper.classList.add("opacity-0", "-translate-y-2");
    wrapper.classList.remove("opacity-100", "translate-y-0");
    setTimeout(() => wrapper.remove(), 250);
  }, 4000);
}


// API que usa el resto del código
function toastError(message) {
  showFlowbiteAlert("warning", message);
}

function toastSuccess(message) {
  showFlowbiteAlert("success", message);
}

function toastInfo(message) {
  showFlowbiteAlert("info", message);
}

function validateUserPayload(payload, { isEdit = false, currentId = null } = {}) {
  const nameRegex = /^[A-Za-zÁÉÍÓÚÜÑÑáéíóúüñ\s]{3,80}$/;
  const numeroRegex = /^[0-9]{6,15}$/;
  const telefonoRegex = /^[0-9]{7,15}$/;
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!payload.nombre_completo || !nameRegex.test(payload.nombre_completo)) {
    toastError("Nombre inválido: solo letras y 3-80 caracteres.");
    inputNombreCompleto.focus();
    return false;
  }

  if (!payload.tipo_documento) {
    toastError("Seleccione el tipo de documento.");
    inputTipoDocumento.focus();
    return false;
  }

  if (!payload.numero_documento || !numeroRegex.test(payload.numero_documento)) {
    toastError("Número de documento inválido (solo números, 6-15 dígitos).");
    inputNumeroDocumento.focus();
    return false;
  }

  if (!payload.telefono || !telefonoRegex.test(payload.telefono)) {
    toastError("Teléfono inválido (solo números, 7-15 dígitos).");
    inputTelefono.focus();
    return false;
  }

  if (!payload.correo || !emailRegex.test(payload.correo)) {
    toastError("Correo inválido. Debe contener '@' y dominio.");
    inputCorreo.focus();
    return false;
  }

  if (!payload.cargo) {
    toastError("Seleccione un cargo.");
    inputCargo.focus();
    return false;
  }

  if (!payload.direccion || payload.direccion.length < 5) {
    toastError("La dirección debe tener al menos 5 caracteres.");
    inputDireccion.focus();
    return false;
  }

  if (!VALID_TIPOS_DOCUMENTO.includes(payload.tipo_documento)) {
    toastError("Tipo de documento no válido (CC, TI o CE).");
    return false;
  }

  if (!VALID_CARGOS.includes(payload.cargo)) {
    toastError("Cargo no válido.");
    return false;
  }

  if (payload.cargo === "Instructor" && !payload.id_programa) {
    toastError("Debe seleccionar un programa para el Instructor.");
    return false;
  }

  if (!isEdit && (!payload.password || payload.password.length < 6)) {
    toastError("La contraseña es obligatoria (mínimo 6 caracteres).");
    inputPassword.focus();
    return false;
  }

  if (isEdit && payload.password && payload.password.length < 6) {
    toastError("La contraseña debe tener al menos 6 caracteres.");
    inputPassword.focus();
    return false;
  }

  // Duplicados locales
  const docDuplicado = users.some((u) =>
    u.numero_documento === payload.numero_documento && (!isEdit || String(u.id_usuario) !== String(currentId))
  );
  if (docDuplicado) {
    toastError("Ya existe un usuario con ese número de documento.");
    return false;
  }

  const correoDuplicado = users.some((u) =>
    u.correo.toLowerCase() === payload.correo.toLowerCase() && (!isEdit || String(u.id_usuario) !== String(currentId))
  );
  if (correoDuplicado) {
    toastError("Ya existe un usuario con ese correo.");
    return false;
  }

  return true;
}


// ====== Referencias DOM ======
const tbodyUsuarios = document.getElementById("tbodyUsuarios");
const inputBuscar = document.getElementById("inputBuscar");
const selectFiltroRol = document.getElementById("selectFiltroRol");

const vistaTabla = document.getElementById("vistaTabla");
const vistaTarjetas = document.getElementById("vistaTarjetas");
const cardsContainer = document.getElementById("cardsContainer");
const btnVistaTabla = document.getElementById("btnVistaTabla");
const btnVistaTarjetas = document.getElementById("btnVistaTarjetas");

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
const inputPassword = document.getElementById("password");
const inputDireccion = document.getElementById("direccion");

// Select de programa y wrapper
const inputPrograma = document.getElementById("id_programa");
const wrapperPrograma = document.getElementById("wrapper_programa");

const modalVerUsuario = document.getElementById("modalVerUsuario");
const btnCerrarModalVerUsuario = document.getElementById("btnCerrarModalVerUsuario");
const detalleUsuarioContent = document.getElementById("detalleUsuarioContent");

// ===== CONTENEDOR ÚNICO DE PAGINACIÓN =====
let paginationTabla = document.getElementById("paginationTabla"); // será el único

function ensurePaginationContainer() {
  if (vistaTarjetas && !paginationTabla) {
    paginationTabla = document.createElement("div");
    paginationTabla.id = "paginationTabla";
    paginationTabla.className = "mt-4 flex justify-end gap-2";
    // lo colocamos justo después de la última vista (quedará por fuera de ambas)
    vistaTarjetas.parentNode.insertBefore(paginationTabla, vistaTarjetas.nextSibling);
  }
}

ensurePaginationContainer();

// ====== Helpers ======
function getInitials(nombre) {
  return nombre
    .split(" ")
    .filter(Boolean)
    .map((n) => n[0])
    .slice(0, 2)
    .join("")
    .toUpperCase();
}

function actualizarVisibilidadPrograma() {
  if (!inputPrograma || !wrapperPrograma) return;
  const esInstructor = inputCargo.value === "Instructor";
  if (esInstructor) {
    wrapperPrograma.classList.remove("hidden");
  } else {
    wrapperPrograma.classList.add("hidden");
    inputPrograma.value = "";
  }
}

function renderOpcionesPrograma() {
  if (!inputPrograma) return;
  inputPrograma.innerHTML = '<option value="">Seleccione un programa</option>';
  programas.forEach((p) => {
    const opt = document.createElement("option");
    opt.value = p.id_programa;
    opt.textContent = p.nombre_programa || p.nombre || "";
    inputPrograma.appendChild(opt);
  });
}

async function cargarProgramas() {
  if (!inputPrograma) return;
  try {
    const res = await fetch(`${PROGRAMAS_API_URL}?accion=listar`);
    const text = await res.text();
    console.log("Respuesta listar programas (cruda):", text);

    let data;
    try {
      const start = text.indexOf("[");
      const end = text.lastIndexOf("]");
      if (start !== -1 && end !== -1 && end > start) {
        data = JSON.parse(text.slice(start, end + 1));
      } else {
        console.error("Respuesta inesperada al listar programas:", text);
        data = [];
      }
    } catch (e) {
      console.error("Error parseando listar programas:", e, text);
      data = [];
    }

    if (Array.isArray(data)) {
      programas = data.map((p) => ({
        id_programa: p.id_programa,
        nombre_programa: p.nombre_programa || p.nombre || "",
      }));

      programasMap = {};
      programas.forEach((p) => {
        programasMap[String(p.id_programa)] = p.nombre_programa;
      });

      renderOpcionesPrograma();
    }
  } catch (error) {
    console.error("Error al cargar programas:", error);
  }
}

function openModalUsuario(editUser = null) {
  selectedUser = editUser;
  modalUsuario.classList.add("active");

  // 👉 contenedor del campo contraseña (no tocamos HTML, solo buscamos el padre)
  let passwordWrapper = null;
  if (inputPassword) {
    passwordWrapper = inputPassword.closest(".space-y-2") ||
                      inputPassword.closest(".grid") ||
                      inputPassword.closest("div");
  }

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
    inputPassword.value = "";
    inputDireccion.value = editUser.direccion;

        // 🧾 Guardamos una foto de los datos originales para validar cambios
    originalEditData = {
      nombre_completo: editUser.nombre_completo?.trim() || "",
      tipo_documento: editUser.tipo_documento || "",
      numero_documento: String(editUser.numero_documento ?? "").trim(),
      telefono: String(editUser.telefono ?? "").trim(),
      cargo: editUser.cargo || "",
      correo: editUser.correo?.trim() || "",
      direccion: editUser.direccion?.trim() || "",
      id_programa:
        editUser.cargo === "Instructor" && editUser.id_programa
          ? String(editUser.id_programa)
          : null,
    };


    // 🔒 En editar: ocultamos el campo contraseña
    if (passwordWrapper) {
      passwordWrapper.classList.add("hidden");
    }

    if (inputPrograma) {
      if (editUser.cargo === "Instructor") {
        wrapperPrograma.classList.remove("hidden");
        renderOpcionesPrograma();
        if (editUser.id_programa) {
          inputPrograma.value = editUser.id_programa;
        } else {
          inputPrograma.value = "";
        }
      } else {
        wrapperPrograma.classList.add("hidden");
        inputPrograma.value = "";
      }
    }
    } else {
    modalUsuarioTitulo.textContent = "Crear Nuevo Usuario";
    modalUsuarioDescripcion.textContent = "Complete los datos para registrar un nuevo usuario";
    hiddenUserId.value = "";
    formUsuario.reset();
    inputTipoDocumento.value = "CC";
    inputCargo.value = "Aprendiz";
    if (inputPrograma) inputPrograma.value = "";
    actualizarVisibilidadPrograma();

    // 🧹 En crear, no hay datos originales
    originalEditData = null;

    if (passwordWrapper) {
      passwordWrapper.classList.remove("hidden");
    }
  }
}

function closeModalUsuario() {
  modalUsuario.classList.remove("active");
  selectedUser = null;
  hiddenUserId.value = "";
  originalEditData = null; // 🧹 limpiamos el estado original al cerrar
}


function openModalVerUsuario(user) {
  selectedUser = user;
  modalVerUsuario.classList.add("active");

  const estadoBadgeClass = user.estado
    ? "badge-estado-activo"
    : "badge-estado-inactivo";

  const programaNombre =
    user.cargo === "Instructor" && user.id_programa
      ? programasMap[String(user.id_programa)] || "Sin programa asignado"
      : null;

  detalleUsuarioContent.innerHTML = `
      <div class="flex items-center gap-4">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-avatar-secondary-39 text-secondary text-xl">
          ${getInitials(user.nombre_completo)}
        </div>
        <div>
          <h3 class="font-semibold text-lg">${user.nombre_completo}</h3>
          <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ${
            roleBadgeStyles[user.cargo] || "badge-role-default"
          }">
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
          <div class="col-span-2">
            <span class="badge-estado-base ${estadoBadgeClass}">
              ${user.estado ? "Activo" : "Inactivo"}
            </span>
          </div>
        </div>
        <div class="grid grid-cols-3 gap-2">
          <span class="text-muted-foreground">Registrado:</span>
          <span class="col-span-2">${user.created_at || ""}</span>
        </div>
        ${
          programaNombre
            ? `
        <div class="grid grid-cols-3 gap-2">
          <span class="text-muted-foreground">Programa:</span>
          <span class="col-span-2 font-medium">${programaNombre}</span>
        </div>
        `
            : ""
        }
      </div>
    `;
}

function closeModalVerUsuario() {
  modalVerUsuario.classList.remove("active");
  selectedUser = null;
}

// =========================
// LÓGICA PARA HABLAR CON EL BACKEND
// =========================

async function callApi(url, payload) {
  const res = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });

  const text = await res.text();
  console.log("Respuesta cruda del servidor:", text);

  try {
    const start = text.indexOf("{");
    const end = text.lastIndexOf("}");
    if (start !== -1 && end !== -1 && end > start) {
      const jsonString = text.slice(start, end + 1);
      return JSON.parse(jsonString);
    }
    return { error: "Respuesta no válida del servidor: " + text };
  } catch (e) {
    console.error("Error parseando JSON:", e);
    return { error: "Respuesta no válida del servidor: " + text };
  }
}

async function cargarUsuarios() {
  try {
    const res = await fetch(`${API_URL}?accion=listar`);
    const text = await res.text();
    console.log("Respuesta listar (cruda):", text);

    let data;
    try {
      const start = text.indexOf("[");
      const end = text.lastIndexOf("]");
      if (start !== -1 && end !== -1 && end > start) {
        data = JSON.parse(text.slice(start, end + 1));
      } else {
        console.error("Respuesta inesperada al listar usuarios:", text);
        data = [];
      }
    } catch (e) {
      console.error("Error parseando listar:", e, text);
      data = [];
    }

    if (!Array.isArray(data)) {
      console.error("Respuesta inesperada al listar usuarios:", data);
      users = [];
    } else {
      users = data.map((u) => {
        let estadoBool = true;

        if (typeof u.estado !== "undefined" && u.estado !== null) {
          const raw = String(u.estado).toLowerCase().trim();

          if (raw === "activo" || raw === "1" || raw === "true") {
            estadoBool = true;
          } else if (raw === "inactivo" || raw === "0" || raw === "false") {
            estadoBool = false;
          }
        }

        return {
          id: u.id_usuario,
          nombre_completo: u.nombre_completo,
          tipo_documento: u.tipo_documento,
          numero_documento: u.numero_documento,
          telefono: u.telefono,
          cargo: u.cargo,
          correo: u.correo,
          direccion: u.direccion,
          estado: estadoBool,
          created_at: u.created_at || "",
          id_programa: u.id_programa ?? null,
        };
      });
    }

    renderTable();
  } catch (error) {
    console.error("Error al cargar usuarios:", error);
    users = [];
    renderTable();
  }
}

function crearUsuario(payload) {
  return callApi(`${API_URL}?accion=crear`, payload);
}

function actualizarUsuario(payload) {
  return callApi(`${API_URL}?accion=actualizar`, payload);
}

// 🔹 OPCIONAL: si tienes endpoint para estado
function cambiarEstadoUsuario(payload) {
  return callApi(`${API_URL}?accion=cambiar_estado`, payload);
}

// 🔹 toggleStatus hablando con backend (no toca diseño)
async function toggleStatus(userId) {
  const user = users.find((u) => String(u.id) === String(userId));
  if (!user) return;

  const nuevoEstado = user.estado ? 0 : 1; // 1 = activo, 0 = inactivo

  try {
    const data = await cambiarEstadoUsuario({
      id_usuario: userId,
      estado: nuevoEstado,
    });

    console.log("Respuesta cambiar_estado:", data);

    if (data.error) {
      toastError(data.error || "No se pudo cambiar el estado del usuario.");
      return;
    }

    users = users.map((u) =>
      String(u.id) === String(userId) ? { ...u, estado: !!nuevoEstado } : u
    );
    renderTable();

    toastSuccess(
      nuevoEstado === 1
        ? "Usuario activado correctamente."
        : "Usuario desactivado correctamente."
    );
  } catch (error) {
    console.error("Error al cambiar estado:", error);
    toastError("Ocurrió un error al cambiar el estado (red/servidor).");
  }
}

// ====== Cambiar vista lista / tarjetas ======
function setVistaTabla() {
  vistaTabla.classList.remove("hidden");
  vistaTarjetas.classList.add("hidden");

  btnVistaTabla.classList.add("bg-muted", "text-foreground");
  btnVistaTarjetas.classList.remove("bg-muted");
  btnVistaTarjetas.classList.add("text-muted-foreground");

  renderTable();
}

function setVistaTarjetas() {
  vistaTabla.classList.add("hidden");
  vistaTarjetas.classList.remove("hidden");

  btnVistaTarjetas.classList.add("bg-muted", "text-foreground");
  btnVistaTabla.classList.remove("bg-muted");
  btnVistaTabla.classList.add("text-muted-foreground");

  renderTable();
}

// =========================
// RENDER PAGINACIÓN (GENÉRICO)
// =========================
function renderPaginationControls(container, totalItems, pageSize, currentPage, onPageChange) {
  if (!container) return;

  const totalPages = Math.ceil(totalItems / pageSize);

  if (totalPages <= 1) {
    container.innerHTML = "";
    return;
  }

  container.innerHTML = "";

  const btnPrev = document.createElement("button");
  btnPrev.type = "button";
  btnPrev.className =
    "px-3 py-1 text-sm rounded-lg border border-border bg-card hover:bg-muted disabled:opacity-40";
  btnPrev.textContent = "Anterior";
  btnPrev.disabled = currentPage === 1;
  btnPrev.addEventListener("click", () => {
    if (currentPage > 1) onPageChange(currentPage - 1);
  });
  container.appendChild(btnPrev);

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.type = "button";
    btn.textContent = i;
    btn.className =
      "px-3 py-1 text-sm rounded-lg border border-border " +
      (i === currentPage
        ? "bg-primary text-primary-foreground"
        : "bg-card hover:bg-muted");
    btn.addEventListener("click", () => {
      if (i !== currentPage) onPageChange(i);
    });
    container.appendChild(btn);
  }

  const btnNext = document.createElement("button");
  btnNext.type = "button";
  btnNext.className =
    "px-3 py-1 text-sm rounded-lg border border-border bg-card hover:bg-muted disabled:opacity-40";
  btnNext.textContent = "Siguiente";
  btnNext.disabled = currentPage === totalPages;
  btnNext.addEventListener("click", () => {
    if (currentPage < totalPages) onPageChange(currentPage + 1);
  });
  container.appendChild(btnNext);
}

// ====== Render de la tabla + tarjetas ======
function renderTable() {
  const search = inputBuscar.value.trim().toLowerCase();
  const rol = selectFiltroRol.value;

  const filtered = users.filter((u) => {

  // 🚫 No mostrar el usuario logueado
  if (typeof AUTH_USER_ID !== "undefined" && String(u.id) === String(AUTH_USER_ID)) {
    return false;
  }

  const matchName = u.nombre_completo.toLowerCase().includes(search);
  const matchRol = rol ? u.cargo === rol : true;
  return matchName && matchRol;
});


  const totalItems = filtered.length;

  const totalPagesTable = Math.max(1, Math.ceil(totalItems / PAGE_SIZE_TABLE) || 1);
  const totalPagesCards = Math.max(1, Math.ceil(totalItems / PAGE_SIZE_CARDS) || 1);

  if (currentPageTable > totalPagesTable) currentPageTable = totalPagesTable;
  if (currentPageCards > totalPagesCards) currentPageCards = totalPagesCards;

  const startIndexTable = (currentPageTable - 1) * PAGE_SIZE_TABLE;
  const endIndexTable = startIndexTable + PAGE_SIZE_TABLE;
  const pageItemsTable = filtered.slice(startIndexTable, endIndexTable);

  const startIndexCards = (currentPageCards - 1) * PAGE_SIZE_CARDS;
  const endIndexCards = startIndexCards + PAGE_SIZE_CARDS;
  const pageItemsCards = filtered.slice(startIndexCards, endIndexCards);

  // ====== TABLA ======
  tbodyUsuarios.innerHTML = "";

  pageItemsTable.forEach((user) => {
    const tr = document.createElement("tr");
    tr.className = "hover:bg-muted/40";

    const estadoBadgeClass = user.estado
      ? "badge-estado-activo"
      : "badge-estado-inactivo";

    tr.innerHTML = `
        <td class="px-4 py-3 align-middle">
          <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-avatar-secondary-39 text-secondary text-sm">
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
          <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ${
            roleBadgeStyles[user.cargo] || "badge-role-default"
          }">
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
              class="inline-flex h-8 w-8 items-center justify-center rounded-md hover:bg-muted text-slate-800"
              data-menu-trigger="${user.id}"
            >
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                   viewBox="0 0 24 24">
                <circle cx="5" cy="12" r="1.5"></circle>
                <circle cx="12" cy="12" r="1.5"></circle>
                <circle cx="19" cy="12" r="1.5"></circle>
              </svg>
            </button>
            <div
              class="dropdown-menu hidden absolute right-0 mt-2 w-48 rounded-xl border border-border bg-popover shadow-md py-1"
              data-menu="${user.id}"
            >
              <button
                type="button"
                class="flex w-full items-center px-3 py-2 text-sm text-slate-700 hover:bg-muted"
                data-action="ver"
                data-id="${user.id}"
              >
                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M1 12S4.5 5 12 5s11 7 11 7-3.5 7-11 7S1 12 1 12z"/>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
                Ver detalles
              </button>
              <button
                type="button"
                class="flex w-full items-center px-3 py-2 text-sm text-slate-700 hover:bg-muted"
                data-action="editar"
                data-id="${user.id}"
              >
                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 20h9"/>
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 3.5a2.121 2.121 0 0 1 3 3L9 17l-4 1 1-4 10.5-10.5z"/>
                </svg>
                Editar
              </button>
              <hr class="border-border my-1">
              <button
                type="button"
                class="flex w-full items-center px-3 py-2 text-sm text-slate-700 hover:bg-muted"
                data-action="toggle"
                data-id="${user.id}"
              >
                ${
                  user.estado
                    ? `
                      <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                           viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <circle cx="9" cy="7" r="3"></circle>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 21v-1a4 4 0 0 1 4-4h2"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 17l4 4m0-4l-4 4"/>
                      </svg>
                      Desactivar
                    `
                    : `
                      <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                           viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <circle cx="9" cy="7" r="3"></circle>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 21v-1a4 4 0 0 1 4-4h2"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 19l2 2 4-4"/>
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

  // ====== TARJETAS ======
  cardsContainer.innerHTML = "";

  pageItemsCards.forEach((user) => {
    const estadoBadgeClass = user.estado
      ? "badge-estado-activo"
      : "badge-estado-inactivo";

    const card = document.createElement("div");
    card.className =
      "rounded-2xl border border-border bg-card p-3 shadow-sm flex flex-col gap-2";

    card.innerHTML = `
        <div class="flex items-start justify-between gap-2">
          <div class="flex items-center gap-2">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-avatar-secondary-39 text-secondary text-xs">
              ${getInitials(user.nombre_completo)}
            </div>
            <div class="space-y-0.5">
              <p class="font-semibold text-xs sm:text-sm leading-snug">${user.nombre_completo}</p>
              <p class="text-[11px] sm:text-xs text-muted-foreground">${user.tipo_documento} ${user.numero_documento}</p>
            </div>
          </div>

          <div class="relative inline-block text-left">
            <button
              type="button"
              class="inline-flex h-6 w-6 items-center justify-center rounded-md hover:bg-muted text-slate-800"
              data-menu-trigger="${user.id}"
            >
              <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                   viewBox="0 0 24 24">
                <circle cx="5" cy="12" r="1.5"></circle>
                <circle cx="12" cy="12" r="1.5"></circle>
                <circle cx="19" cy="12" r="1.5"></circle>
              </svg>
            </button>
            <div
              class="dropdown-menu hidden absolute right-0 mt-2 w-40 rounded-xl border border-border bg-popover shadow-md py-1"
              data-menu="${user.id}"
            >
              <button
                type="button"
                class="flex w-full items-center px-3 py-2 text-xs text-slate-700 hover:bg-muted"
                data-action="ver"
                data-id="${user.id}"
              >
                <svg class="mr-2 h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M1 12S4.5 5 12 5s11 7 11 7-3.5 7-11 7S1 12 1 12z"/>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
                Ver detalles
              </button>
              <button
                type="button"
                class="flex w-full items-center px-3 py-2 text-xs text-slate-700 hover:bg-muted"
                data-action="editar"
                data-id="${user.id}"
              >
                <svg class="mr-2 h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 20h9"/>
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 3.5a2.121 2.121 0 0 1 3 3L9 17l-4 1 1-4 10.5-10.5z"/>
                </svg>
                Editar
              </button>
              <hr class="border-border my-1">
              <button
                type="button"
                class="flex w-full items-center px-3 py-2 text-xs text-slate-700 hover:bg-muted"
                data-action="toggle"
                data-id="${user.id}"
              >
                ${
                  user.estado
                    ? `
                      <svg class="mr-2 h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                           viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <circle cx="9" cy="7" r="3"></circle>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 21v-1a4 4 0 0 1 4-4h2"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 17l4 4m0-4l-4 4"/>
                      </svg>
                      Desactivar
                    `
                    : `
                      <svg class="mr-2 h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                           viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <circle cx="9" cy="7" r="3"></circle>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 21v-1a4 4 0 0 1 4-4h2"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16 19l2 2 4-4"/>
                      </svg>
                      Activar
                    `
                }
              </button>
            </div>
          </div>
        </div>

        <div class="space-y-1 text-[11px] sm:text-xs text-muted-foreground">
          <div class="flex items-center gap-2">
            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 6h16v12H4z"/>
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 6l8 6 8-6"/>
            </svg>
            <span>${user.correo}</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 5a2 2 0 0 1 2-2h2l2 5-2 1c1 2.5 3 4.5 5.5 5.5l1-2 5 2v2a2 2 0 0 1-2 2h-1C9.82 19 5 14.18 5 8V7a2 2 0 0 1-2-2z"/>
            </svg>
            <span>${user.telefono}</span>
          </div>
        </div>

        <div class="flex items-center justify-between mt-1">
          <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium ${
              roleBadgeStyles[user.cargo] || "badge-role-default"
            }">
              ${roleLabels[user.cargo] || user.cargo}
            </span>
            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium ${estadoBadgeClass}">
              ${user.estado ? "Activo" : "Inactivo"}
            </span>
          </div>
        </div>

        <hr class="border-border my-1" />

        <div class="flex justify-end">
          <button
  type="button"
  class="switch-siga ${user.estado ? 'on' : 'off'}"
  onclick="toggleStatus('${user.id}')"
>
  <span class="thumb" style="transform: translateX(${user.estado ? '18px' : '0px'});"></span>
</button>

        </div>
      `;

    cardsContainer.appendChild(card);
  });

  attachMenuEvents();

  const tablaVisible = !vistaTabla.classList.contains("hidden");

  if (tablaVisible) {
    renderPaginationControls(
      paginationTabla,
      totalItems,
      PAGE_SIZE_TABLE,
      currentPageTable,
      (page) => {
        currentPageTable = page;
        renderTable();
      }
    );
  } else {
    renderPaginationControls(
      paginationTabla,
      totalItems,
      PAGE_SIZE_CARDS,
      currentPageCards,
      (page) => {
        currentPageCards = page;
        renderTable();
      }
    );
  }
}

// Manejo de dropdown menú
function attachMenuEvents() {
  document.addEventListener("click", (e) => {
    if (
      !e.target.closest("[data-menu-trigger]") &&
      !e.target.closest("[data-menu]")
    ) {
      document.querySelectorAll("[data-menu]").forEach((el) => {
        el.classList.add("hidden");
        el.classList.remove("show");
      });
    }
  });

  document.querySelectorAll("[data-menu-trigger]").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.stopPropagation();

      const wrapper = btn.closest(".relative, .inline-block, td, div");
      if (!wrapper) return;

      const menu = wrapper.querySelector("[data-menu]");
      if (!menu) return;

      const isHidden = menu.classList.contains("hidden");

      document.querySelectorAll("[data-menu]").forEach((el) => {
        el.classList.add("hidden");
        el.classList.remove("show");
      });

      if (isHidden) {
        menu.classList.remove("hidden");
        requestAnimationFrame(() => {
          menu.classList.add("show");
        });
      } else {
        menu.classList.remove("show");
        setTimeout(() => {
          menu.classList.add("hidden");
        }, 150);
      }
    });
  });

  document.querySelectorAll("[data-menu] [data-action]").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.stopPropagation();

      const action = btn.getAttribute("data-action");
      const id = btn.getAttribute("data-id");
      const user = users.find((u) => String(u.id) === String(id));
      if (!user) return;

      if (action === "ver") {
        openModalVerUsuario(user);
      } else if (action === "editar") {
        openModalUsuario(user);
      } else if (action === "toggle") {
        toggleStatus(id);
      }

      const menu = btn.closest("[data-menu]");
      if (menu) {
        menu.classList.add("hidden");
        menu.classList.remove("show");
      }
    });
  });
}

// ====== Eventos globales ======
inputBuscar.addEventListener("input", () => {
  currentPageTable = 1;
  currentPageCards = 1;
  renderTable();
});

selectFiltroRol.addEventListener("change", () => {
  currentPageTable = 1;
  currentPageCards = 1;
  renderTable();
});

btnNuevoUsuario.addEventListener("click", () => openModalUsuario(null));
btnCerrarModalUsuario.addEventListener("click", closeModalUsuario);
btnCancelarModalUsuario.addEventListener("click", closeModalUsuario);

btnCerrarModalVerUsuario.addEventListener("click", closeModalVerUsuario);

inputCargo.addEventListener("change", actualizarVisibilidadPrograma);

btnVistaTabla.addEventListener("click", setVistaTabla);
btnVistaTarjetas.addEventListener("click", setVistaTarjetas);

// ================================
// VALIDACIONES Y ENVÍO FORMULARIO
// ================================
formUsuario.addEventListener("submit", async (e) => {
  e.preventDefault();

  const payload = {
    nombre_completo: inputNombreCompleto.value.trim(),
    tipo_documento: inputTipoDocumento.value,
    numero_documento: inputNumeroDocumento.value.trim(),
    telefono: inputTelefono.value.trim(),
    cargo: inputCargo.value,
    correo: inputCorreo.value.trim(),
    password: inputPassword.value.trim(),
    direccion: inputDireccion.value.trim(),
    id_programa: inputPrograma ? inputPrograma.value : null,
  };

  // 👇 normalizar id_programa
  if (payload.cargo !== "Instructor" || !payload.id_programa) {
    payload.id_programa = null;
  }

  const isEdit = !!hiddenUserId.value;
  if (!validateUserPayload(payload, { isEdit, currentId: hiddenUserId.value })) return;

  // Validación de cambios en edición
  if (isEdit && originalEditData) {
    const currentData = {
      nombre_completo: payload.nombre_completo,
      tipo_documento: payload.tipo_documento,
      numero_documento: payload.numero_documento,
      telefono: payload.telefono,
      cargo: payload.cargo,
      correo: payload.correo,
      direccion: payload.direccion,
      id_programa:
        payload.cargo === "Instructor" && payload.id_programa
          ? String(payload.id_programa)
          : null,
    };

    const noHayCambios =
      JSON.stringify(currentData) === JSON.stringify(originalEditData) &&
      !payload.password;

    if (noHayCambios) {
      toastInfo("Para actualizar debes modificar al menos un dato.");
      return;
    }
  }

  if (isEdit) {
    payload.id_usuario = hiddenUserId.value;
  }

  try {
    const data = isEdit
      ? await actualizarUsuario(payload)
      : await crearUsuario(payload);


    console.log("Respuesta procesada:", data);

    if (data.error) {
      toastError(data.error || "Ocurrió un error al procesar la solicitud.");
      return;
    }

    toastSuccess(
      data.mensaje ||
        (isEdit ? "Usuario actualizado correctamente." : "Usuario creado correctamente.")
    );

    closeModalUsuario();
    await cargarUsuarios();
  } catch (error) {
    console.error("Error de red al guardar usuario:", error);
    toastError("Ocurrió un error al guardar el usuario (red/servidor).");
  }
});

// Render inicial
cargarUsuarios();
cargarProgramas();
setVistaTabla();
