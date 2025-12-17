// ============================================================
//  MÓDULO SOLICITUDES
// ============================================================


// ============================================================
//  SELECTORES PRINCIPALES
// ============================================================
const btnNueva = document.getElementById("sol-btn-nueva");
const modal = document.getElementById("sol-modal");
const btnCerrarModal = document.getElementById("sol-modal-cerrar");

const paso1 = document.getElementById("sol-paso-1");
const paso2 = document.getElementById("sol-paso-2");

const btnPaso2 = document.getElementById("sol-btn-ir-paso-2");
const btnVolver = document.getElementById("sol-btn-volver");
const btnGuardar = document.getElementById("sol-btn-guardar");

const contenedorCards = document.getElementById("sol-cards");
const paginationContainer = document.getElementById("sol-pagination"); // 🔹 PAGINACIÓN
const filtros = document.querySelectorAll(".sol-filtro-btn");


// ============================================================
//  DATA MOCK (SIMULACIÓN BACKEND)
// ============================================================
let solicitudes = [
  {
    id: 4,
    fecha: "2024-11-27",
    instructor: "Juan Pablo Hernández Castro",
    ficha: "2896365",
    estado: "pendiente",
    materiales: [
      { nombre: "Cemento Gris", cantidad: 15 },
      { nombre: "Arena de Río", cantidad: 3 },
    ],
    observaciones: "Práctica de cimentación semana 48",
  },
  {
    id: 1,
    fecha: "2023-11-27",
    instructor: "Juan Pepe Castro Patiño",
    ficha: "2896365",
    estado: "aprobada",
    materiales: [
      { nombre: "Cemento Rojo", cantidad: 10 },
      { nombre: "Arena de Río", cantidad: 8 },
    ],
    observaciones: "Práctica de cimentación semana 24",
  },
  {
    id: 2,
    fecha: "2023-11-27",
    instructor: "Juan Pablo Hernández Castro",
    ficha: "2896463",
    estado: "aprobada",
    materiales: [
      { nombre: "Cemento Gris", cantidad: 15 },
      { nombre: "Arena de Río", cantidad: 3 },
    ],
    observaciones: "Práctica de cimentación semana 48",
  },
  {
    id: 3,
    fecha: "2021-01-15",
    instructor: "Pepito Perez Ozuna",
    ficha: "6969696",
    estado: "rechazada",
    materiales: [
      { nombre: "Cemento Gris", cantidad: 15 },
      { nombre: "Ladrillos pios", cantidad: 3 },
    ],
    observaciones: "Para mi casita XD",
  },
  {
    id: 5,
    fecha: "2021-01-15",
    instructor: "Pepito Perez Ozuna",
    ficha: "6969696",
    estado: "rechazada",
    materiales: [
      { nombre: "Cemento Gris", cantidad: 15 },
      { nombre: "Ladrillos pios", cantidad: 3 },
    ],
    observaciones: "Para mi casita XD",
  },
  {
    id: 6,
    fecha: "2021-01-15",
    instructor: "Pepito Perez Ozuna",
    ficha: "6969696",
    estado: "rechazada",
    materiales: [
      { nombre: "Cemento Gris", cantidad: 15 },
      { nombre: "Ladrillos pios", cantidad: 3 },
    ],
    observaciones: "Para mi casita XD",
  },
    {
    id: 7,
    fecha: "2021-01-15",
    instructor: "Pepito Perez Ozuna",
    ficha: "6969696",
    estado: "rechazada",
    materiales: [
      { nombre: "Cemento Gris", cantidad: 15 },
      { nombre: "Ladrillos pios", cantidad: 3 },
    ],
    observaciones: "Para mi casita XD",
  },
    {
    id: 8,
    fecha: "2021-01-15",
    instructor: "Pepito Perez Ozuna",
    ficha: "6969696",
    estado: "rechazada",
    materiales: [
      { nombre: "Cemento Gris", cantidad: 15 },
      { nombre: "Ladrillos pios", cantidad: 3 },
    ],
    observaciones: "Para mi casita XD",
  },
    {
    id: 9,
    fecha: "2021-01-15",
    instructor: "Pepito Perez Ozuna",
    ficha: "6969696",
    estado: "rechazada",
    materiales: [
      { nombre: "Cemento Gris", cantidad: 15 },
      { nombre: "Ladrillos pios", cantidad: 3 },
    ],
    observaciones: "Para mi casita XD",
  },
    {
    id: 10  ,
    fecha: "2021-01-15",
    instructor: "Pepito Perez Ozuna",
    ficha: "6969696",
    estado: "rechazada",
    materiales: [
      { nombre: "Cemento Gris", cantidad: 15 },
      { nombre: "Ladrillos pios", cantidad: 3 },
    ],
    observaciones: "Para mi casita XD",
  },
];

let filtroActivo = "todas";


// =========================
// PAGINACIÓN
// =========================
let currentPage = 1;            // 🔹 PAGINACIÓN
const PAGE_SIZE = 9;            // 🔹 PAGINACIÓN (3x3)


// ============================================================
//  HTML: EMPTY STATE
// ============================================================
function renderEmptyState() {
  contenedorCards.innerHTML = `
    <div class="sol-empty">
      <div class="sol-empty-icon">
        <i data-lucide="file-text"></i>
      </div>
      <p class="sol-empty-title">No hay solicitudes registradas</p>
      <p class="sol-empty-subtitle">
        Cree una solicitud desde el botón <strong>"Nueva Solicitud"</strong>
      </p>
    </div>
  `;
  if (paginationContainer) paginationContainer.innerHTML = ""; // 🔹 PAGINACIÓN
}


// ============================================================
//  RENDER: SOLICITUDES
// ============================================================
function renderSolicitudes() {
  if (!contenedorCards) return;

  // 1) Filtrar lista
  let lista = solicitudes;
  if (filtroActivo !== "todas") {
    lista = solicitudes.filter((s) => s.estado === filtroActivo);
  }

  // 2) Empty state
  if (lista.length === 0) {
    renderEmptyState();
    lucide.createIcons();
    return;
  }

  // 🔹 PAGINACIÓN
  const totalItems = lista.length;
  const start = (currentPage - 1) * PAGE_SIZE;
  const end = start + PAGE_SIZE;
  const listaPaginada = lista.slice(start, end);

  // 3) Render cards
  contenedorCards.innerHTML = "";

  listaPaginada.forEach((sol) => {
    const card = document.createElement("div");
    card.className = "sol-card";

    card.innerHTML = `
      <div class="sol-card-header">
        <div class="sol-card-title-wrap">
          <div class="sol-card-icon ${sol.estado}">
            <i data-lucide="${iconoEstado(sol.estado)}"></i>
          </div>
          <div>
            <div class="sol-card-title">Solicitud #${sol.id}</div>
            <div class="sol-card-date">${sol.fecha}</div>
          </div>
        </div>

        <span class="sol-badge ${sol.estado}">
          ${capitalizar(sol.estado)}
        </span>
      </div>

      <div class="sol-card-row">
        <i data-lucide="user" class="sol-icon-muted"></i>
        <span><strong>Instructor:</strong> ${sol.instructor}</span>
        <span class="sol-chip">Ficha ${sol.ficha}</span>
      </div>

      <div class="sol-card-section">
        <div class="sol-section-title">Materiales solicitados:</div>
        <div class="sol-materials">
          ${sol.materiales.map(m => `
            <span class="sol-material">
              <i data-lucide="cube"></i>
              ${m.nombre} (${m.cantidad})
            </span>
          `).join("")}
        </div>
      </div>

      <div class="sol-card-section">
        <div class="sol-section-title muted">Observaciones:</div>
        <div class="sol-observacion">${sol.observaciones}</div>
      </div>

      ${
        sol.estado === "pendiente"
          ? `
        <div class="sol-card-actions">
          <button class="sol-btn-approve" onclick="aprobarSolicitud(${sol.id})">
            <i data-lucide="check-circle"></i> Aprobar
          </button>
          <button class="sol-btn-reject" onclick="rechazarSolicitud(${sol.id})">
            <i data-lucide="x-circle"></i> Rechazar
          </button>
        </div>
      `
          : ""
      }
    `;

    contenedorCards.appendChild(card);
  });

  // 🔹 PAGINACIÓN
  renderPaginationControls(
    paginationContainer,
    totalItems,
    PAGE_SIZE,
    currentPage,
    (page) => {
      currentPage = page;
      renderSolicitudes();
    }
  );

  lucide.createIcons();
}


// ============================================================
//  ACCIONES
// ============================================================
function aprobarSolicitud(id) {
  const sol = solicitudes.find((s) => s.id === id);
  if (!sol) return;

  sol.estado = "aprobada";
  currentPage = 1; // 🔹 PAGINACIÓN
  actualizarContadores();
  renderSolicitudes();
}

function rechazarSolicitud(id) {
  const sol = solicitudes.find((s) => s.id === id);
  if (!sol) return;

  sol.estado = "rechazada";
  currentPage = 1; // 🔹 PAGINACIÓN
  actualizarContadores();
  renderSolicitudes();
}

window.aprobarSolicitud = aprobarSolicitud;
window.rechazarSolicitud = rechazarSolicitud;


// ============================================================
//  FILTROS
// ============================================================
filtros.forEach((btn) => {
  btn.addEventListener("click", () => {
    filtros.forEach((b) => b.classList.remove("sol-filtro-btn-activo"));
    btn.classList.add("sol-filtro-btn-activo");

    filtroActivo = btn.dataset.filtro;
    currentPage = 1; // 🔹 PAGINACIÓN
    renderSolicitudes();
  });
});


// ============================================================
//  UTILIDADES
// ============================================================
function capitalizar(texto) {
  return texto.charAt(0).toUpperCase() + texto.slice(1);
}

function iconoEstado(estado) {
  if (estado === "pendiente") return "clock";
  if (estado === "aprobada") return "check-circle";
  return "x-circle";
}


// =========================
// GENERIC PAGINATION RENDER
// =========================
function renderPaginationControls(container, totalItems, pageSize, currentPage, onPageChange) {
  if (!container) return;

  const totalPages = Math.ceil(totalItems / pageSize);

  if (totalPages <= 1) {
    container.innerHTML = "";
    return;
  }

  container.innerHTML = "";
  container.className = "flex items-center justify-end gap-2 mt-6";

  const baseBtn =
    "px-3 py-1.5 text-sm rounded-md border transition-colors";

  const btnNormal =
    "bg-white border-border hover:bg-muted";

  const btnActive =
    "bg-primary text-white border-primary";

  const btnDisabled =
    "opacity-40 cursor-not-allowed";

  // ===== Anterior =====
  const btnPrev = document.createElement("button");
  btnPrev.type = "button";
  btnPrev.textContent = "Anterior";
  btnPrev.className = `${baseBtn} ${btnNormal}`;

  if (currentPage === 1) {
    btnPrev.disabled = true;
    btnPrev.classList.add(...btnDisabled.split(" "));
  } else {
    btnPrev.onclick = () => onPageChange(currentPage - 1);
  }

  container.appendChild(btnPrev);

  // ===== Números =====
  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.type = "button";
    btn.textContent = i;
    btn.className = `${baseBtn} ${
      i === currentPage ? btnActive : btnNormal
    }`;
    btn.onclick = () => onPageChange(i);
    container.appendChild(btn);
  }

  // ===== Siguiente =====
  const btnNext = document.createElement("button");
  btnNext.type = "button";
  btnNext.textContent = "Siguiente";
  btnNext.className = `${baseBtn} ${btnNormal}`;

  if (currentPage === totalPages) {
    btnNext.disabled = true;
    btnNext.classList.add(...btnDisabled.split(" "));
  } else {
    btnNext.onclick = () => onPageChange(currentPage + 1);
  }

  container.appendChild(btnNext);
}


// ============================================================
//  INIT
// ============================================================
//actualizarContadores();
renderSolicitudes();
