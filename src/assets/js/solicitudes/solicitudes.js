// ============================================================
//  MÓDULO SOLICITUDES – JS FINAL FUNCIONAL
// ============================================================

const API = "src/controllers/solicitudes_controller.php";

// ============================================================
//  MAPEO DE ESTADOS
// ============================================================
const ESTADO_MAP = {
  Pendiente: "pendiente",
  Aprobada: "aprobada",
  Rechazada: "rechazada",
  Entregada: "entregada",
};

const ESTADO_LABEL = {
  pendiente: "Pendiente",
  aprobada: "Aprobada",
  rechazada: "Rechazada",
  entregada: "Entregada",
};

const ESTADO_ICON = {
  pendiente: "clock",
  aprobada: "check-circle",
  entregada: "package-check",
  rechazada: "x-circle",
};

// ============================================================
//  SELECTORES
// ============================================================
const btnNueva = document.getElementById("sol-btn-nueva");
const modal = document.getElementById("sol-modal");
const btnCerrar = document.getElementById("sol-modal-cerrar");
const btnCancelar = document.getElementById("sol-btn-cancelar");

const paso1 = document.getElementById("sol-paso-1");
const paso2 = document.getElementById("sol-paso-2");

const btnPaso2 = document.getElementById("sol-btn-ir-paso-2");
const btnVolver = document.getElementById("sol-btn-volver");
const btnGuardar = document.getElementById("sol-btn-guardar");

const contenedor = document.getElementById("sol-cards");
const paginacion = document.getElementById("sol-pagination");
const filtros = document.querySelectorAll(".sol-filtro-btn");

// ============================================================
//  ESTADO GLOBAL
// ============================================================
let solicitudes = [];
let filtroActivo = "todas";
let paginaActual = 1;
const PAGE_SIZE = 9;

// ============================================================
//  HELPERS
// ============================================================
function normalizarEstado(estadoBD) {
  return ESTADO_MAP[estadoBD] || "pendiente";
}

// ============================================================
//  CARGAR SOLICITUDES (BD REAL)
// ============================================================
async function cargarSolicitudes() {
  try {
    const res = await fetch(`${API}?accion=listar`);
    const data = await res.json();

    if (!Array.isArray(data)) {
      console.error("Respuesta inválida:", data);
      return;
    }

    solicitudes = data.map((s) => ({
      id: s.id_solicitud,
      fecha: s.fecha_solicitud?.split(" ")[0] ?? "",
      ficha: s.id_ficha,
      estado: normalizarEstado(s.estado),
    }));

    paginaActual = 1;
    renderSolicitudes();
    actualizarResumen();
    actualizarFiltros();
  } catch (e) {
    console.error("Error cargando solicitudes:", e);
  }
}

// ============================================================
//  RESUMEN Y FILTROS
// ============================================================
function contar() {
  const c = { pendiente: 0, aprobada: 0, entregada: 0, rechazada: 0 };
  solicitudes.forEach((s) => c[s.estado]++);
  return c;
}

function actualizarResumen() {
  const c = contar();
  const nums = document.querySelectorAll(".sol-resumen-numero");
  if (nums.length !== 4) return;

  nums[0].textContent = c.pendiente;
  nums[1].textContent = c.aprobada;
  nums[2].textContent = c.entregada;
  nums[3].textContent = c.rechazada;
}

function actualizarFiltros() {
  const c = contar();
  filtros.forEach((btn) => {
    const f = btn.dataset.filtro;
    if (f === "todas") {
      btn.textContent = `Todas (${solicitudes.length})`;
    } else {
      btn.textContent = `${ESTADO_LABEL[f]}s (${c[f]})`;
    }
  });
}

// ============================================================
//  RENDER
// ============================================================
function renderSolicitudes() {
  let lista =
    filtroActivo === "todas"
      ? solicitudes
      : solicitudes.filter((s) => s.estado === filtroActivo);

  if (lista.length === 0) {
    contenedor.innerHTML = `
      <div class="sol-empty col-span-full">
        <div class="sol-empty-icon">
          <i data-lucide="file-text"></i>
        </div>
        <h3>No hay solicitudes</h3>
        <p>No existen solicitudes para este filtro</p>
      </div>
    `;
    lucide.createIcons();
    return;
  }

  const inicio = (paginaActual - 1) * PAGE_SIZE;
  const fin = inicio + PAGE_SIZE;
  const pagina = lista.slice(inicio, fin);

  contenedor.innerHTML = "";

  pagina.forEach((s) => {
    const card = document.createElement("div");
    card.className = "sol-card";

    card.innerHTML = `
      <div class="sol-card-header">
        <div class="sol-card-title-wrap">
          <div class="sol-card-icon ${s.estado}">
            <i data-lucide="${ESTADO_ICON[s.estado]}"></i>
          </div>
          <div>
            <div class="sol-card-title">Solicitud #${s.id}</div>
            <div class="sol-card-date">${s.fecha}</div>
          </div>
        </div>

        <span class="sol-badge ${s.estado}">
          ${ESTADO_LABEL[s.estado]}
        </span>
      </div>

      <div class="sol-card-row">
        <i data-lucide="layers" class="sol-icon-muted"></i>
        <span>Ficha ${s.ficha}</span>
      </div>
    `;

    contenedor.appendChild(card);
  });

  lucide.createIcons();
}

// ============================================================
//  FILTROS CLICK
// ============================================================
filtros.forEach((btn) => {
  btn.addEventListener("click", () => {
    filtros.forEach((b) => b.classList.remove("sol-filtro-btn-activo"));
    btn.classList.add("sol-filtro-btn-activo");

    filtroActivo = btn.dataset.filtro;
    paginaActual = 1;
    renderSolicitudes();
  });
});

// ============================================================
//  MODAL
// ============================================================
btnNueva.onclick = () => {
  modal.classList.add("sol-modal-show");
  paso1.classList.remove("hidden");
  paso2.classList.add("hidden");
};

btnCerrar.onclick = () => modal.classList.remove("sol-modal-show");
btnCancelar.onclick = () => modal.classList.remove("sol-modal-show");

btnPaso2.onclick = () => {
  paso1.classList.add("hidden");
  paso2.classList.remove("hidden");
};

btnVolver.onclick = () => {
  paso2.classList.add("hidden");
  paso1.classList.remove("hidden");
};

// ============================================================
//  INIT
// ============================================================
cargarSolicitudes();
