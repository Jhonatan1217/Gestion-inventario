/* evidencias.js — Gestión de Evidencias de Formación */

/* =========================
   Variables globales
   ========================= */
let evidencesData = []

// Datos de ejemplo (simula backend)
const mockEvidences = [
  {
    id: 1,
    fecha: "2024-04-06",
    ficha: "2896441",
    imagen: "/images/image.png",
    titulo: "Técnico en Instalaciones Eléctricas Residenciales",
    descripcion: "Trabajo de cimentación realizado por los aprendices de la ficha 2567890",
    materiales: ["Cemento Gris", "Arena de Río"],
  },
  {
    id: 2,
    fecha: "2024-11-24",
    ficha: "2896441",
    imagen: "/images/image.png",
    titulo: "Instalación Eléctrica Residencial",
    descripcion: "Instalación eléctrica residencial completada exitosamente",
    materiales: ["Cable Eléctrico #12"],
  },
  {
    id: 3,
    fecha: "2024-11-24",
    ficha: "2896441",
    imagen: "/images/image.png",
    titulo: "Formación de Procesos Constructivos",
    descripcion: "Instalación eléctrica residencial completada exitosamente",
    materiales: ["Cable Eléctrico #12"],
  },
]

/* =========================
   Inicialización
   ========================= */
document.addEventListener("DOMContentLoaded", () => {
  evidencesData = [...mockEvidences]
  renderEvidenceCards()
})

/* =========================
   Iconos SVG
   ========================= */
const icons = {
  calendar:
    '<svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>',
  tag: '<svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>',
  photo:
    '<svg class="w-6 h-6 text-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>',
}

/* =========================
   Render de tarjetas
   ========================= */
function renderEvidenceCards() {
  const grid = document.getElementById("evidenceGrid")
  grid.innerHTML = ""

  evidencesData.forEach((evidence) => {
    const card = document.createElement("div")
    card.className =
      "rounded-2xl border border-border bg-card shadow-sm overflow-hidden cursor-pointer hover:shadow-lg hover:border-primary transition-all duration-300"
    card.onclick = () => openDetailsModal(evidence.id)

    card.innerHTML = `
      <div class="relative">
        <img src="${evidence.imagen}" alt="Evidencia" class="w-full h-48 object-cover bg-muted">
        <div class="absolute top-3 right-3 bg-card/90 backdrop-blur-sm px-3 py-1.5 rounded-lg flex items-center gap-2 shadow-sm">
          ${icons.calendar}
          <span class="text-xs font-medium">Ficha ${evidence.ficha}</span>
        </div>
      </div>
      <div class="p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs text-muted-foreground">${evidence.fecha}</span>
        </div>
        <p class="text-sm text-foreground line-clamp-2 mb-3">${evidence.descripcion}</p>
        <div class="flex flex-wrap gap-2">
          ${evidence.materiales
            .map(
              (material) => `
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-secondary-13 text-secondary">
              ${icons.tag}
              ${material}
            </span>
          `,
            )
            .join("")}
        </div>
      </div>
    `

    grid.appendChild(card)
  })
}

/* =========================
   Modal de Detalles
   ========================= */
function openDetailsModal(id) {
  const evidence = evidencesData.find((e) => e.id === id)
  if (!evidence) return

  // Actualizar contenido del modal
  document.getElementById("detailImage").src = evidence.imagen
  document.getElementById("detailTitle").textContent = evidence.titulo
  document.getElementById("detailDate").textContent = evidence.fecha
  document.getElementById("detailDescription").textContent = evidence.descripcion

  const materialsContainer = document.getElementById("detailMaterials")
  materialsContainer.innerHTML = evidence.materiales
    .map(
      (material) => `
    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-badge-secondary text-badge-secondary">
      ${icons.tag}
      ${material}
    </span>
  `,
    )
    .join("")

  // Mostrar modal
  const modal = document.getElementById("detailsModal")
  modal.classList.add("active")
  document.body.style.overflow = "hidden"
}

function closeDetailsModal() {
  const modal = document.getElementById("detailsModal")
  modal.classList.remove("active")
  document.body.style.overflow = "auto"
}

/* =========================
   Modal de Registro
   ========================= */
function openCreateModal() {
  const modal = document.getElementById("createModal")
  modal.classList.add("active")
  document.body.style.overflow = "hidden"
}

function closeCreateModal() {
  const modal = document.getElementById("createModal")
  modal.classList.remove("active")
  document.body.style.overflow = "auto"

  // Limpiar formulario
  document.getElementById("fecha").value = ""
  document.getElementById("descripcion").value = ""
  document.getElementById("materiales").value = ""
  document.getElementById("photoInput").value = ""
}

/* =========================
   Crear evidencia
   ========================= */
function createEvidence() {
  const fecha = document.getElementById("fecha").value
  const descripcion = document.getElementById("descripcion").value
  const materiales = document.getElementById("materiales").value

  if (!fecha || !descripcion || !materiales) {
    alert("Por favor complete todos los campos obligatorios")
    return
  }

  // Simulación de creación
  const newEvidence = {
    id: evidencesData.length + 1,
    fecha: fecha,
    ficha: "2896441",
    imagen: "/images/image.png",
    titulo: "Nueva Evidencia",
    descripcion: descripcion,
    materiales: [materiales],
  }

  evidencesData.push(newEvidence)
  renderEvidenceCards()
  closeCreateModal()

  showAlert("Evidencia creada exitosamente", "success")
}

/* =========================
   Helper: Alertas
   ========================= */
function showAlert(message, type = "success") {
  // Simulación simple de alerta
  alert(message)
}

/* =========================
   Event Listeners
   ========================= */
// Cerrar modales al hacer clic en el overlay
document.getElementById("detailsModal").addEventListener("click", function (e) {
  if (e.target === this) {
    closeDetailsModal()
  }
})

document.getElementById("createModal").addEventListener("click", function (e) {
  if (e.target === this) {
    closeCreateModal()
  }
})

// Cerrar con tecla Escape
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    closeDetailsModal()
    closeCreateModal()
  }
})
