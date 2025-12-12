// =========================
// CONFIG: CONTROLLER ENDPOINTS
// =========================
const API_URL = "src/controllers/ficha_controller.php"
const PROGRAMAS_API_URL = "src/controllers/programa_controller.php"
const INSTRUCTORES_API_URL = "src/controllers/instructor_controller.php"

// =========================
// NIVEL CONFIGURATION (label and badge styles)
// =========================
const nivelLabels = {
  Tecnólogo: "Tecnólogo",
  Técnico: "Técnico",
}

// Badge classes defined in fichas.css
const nivelBadgeStyles = {
  Tecnólogo: "badge-nivel-tecnologo",
  Técnico: "badge-nivel-tecnico",
  Auxiliar: "badge-nivel-auxiliar",
  Operario: "badge-nivel-operario",
}

// =========================
// VALID LISTS ACCORDING TO DATABASE
// =========================
const VALID_NIVELES = ["Tecnólogo", "Técnico"]

// In-memory list used to render table and cards
let fichas = []
let originalEditData = null
let selectedFicha = null
let programas = []
let programasMap = {}
let instructores = []
let instructoresMap = {}

// =========================
// PAGINATION
// =========================
const PAGE_SIZE_TABLE = 10
const PAGE_SIZE_CARDS = 9

let currentPageTable = 1
let currentPageCards = 1

// =========================
// FLOWBITE-STYLE ALERTS
// =========================

function getOrCreateFlowbiteContainer() {
  let container = document.getElementById("flowbite-alert-container")

  if (!container) {
    container = document.createElement("div")
    container.id = "flowbite-alert-container"
    container.className =
      "fixed top-6 left-1/2 -translate-x-1/2 z-[9999] flex flex-col gap-3 w-full max-w-md px-4 pointer-events-none"
    document.body.appendChild(container)
  }

  return container
}

function showFlowbiteAlert(type, message) {
  const container = getOrCreateFlowbiteContainer()
  const wrapper = document.createElement("div")

  let borderColor = "border-amber-500"
  let textColor = "text-amber-900"
  let titleText = "Advertencia"

  let iconSVG = `
    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
         fill="currentColor" viewBox="0 0 20 20">
      <path d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l6.518 11.59A1.75 1.75 0 0 1 16.768 17H3.232a1.75 1.75 0 0 1-1.492-2.311L8.257 3.1z"/>
      <path d="M11 13H9V9h2zm0 3H9v-2h2z" fill="#fff"/>
    </svg>
  `

  if (type === "success") {
    borderColor = "border-emerald-500"
    textColor = "text-emerald-900"
    titleText = "Éxito"
    iconSVG = `
      <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
           fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm-1 15-4-4 1.414-1.414L9 12.172l4.586-4.586L15 9z"/>
      </svg>
    `
  }

  if (type === "info") {
    borderColor = "border-blue-500"
    textColor = "text-blue-900"
    titleText = "Información"
    iconSVG = `
      <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
           fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm1 15H9v-5h2Zm0-7H9V6h2Z"/>
      </svg>
    `
  }

  wrapper.className = `
    relative flex items-center w-full mx-auto pointer-events-auto
    rounded-2xl border-l-4 ${borderColor} bg-white shadow-md
    px-4 py-3 text-sm ${textColor}
    opacity-0 -translate-y-2
    transition-all duration-300 ease-out
    animate-fade-in-up
  `

  wrapper.innerHTML = `
    <div class="flex-shrink-0 mr-3 text-current">
      ${iconSVG}
    </div>
    <div class="flex-1 min-w-0">
      <p class="font-semibold">${titleText}</p>
      <p class="mt-0.5 text-sm">${message}</p>
    </div>
  `

  container.appendChild(wrapper)

  requestAnimationFrame(() => {
    wrapper.classList.remove("opacity-0", "-translate-y-2")
    wrapper.classList.add("opacity-100", "translate-y-0")
  })

  setTimeout(() => {
    wrapper.classList.add("opacity-0", "-translate-y-2")
    wrapper.classList.remove("opacity-100", "translate-y-0")
    setTimeout(() => wrapper.remove(), 250)
  }, 4000)
}

function toastError(message) {
  showFlowbiteAlert("warning", message)
}

function toastSuccess(message) {
  showFlowbiteAlert("success", message)
}

function toastInfo(message) {
  showFlowbiteAlert("info", message)
}

// =========================
// DOM REFERENCES
// =========================
const tbodyFichas = document.getElementById("tbodyFichas")
const inputBuscar = document.getElementById("inputBuscar")
const selectFiltroEstado = document.getElementById("selectFiltroEstado")

const vistaTabla = document.getElementById("vistaTabla")
const vistaTarjetas = document.getElementById("vistaTarjetas")
const cardsContainer = document.getElementById("cardsContainer")
const btnVistaTabla = document.getElementById("btnVistaTabla")
const btnVistaTarjetas = document.getElementById("btnVistaTarjetas")

const modalFicha = document.getElementById("modalFicha")
const btnNuevaFicha = document.getElementById("btnNuevaFicha")
const btnCerrarModalFicha = document.getElementById("btnCerrarModalFicha")
const btnCancelarModalFicha = document.getElementById("btnCancelarModalFicha")

const formFicha = document.getElementById("formFicha")
const hiddenFichaId = document.getElementById("hiddenFichaId")
const modalFichaTitulo = document.getElementById("modalFichaTitulo")
const modalFichaDescripcion = document.getElementById("modalFichaDescripcion")

const inputNumeroFicha = document.getElementById("numero_ficha")
const inputPrograma = document.getElementById("id_programa")
const inputNivel = document.getElementById("nivel")
const inputInstructor = document.getElementById("id_instructor")

const modalVerFicha = document.getElementById("modalVerFicha")
const btnCerrarModalVerFicha = document.getElementById("btnCerrarModalVerFicha")
const detalleFichaContent = document.getElementById("detalleFichaContent")

// =========================
// SINGLE PAGINATION CONTAINER
// =========================
let paginationTabla = document.getElementById("paginationTabla")

function ensurePaginationContainer() {
  if (vistaTarjetas && !paginationTabla) {
    paginationTabla = document.createElement("div")
    paginationTabla.id = "paginationTabla"
    paginationTabla.className = "mt-4 flex justify-end gap-2"
    vistaTarjetas.parentNode.insertBefore(paginationTabla, vistaTarjetas.nextSibling)
  }
}

ensurePaginationContainer()

// =========================
// EMPTY STATE CONTAINERS
// =========================
let emptyStateContainer = document.getElementById("emptyStateFichas")
let emptySearchContainer = document.getElementById("emptySearchFichas")

if (!emptyStateContainer && vistaTabla && vistaTabla.parentNode) {
  emptyStateContainer = document.createElement("div")
  emptyStateContainer.id = "emptyStateFichas"
  emptyStateContainer.className =
    "hidden mt-10 mb-6 flex flex-col items-center justify-center text-center border border-border rounded-2xl p-10 w-full"
  emptyStateContainer.innerHTML = `
    <div class="flex h-14 w-14 items-center justify-center rounded-full border border-border bg-transparent">
      <svg class="h-7 w-7 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" fill="none"
           viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
    </div>
    <h3 class="text-lg font-semibold mt-4">No hay fichas registradas</h3>
    <p class="text-sm text-muted-foreground mt-1 max-w-md">
      Una vez agregue fichas desde el botón <strong>"Nueva Ficha"</strong>, aparecerán listadas en esta vista.
    </p>
  `
  vistaTabla.parentNode.insertBefore(emptyStateContainer, vistaTabla)
}

if (!emptySearchContainer && vistaTabla && vistaTabla.parentNode) {
  emptySearchContainer = document.createElement("div")
  emptySearchContainer.id = "emptySearchFichas"
  emptySearchContainer.className =
    "hidden mt-10 mb-6 flex flex-col items-center justify-center text-center border border-border rounded-2xl p-10 w-full"
  emptySearchContainer.innerHTML = `
    <div class="flex h-14 w-14 items-center justify-center rounded-full border border-border bg-transparent">
      <svg class="h-7 w-7 text-muted-foreground"
           xmlns="http://www.w3.org/2000/svg"
           fill="none"
           viewBox="0 0 24 24"
           stroke="currentColor"
           stroke-width="1.8">
        <circle cx="11" cy="11" r="6" stroke-linecap="round" stroke-linejoin="round"></circle>
        <line x1="16" y1="16" x2="20" y2="20" stroke-linecap="round" stroke-linejoin="round"></line>
      </svg>
    </div>
    <h3 class="text-lg font-semibold mt-4">No se encontraron resultados</h3>
    <p class="text-sm text-muted-foreground mt-1 max-w-md">
      No se encontraron fichas que coincidan con los criterios de búsqueda actuales.
    </p>
  `
  vistaTabla.parentNode.insertBefore(emptySearchContainer, vistaTabla)
}

// =========================
// HELPER FUNCTIONS
// =========================

function renderOpcionesPrograma() {
  if (!inputPrograma) return

  inputPrograma.innerHTML = ""

  if (!Array.isArray(programas) || programas.length === 0) {
    inputPrograma.innerHTML = `<option value="">No hay programas disponibles</option>`
    inputPrograma.disabled = true
    return
  }

  inputPrograma.disabled = false
  inputPrograma.innerHTML = `<option value="">Seleccione</option>`

  programas.forEach((p) => {
    const opt = document.createElement("option")
    opt.value = p.id_programa
    opt.textContent = p.nombre_programa || p.nombre || ""
    inputPrograma.appendChild(opt)
  })
}

function renderOpcionesInstructor() {
  if (!inputInstructor) return

  inputInstructor.innerHTML = ""

  if (!Array.isArray(instructores) || instructores.length === 0) {
    inputInstructor.innerHTML = `<option value="">No hay instructores disponibles</option>`
    inputInstructor.disabled = true
    return
  }

  inputInstructor.disabled = false
  inputInstructor.innerHTML = `<option value="">Seleccione un instructor</option>`

  instructores.forEach((i) => {
    const opt = document.createElement("option")
    opt.value = i.id_instructor || i.id_usuario
    opt.textContent = i.nombre_completo || `${i.nombres || ""} ${i.apellidos || ""}`.trim()
    inputInstructor.appendChild(opt)
  })
}

async function cargarProgramas() {
  if (!inputPrograma) return

  try {
    const res = await fetch(`${PROGRAMAS_API_URL}?accion=listar`)
    const text = await res.text()

    let data
    try {
      const start = text.indexOf("[")
      const end = text.lastIndexOf("]")
      if (start !== -1 && end !== -1 && end > start) {
        data = JSON.parse(text.slice(start, end + 1))
      } else {
        data = []
      }
    } catch (e) {
      data = []
    }

    if (Array.isArray(data)) {
      programas = data.map((p) => ({
        id_programa: p.id_programa,
        nombre_programa: p.nombre_programa || p.nombre || "",
      }))

      programasMap = {}
      programas.forEach((p) => {
        programasMap[String(p.id_programa)] = p.nombre_programa
      })
    } else {
      programas = []
    }

    renderOpcionesPrograma()
  } catch (error) {
    console.error("Error al cargar programas:", error)
    programas = []
    renderOpcionesPrograma()
  }
}

async function cargarInstructores() {
  if (!inputInstructor) return

  try {
    const res = await fetch(`${INSTRUCTORES_API_URL}?accion=listar`)
    const text = await res.text()

    let data
    try {
      const start = text.indexOf("[")
      const end = text.lastIndexOf("]")
      if (start !== -1 && end !== -1 && end > start) {
        data = JSON.parse(text.slice(start, end + 1))
      } else {
        data = []
      }
    } catch (e) {
      data = []
    }

    if (Array.isArray(data)) {
      instructores = data.map((i) => ({
        id_instructor: i.id_instructor || i.id_usuario,
        nombre_completo: i.nombre_completo || `${i.nombres || ""} ${i.apellidos || ""}`.trim(),
      }))

      instructoresMap = {}
      instructores.forEach((i) => {
        instructoresMap[String(i.id_instructor)] = i.nombre_completo
      })
    } else {
      instructores = []
    }

    renderOpcionesInstructor()
  } catch (error) {
    console.error("Error al cargar instructores:", error)
    instructores = []
    renderOpcionesInstructor()
  }
}

function openModalFicha(editFicha = null) {
  selectedFicha = editFicha
  modalFicha.classList.add("active")

  if (editFicha) {
    modalFichaTitulo.textContent = "Editar Ficha"
    modalFichaDescripcion.textContent = "Modifica la información de la ficha"
    hiddenFichaId.value = editFicha.id

    inputNumeroFicha.value = editFicha.numero_ficha
    inputPrograma.value = editFicha.id_programa || ""
    inputNivel.value = editFicha.nivel || "Tecnólogo"
    inputInstructor.value = editFicha.id_instructor || ""

    originalEditData = {
      numero_ficha: String(editFicha.numero_ficha ?? "").trim(),
      id_programa: editFicha.id_programa ? String(editFicha.id_programa) : "",
      nivel: editFicha.nivel || "Tecnólogo",
      id_instructor: editFicha.id_instructor ? String(editFicha.id_instructor) : "",
    }
  } else {
    modalFichaTitulo.textContent = "Crear Nueva Ficha"
    modalFichaDescripcion.textContent = "Complete los datos para registrar una nueva ficha de formación"
    hiddenFichaId.value = ""
    formFicha.reset()
    inputNivel.value = "Tecnólogo"
    originalEditData = null
  }

  renderOpcionesPrograma()
  renderOpcionesInstructor()
}

function closeModalFicha() {
  modalFicha.classList.remove("active")
  selectedFicha = null
  hiddenFichaId.value = ""
  originalEditData = null
}

function openModalVerFicha(ficha) {
  selectedFicha = ficha
  modalVerFicha.classList.add("active")

  const estadoBadgeClass = ficha.estado ? "badge-estado-activo" : "badge-estado-inactivo"

  const programaNombre = ficha.id_programa
    ? programasMap[String(ficha.id_programa)] || "Sin programa asignado"
    : "Sin programa asignado"

  const instructorNombre = ficha.id_instructor
    ? instructoresMap[String(ficha.id_instructor)] || "Sin instructor asignado"
    : "Sin instructor asignado"

  detalleFichaContent.innerHTML = `
    <div class="flex items-center gap-4">
      <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
      </div>
      <div>
        <h3 class="font-semibold text-xl">${ficha.numero_ficha}</h3>
        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
          nivelBadgeStyles[ficha.nivel] || "badge-nivel-default"
        }">
          ${nivelLabels[ficha.nivel] || ficha.nivel || "N/A"}
        </span>
      </div>
    </div>
    <div class="space-y-3 text-sm mt-4">
      <div class="flex items-start gap-3">
        <span class="text-muted-foreground min-w-[80px]">Programa:</span>
        <span class="font-medium">${programaNombre}</span>
      </div>
      <div class="flex items-start gap-3">
        <span class="text-muted-foreground min-w-[80px]">Instructor:</span>
        <span class="font-medium">${instructorNombre}</span>
      </div>
      <div class="flex items-start gap-3">
        <span class="text-muted-foreground min-w-[80px]">Estado:</span>
        <span class="badge-estado-base ${estadoBadgeClass}">
          ${ficha.estado ? "Activa" : "Inactiva"}
        </span>
      </div>
    </div>
  `
}

function closeModalVerFicha() {
  modalVerFicha.classList.remove("active")
  selectedFicha = null
}

// =========================
// BACKEND COMMUNICATION LOGIC
// =========================

async function callApi(url, payload) {
  const res = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  })

  const text = await res.text()

  try {
    const start = text.indexOf("{")
    const end = text.lastIndexOf("}")
    if (start !== -1 && end !== -1 && end > start) {
      const jsonString = text.slice(start, end + 1)
      return JSON.parse(jsonString)
    }
    return { error: "Respuesta no válida del servidor: " + text }
  } catch (e) {
    return { error: "Respuesta no válida del servidor: " + text }
  }
}

async function cargarFichas() {
  try {
    const res = await fetch(`${API_URL}?accion=listar`)
    const text = await res.text()

    let data
    try {
      const start = text.indexOf("[")
      const end = text.lastIndexOf("]")
      if (start !== -1 && end !== -1 && end > start) {
        data = JSON.parse(text.slice(start, end + 1))
      } else {
        data = []
      }
    } catch (e) {
      data = []
    }

    if (!Array.isArray(data)) {
      fichas = []
    } else {
      fichas = data.map((f) => {
        let estadoBool = true

        if (typeof f.estado !== "undefined" && f.estado !== null) {
          const raw = String(f.estado).toLowerCase().trim()
          if (raw === "activa" || raw === "activo" || raw === "1" || raw === "true") {
            estadoBool = true
          } else if (raw === "inactiva" || raw === "inactivo" || raw === "0" || raw === "false") {
            estadoBool = false
          }
        }

        return {
          id: f.id_ficha,
          numero_ficha: f.numero_ficha,
          id_programa: f.id_programa ?? null,
          id_instructor: f.id_instructor ?? null,
          nivel: f.nivel || "Tecnólogo",
          estado: estadoBool,
        }
      })
    }

    renderTable()
  } catch (error) {
    console.error("Error al cargar fichas:", error)
    fichas = []
    renderTable()
  }
}

function crearFicha(payload) {
  return callApi(`${API_URL}?accion=crear`, payload)
}

function actualizarFicha(payload) {
  return callApi(`${API_URL}?accion=actualizar`, payload)
}

function cambiarEstadoFicha(payload) {
  return callApi(`${API_URL}?accion=cambiar_estado`, payload)
}

async function toggleStatus(fichaId) {
  const ficha = fichas.find((f) => String(f.id) === String(fichaId))
  if (!ficha) return

  const nuevoEstado = ficha.estado ? 0 : 1

  try {
    const data = await cambiarEstadoFicha({
      id_ficha: fichaId,
      estado: nuevoEstado,
    })

    if (data.error) {
      toastError(data.error || "No se pudo cambiar el estado de la ficha.")
      return
    }

    fichas = fichas.map((f) => (String(f.id) === String(fichaId) ? { ...f, estado: !!nuevoEstado } : f))
    renderTable()

    toastSuccess(nuevoEstado === 1 ? "Ficha activada correctamente." : "Ficha desactivada correctamente.")
  } catch (error) {
    console.error("Error al cambiar estado:", error)
    toastError("Ocurrió un error al cambiar el estado (red/servidor).")
  }
}

// =========================
// VIEW MODE SWITCH: TABLE / CARDS
// =========================

function setVistaTabla() {
  vistaTabla.classList.remove("hidden")
  vistaTarjetas.classList.add("hidden")

  btnVistaTabla.classList.add("bg-muted", "text-foreground")
  btnVistaTarjetas.classList.remove("bg-muted")
  btnVistaTarjetas.classList.add("text-muted-foreground")

  renderTable()
}

function setVistaTarjetas() {
  vistaTabla.classList.add("hidden")
  vistaTarjetas.classList.remove("hidden")

  btnVistaTarjetas.classList.add("bg-muted", "text-foreground")
  btnVistaTabla.classList.remove("bg-muted")
  btnVistaTabla.classList.add("text-muted-foreground")

  renderTable()
}

// =========================
// GENERIC PAGINATION RENDER
// =========================

function renderPaginationControls(container, totalItems, pageSize, currentPage, onPageChange) {
  if (!container) return

  const totalPages = Math.ceil(totalItems / pageSize)

  if (totalPages <= 1) {
    container.innerHTML = ""
    return
  }

  container.innerHTML = ""

  const btnPrev = document.createElement("button")
  btnPrev.type = "button"
  btnPrev.className = "px-3 py-1 text-sm rounded-lg border border-border bg-card hover:bg-muted disabled:opacity-40"
  btnPrev.textContent = "Anterior"
  btnPrev.disabled = currentPage === 1
  btnPrev.addEventListener("click", () => {
    if (currentPage > 1) onPageChange(currentPage - 1)
  })
  container.appendChild(btnPrev)

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button")
    btn.type = "button"
    btn.textContent = i
    btn.className =
      "px-3 py-1 text-sm rounded-lg border border-border " +
      (i === currentPage ? "bg-emerald-500 text-white" : "bg-card hover:bg-muted")
    btn.addEventListener("click", () => {
      if (i !== currentPage) onPageChange(i)
    })
    container.appendChild(btn)
  }

  const btnNext = document.createElement("button")
  btnNext.type = "button"
  btnNext.className = "px-3 py-1 text-sm rounded-lg border border-border bg-card hover:bg-muted disabled:opacity-40"
  btnNext.textContent = "Siguiente"
  btnNext.disabled = currentPage === totalPages
  btnNext.addEventListener("click", () => {
    if (currentPage < totalPages) onPageChange(currentPage + 1)
  })
  container.appendChild(btnNext)
}

// =========================
// TABLE AND CARDS RENDERING
// =========================

function renderTable() {
  const search = inputBuscar.value.trim().toLowerCase()
  const estadoFilter = selectFiltroEstado ? selectFiltroEstado.value : ""

  const filtered = fichas.filter((f) => {
    const matchNumero = String(f.numero_ficha).toLowerCase().includes(search)
    let matchEstado = true
    if (estadoFilter === "1") matchEstado = f.estado === true
    else if (estadoFilter === "0") matchEstado = f.estado === false
    return matchNumero && matchEstado
  })

  const totalItems = filtered.length

  const clearRenderedContent = () => {
    tbodyFichas.innerHTML = ""
    cardsContainer.innerHTML = ""
    if (paginationTabla) paginationTabla.innerHTML = ""
  }

  if (fichas.length === 0) {
    clearRenderedContent()
    vistaTabla.classList.add("hidden")
    vistaTarjetas.classList.add("hidden")
    if (emptyStateContainer) emptyStateContainer.classList.remove("hidden")
    if (emptySearchContainer) emptySearchContainer.classList.add("hidden")
    return
  }

  if (totalItems === 0) {
    clearRenderedContent()
    vistaTabla.classList.add("hidden")
    vistaTarjetas.classList.add("hidden")
    if (emptyStateContainer) emptyStateContainer.classList.add("hidden")
    if (emptySearchContainer) emptySearchContainer.classList.remove("hidden")
    return
  }

  if (emptyStateContainer) emptyStateContainer.classList.add("hidden")
  if (emptySearchContainer) emptySearchContainer.classList.add("hidden")

  if (btnVistaTabla.classList.contains("bg-muted")) {
    vistaTabla.classList.remove("hidden")
  }
  if (btnVistaTarjetas.classList.contains("bg-muted")) {
    vistaTarjetas.classList.remove("hidden")
  }

  const totalPagesTable = Math.max(1, Math.ceil(totalItems / PAGE_SIZE_TABLE) || 1)
  const totalPagesCards = Math.max(1, Math.ceil(totalItems / PAGE_SIZE_CARDS) || 1)

  if (currentPageTable > totalPagesTable) currentPageTable = totalPagesTable
  if (currentPageCards > totalPagesCards) currentPageCards = totalPagesCards

  const startIndexTable = (currentPageTable - 1) * PAGE_SIZE_TABLE
  const endIndexTable = startIndexTable + PAGE_SIZE_TABLE
  const pageItemsTable = filtered.slice(startIndexTable, endIndexTable)

  const startIndexCards = (currentPageCards - 1) * PAGE_SIZE_CARDS
  const endIndexCards = startIndexCards + PAGE_SIZE_CARDS
  const pageItemsCards = filtered.slice(startIndexCards, endIndexCards)

  tbodyFichas.innerHTML = ""

  pageItemsTable.forEach((ficha) => {
    const tr = document.createElement("tr")
    tr.className = "hover:bg-muted/40"

    const estadoBadgeClass = ficha.estado ? "badge-estado-activo" : "badge-estado-inactivo"
    const programaNombre = ficha.id_programa ? programasMap[String(ficha.id_programa)] || "Sin asignar" : "Sin asignar"
    const instructorNombre = ficha.id_instructor
      ? instructoresMap[String(ficha.id_instructor)] || "Sin asignar"
      : "Sin asignar"

    tr.innerHTML = `
      <td class="px-4 py-3 align-middle">
        <div class="flex items-center gap-3">
          <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <span class="font-medium text-sm">${ficha.numero_ficha}</span>
        </div>
      </td>
      <td class="px-4 py-3 align-middle">
        <div class="flex items-center gap-2">
          <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
          <span class="text-sm">${programaNombre}</span>
        </div>
      </td>
      <td class="px-4 py-3 align-middle">
        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ${
          nivelBadgeStyles[ficha.nivel] || "badge-nivel-default"
        }">
          ${nivelLabels[ficha.nivel] || ficha.nivel || "N/A"}
        </span>
      </td>
      <td class="px-4 py-3 align-middle">
        <div class="flex items-center gap-2">
          <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span class="text-sm">${instructorNombre}</span>
        </div>
      </td>
      <td class="px-4 py-3 align-middle">
        <span class="badge-estado-base ${estadoBadgeClass}">
          ${ficha.estado ? "Activa" : "Inactiva"}
        </span>
      </td>
      <td class="px-4 py-3 align-middle text-right">
        <div class="relative inline-block text-left">
          <button
            type="button"
            class="inline-flex h-8 w-8 items-center justify-center rounded-md hover:bg-muted text-slate-800"
            data-menu-trigger="${ficha.id}"
          >
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
              <circle cx="5" cy="12" r="1.5"></circle>
              <circle cx="12" cy="12" r="1.5"></circle>
              <circle cx="19" cy="12" r="1.5"></circle>
            </svg>
          </button>
          <div
            class="dropdown-menu hidden absolute right-0 mt-2 w-48 rounded-xl border border-border bg-popover shadow-md py-1 z-50"
            data-menu="${ficha.id}"
          >
            <button
              type="button"
              class="flex w-full items-center px-3 py-2 text-sm text-slate-700 hover:bg-muted"
              data-action="ver"
              data-id="${ficha.id}"
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
              data-id="${ficha.id}"
            >
              <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9"/>
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
              data-id="${ficha.id}"
            >
              ${
                ficha.estado
                  ? `<svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    Desactivar`
                  : `<svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Activar`
              }
            </button>
          </div>
        </div>
      </td>
    `

    tbodyFichas.appendChild(tr)
  })

  cardsContainer.innerHTML = ""

  pageItemsCards.forEach((ficha) => {
    const programaNombre = ficha.id_programa ? programasMap[String(ficha.id_programa)] || "Sin asignar" : "Sin asignar"
    const instructorNombre = ficha.id_instructor
      ? instructoresMap[String(ficha.id_instructor)] || "Sin asignar"
      : "Sin asignar"

    const card = document.createElement("div")
    card.className = "rounded-2xl border border-border bg-card p-4 shadow-sm flex flex-col"

    card.innerHTML = `
      <div class="flex items-start justify-between gap-2 mb-3">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div>
            <p class="font-semibold text-base">${ficha.numero_ficha}</p>
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ${
              nivelBadgeStyles[ficha.nivel] || "badge-nivel-default"
            }">
              ${nivelLabels[ficha.nivel] || ficha.nivel || "N/A"}
            </span>
          </div>
        </div>

        <div class="relative inline-block text-left">
          <button
            type="button"
            class="inline-flex h-7 w-7 items-center justify-center rounded-md hover:bg-muted text-slate-800"
            data-menu-trigger="${ficha.id}"
          >
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
              <circle cx="5" cy="12" r="1.5"></circle>
              <circle cx="12" cy="12" r="1.5"></circle>
              <circle cx="19" cy="12" r="1.5"></circle>
            </svg>
          </button>
          <div
            class="dropdown-menu hidden absolute right-0 mt-2 w-40 rounded-xl border border-border bg-popover shadow-md py-1 z-50"
            data-menu="${ficha.id}"
          >
            <button
              type="button"
              class="flex w-full items-center px-3 py-2 text-xs text-slate-700 hover:bg-muted"
              data-action="ver"
              data-id="${ficha.id}"
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
              data-id="${ficha.id}"
            >
              <svg class="mr-2 h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9"/>
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
              data-id="${ficha.id}"
            >
              ${
                ficha.estado
                  ? `<svg class="mr-2 h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    Desactivar`
                  : `<svg class="mr-2 h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Activar`
              }
            </button>
          </div>
        </div>
      </div>

      <div class="space-y-2 text-sm text-muted-foreground flex-1">
        <div class="flex items-center gap-2">
          <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
          <span>${programaNombre}</span>
        </div>
        <div class="flex items-center gap-2">
          <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span>${instructorNombre}</span>
        </div>
      </div>

      <hr class="border-border my-3" />

      <div class="flex items-center justify-between">
        <span class="text-sm ${ficha.estado ? "text-foreground" : "text-muted-foreground"}">${ficha.estado ? "Activa" : "Inactiva"}</span>
        <button
          type="button"
          class="switch-siga ${ficha.estado ? "on" : "off"}"
          onclick="toggleStatus('${ficha.id}')"
        >
          <span class="thumb" style="transform: translateX(${ficha.estado ? "20px" : "0px"});"></span>
        </button>
      </div>
    `

    cardsContainer.appendChild(card)
  })

  attachMenuEvents()

  const tablaVisible = !vistaTabla.classList.contains("hidden")

  if (tablaVisible) {
    renderPaginationControls(paginationTabla, totalItems, PAGE_SIZE_TABLE, currentPageTable, (page) => {
      currentPageTable = page
      renderTable()
    })
  } else {
    renderPaginationControls(paginationTabla, totalItems, PAGE_SIZE_CARDS, currentPageCards, (page) => {
      currentPageCards = page
      renderTable()
    })
  }
}

// =========================
// DROPDOWN MENU HANDLING
// =========================

function attachMenuEvents() {
  document.addEventListener("click", (e) => {
    if (!e.target.closest("[data-menu-trigger]") && !e.target.closest("[data-menu]")) {
      document.querySelectorAll("[data-menu]").forEach((el) => {
        el.classList.add("hidden")
        el.classList.remove("show")
      })
    }
  })

  document.querySelectorAll("[data-menu-trigger]").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.stopPropagation()

      const wrapper = btn.closest(".relative, .inline-block, td, div")
      if (!wrapper) return

      const menu = wrapper.querySelector("[data-menu]")
      if (!menu) return

      const isHidden = menu.classList.contains("hidden")

      document.querySelectorAll("[data-menu]").forEach((el) => {
        el.classList.add("hidden")
        el.classList.remove("show")
      })

      if (isHidden) {
        menu.classList.remove("hidden")
        requestAnimationFrame(() => {
          menu.classList.add("show")
        })
      } else {
        menu.classList.remove("show")
        setTimeout(() => {
          menu.classList.add("hidden")
        }, 150)
      }
    })
  })

  document.querySelectorAll("[data-menu] [data-action]").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.stopPropagation()

      const action = btn.getAttribute("data-action")
      const id = btn.getAttribute("data-id")
      const ficha = fichas.find((f) => String(f.id) === String(id))
      if (!ficha) return

      if (action === "ver") {
        openModalVerFicha(ficha)
      } else if (action === "editar") {
        openModalFicha(ficha)
      } else if (action === "toggle") {
        toggleStatus(id)
      }

      const menu = btn.closest("[data-menu]")
      if (menu) {
        menu.classList.add("hidden")
        menu.classList.remove("show")
      }
    })
  })
}

// =========================
// GLOBAL EVENT LISTENERS
// =========================

inputBuscar.addEventListener("input", () => {
  currentPageTable = 1
  currentPageCards = 1
  renderTable()
})

if (selectFiltroEstado) {
  selectFiltroEstado.addEventListener("change", () => {
    currentPageTable = 1
    currentPageCards = 1
    renderTable()
  })
}

btnNuevaFicha.addEventListener("click", () => openModalFicha(null))
btnCerrarModalFicha.addEventListener("click", closeModalFicha)
btnCancelarModalFicha.addEventListener("click", closeModalFicha)

btnCerrarModalVerFicha.addEventListener("click", closeModalVerFicha)

btnVistaTabla.addEventListener("click", setVistaTabla)
btnVistaTarjetas.addEventListener("click", setVistaTarjetas)

// ================================
// FORM VALIDATION AND SUBMISSION
// ================================
formFicha.addEventListener("submit", async (e) => {
  e.preventDefault()

  const payload = {
    numero_ficha: inputNumeroFicha.value.trim(),
    id_programa: inputPrograma.value || null,
    nivel: inputNivel.value,
    id_instructor: inputInstructor.value || null,
  }

  const isEdit = !!hiddenFichaId.value
  const numeroRegex = /^[0-9]+$/

  const allEmpty = !payload.numero_ficha && !payload.id_programa && !payload.id_instructor

  if (allEmpty) {
    toastError("Todos los campos son obligatorios.")
    inputNumeroFicha.focus()
    return
  }

  if (!payload.numero_ficha) {
    toastError("El número de ficha es obligatorio.")
    inputNumeroFicha.focus()
    return
  }

  if (!numeroRegex.test(payload.numero_ficha)) {
    toastError("El número de ficha solo puede contener números.")
    inputNumeroFicha.focus()
    return
  }

  if (!payload.id_programa) {
    toastError("Debe seleccionar un programa de formación.")
    inputPrograma.focus()
    return
  }

  if (!payload.nivel) {
    toastError("Debe seleccionar un nivel.")
    inputNivel.focus()
    return
  }

  if (!payload.id_instructor) {
    toastError("Debe seleccionar un instructor.")
    inputInstructor.focus()
    return
  }

  if (!VALID_NIVELES.includes(payload.nivel)) {
    toastError("Nivel no válido.")
    return
  }

  if (isEdit && originalEditData) {
    const currentData = {
      numero_ficha: payload.numero_ficha,
      id_programa: payload.id_programa ? String(payload.id_programa) : "",
      nivel: payload.nivel,
      id_instructor: payload.id_instructor ? String(payload.id_instructor) : "",
    }

    const noHayCambios = JSON.stringify(currentData) === JSON.stringify(originalEditData)

    if (noHayCambios) {
      toastInfo("Para actualizar el registro es necesario modificar al menos un dato de la ficha.")
      return
    }
  }

  if (isEdit) {
    payload.id_ficha = hiddenFichaId.value
  }

  try {
    const data = isEdit ? await actualizarFicha(payload) : await crearFicha(payload)

    if (data.error) {
      toastError(data.error || "Ocurrió un error al procesar la solicitud.")
      return
    }

    toastSuccess(data.mensaje || (isEdit ? "Ficha actualizada correctamente." : "Ficha creada correctamente."))

    closeModalFicha()
    await cargarFichas()
  } catch (error) {
    console.error("Error de red al guardar ficha:", error)
    toastError("Ocurrió un error al guardar la ficha (red/servidor).")
  }
})

// ================================
// KEYBOARD SHORTCUTS: CLOSE MODALS WITH ESC
// ================================
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" || e.key === "Esc" || e.keyCode === 27) {
    if (modalFicha && modalFicha.classList.contains("active")) {
      closeModalFicha()
    }

    if (modalVerFicha && modalVerFicha.classList.contains("active")) {
      closeModalVerFicha()
    }
  }
})

// ================================
// INITIAL LOAD
// ================================
cargarFichas()
cargarProgramas()
cargarInstructores()
setVistaTabla()
