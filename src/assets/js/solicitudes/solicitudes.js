// ============================================================
//  MÓDULO SOLICITUDES – FRONTEND FINAL (ESTABLE)
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
const estadoVacio = document.getElementById("sol-empty");

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
            { nombre: "Arena de Río", cantidad: 3 }
        ],
        observaciones: "Práctica de cimentación semana 48"
    },
    {
        id: 1,
        fecha: "2023-11-27",
        instructor: "Juan Pepe Castro Patiño",
        ficha: "2896365",
        estado: "aprobada",
        materiales: [
            { nombre: "Cemento Rojo", cantidad: 10 },
            { nombre: "Arena de Río", cantidad: 8 }
        ],
        observaciones: "Práctica de cimentación semana 24"
    },
    {
        id: 2,
        fecha: "2023-11-27",
        instructor: "Juan Pablo Hernández Castro",
        ficha: "2896463",
        estado: "aprobada",
        materiales: [
            { nombre: "Cemento Gris", cantidad: 15 },
            { nombre: "Arena de Río", cantidad: 3 }
        ],
        observaciones: "Práctica de cimentación semana 48"
    },
    {
        id: 3,
        fecha: "2021-01-15",
        instructor: "Pepito Perez Ozuna",
        ficha: "6969696",
        estado: "rechazada",
        materiales: [
            { nombre: "Cemento Gris", cantidad: 15 },
            { nombre: "Ladrillos pios", cantidad: 3 }
        ],
        observaciones: "Para mi casita XD"
    }
];

let filtroActivo = "todas";


// ============================================================
//  RENDER DE SOLICITUDES (FIX DEFINITIVO)
// ============================================================
function renderSolicitudes() {

    // Reset visual limpio
    contenedorCards.innerHTML = "";
    estadoVacio.classList.add("hidden");

    // Aplicar filtro
    let lista = solicitudes;
    if (filtroActivo !== "todas") {
        lista = solicitudes.filter(s => s.estado === filtroActivo);
    }

    // ESTADO VACÍO (CORRECTO)
    if (lista.length === 0) {
        estadoVacio.classList.remove("hidden");
        lucide.createIcons();
        return;
    }

    // Render de cards
    lista.forEach(sol => {
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

            ${sol.estado === "pendiente" ? `
                <div class="sol-card-actions">
                    <button class="sol-btn-approve" onclick="aprobarSolicitud(${sol.id})">
                        <i data-lucide="check-circle"></i> Aprobar
                    </button>
                    <button class="sol-btn-reject" onclick="rechazarSolicitud(${sol.id})">
                        <i data-lucide="x-circle"></i> Rechazar
                    </button>
                </div>
            ` : ""}
        `;

        contenedorCards.appendChild(card);
    });

    lucide.createIcons();
}


// ============================================================
//  ACCIONES (APROBAR / RECHAZAR)
// ============================================================
function aprobarSolicitud(id) {
    const sol = solicitudes.find(s => s.id === id);
    if (!sol) return;

    sol.estado = "aprobada";
    actualizarContadores();
    renderSolicitudes();
}

function rechazarSolicitud(id) {
    const sol = solicitudes.find(s => s.id === id);
    if (!sol) return;

    sol.estado = "rechazada";
    actualizarContadores();
    renderSolicitudes();
}


// ============================================================
//  CONTADORES (RESUMEN + FILTROS)
// ============================================================
function actualizarContadores() {
    document.querySelector('[data-filtro="todas"]').textContent =
        `Todas (${solicitudes.length})`;

    document.querySelector('[data-filtro="pendiente"]').textContent =
        `Pendientes (${solicitudes.filter(s => s.estado === "pendiente").length})`;

    document.querySelector('[data-filtro="aprobada"]').textContent =
        `Aprobadas (${solicitudes.filter(s => s.estado === "aprobada").length})`;

    document.querySelector('[data-filtro="rechazada"]').textContent =
        `Rechazadas (${solicitudes.filter(s => s.estado === "rechazada").length})`;
}


// ============================================================
//  FILTROS
// ============================================================
filtros.forEach(btn => {
    btn.addEventListener("click", () => {
        filtros.forEach(b => b.classList.remove("sol-filtro-btn-activo"));
        btn.classList.add("sol-filtro-btn-activo");

        filtroActivo = btn.dataset.filtro;
        renderSolicitudes();
    });
});


// ============================================================
//  MODAL (2 PASOS)
// ============================================================
btnNueva.onclick = () => {
    modal.classList.add("sol-modal-show");
    paso1.classList.remove("hidden");
    paso2.classList.add("hidden");
};

btnCerrarModal.onclick = () => {
    modal.classList.remove("sol-modal-show");
};

btnPaso2.onclick = () => {
    paso1.classList.add("hidden");
    paso2.classList.remove("hidden");
};

btnVolver.onclick = () => {
    paso2.classList.add("hidden");
    paso1.classList.remove("hidden");
};

btnGuardar.onclick = () => {
    alert("Solicitud creada (simulación)");
    modal.classList.remove("sol-modal-show");
};


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


// ============================================================
//  INIT
// ============================================================
actualizarContadores();
renderSolicitudes();
