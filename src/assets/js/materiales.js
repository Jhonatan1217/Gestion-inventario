/* materiales.js — Integración con backend PHP */

/* =========================
   Variables globales
   ========================= */
let materialsData = []
let filteredData = []
let currentPage = 1
let currentCardPage = 1
const itemsPerPage = 10
const cardsPerPage = 9
let currentView = "table"

// API endpoints
const API_URL = window.location.origin + "/Gestion-inventario/src/controllers/material_formacion_controller.php"

/* =========================
   API Functions
   ========================= */
async function fetchMaterials() {
  try {
    const response = await fetch(`${API_URL}?accion=listar`)
    if (!response.ok) throw new Error("Error al cargar materiales")
    const data = await response.json()
    materialsData = data.map((material) => ({
      id: Number.parseInt(material.id_material),
      name: material.nombre,
      description: material.descripcion,
      clasificacion: material.clasificacion,
      codigo: material.codigo_inventario,
      unit: material.unidad_medida,
      enabled: material.estado === "Disponible",
    }))
    filteredData = [...materialsData]
    if (currentView === "table") {
      renderTable()
    } else {
      renderCards()
    }
  } catch (error) {
    console.error("Error:", error)
    showAlert("Error al cargar los materiales", "error")
  }
}

async function createMaterialAPI(materialData) {
  try {
    const response = await fetch(`${API_URL}?accion=crear`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        nombre: materialData.nombre,
        descripcion: materialData.descripcion,
        clasificacion: materialData.clasificacion,
        codigo_inventario: materialData.codigo_inventario,
        unidad_medida: materialData.unidad_medida,
      }),
    })
    const result = await response.json()
    return result
  } catch (error) {
    console.error("Error:", error)
    return { success: false, message: "Error de conexión" }
  }
}

async function updateMaterialAPI(id, materialData) {
  try {
    const response = await fetch(`${API_URL}?accion=actualizar&id=${id}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        nombre: materialData.nombre,
        descripcion: materialData.descripcion,
        clasificacion: materialData.clasificacion,
        codigo_inventario: materialData.codigo_inventario,
        unidad_medida: materialData.unidad_medida,
        estado: materialData.estado,
      }),
    })
    const result = await response.json()
    return result
  } catch (error) {
    console.error("Error:", error)
    return { success: false, message: "Error de conexión" }
  }
}

async function toggleMaterialStatusAPI(id) {
  try {
    const response = await fetch(`${API_URL}?accion=toggleEstado&id=${id}`, {
      method: "GET",
    })
    const result = await response.json()
    return result
  } catch (error) {
    console.error("Error:", error)
    return { success: false, message: "Error de conexión" }
  }
}

/* =========================
  Helper Functions
  ========================= */

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
    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
      <path d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l6.518 11.59A1.75 1.75 0 0 1 16.768 17H3.232a1.75 1.75 0 0 1-1.492-2.311L8.257 3.1z"/>
      <path d="M11 13H9V9h2zm0 3H9v-2h2z" fill="#fff"/>
    </svg>
  `

  if (type === "success") {
    borderColor = "border-emerald-500"
    textColor = "text-emerald-900"
    titleText = "Éxito"
    iconSVG = `
      <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm-1 15-4-4 1.414-1.414L9 12.172l4.586-4.586L15 9z"/>
      </svg>
    `
  }

  if (type === "error") {
    borderColor = "border-red-500"
    textColor = "text-red-900"
    titleText = "Error"
    iconSVG = `
      <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm3.707 12.293-1.414 1.414L10 11.414l-2.293 2.293-1.414-1.414L8.586 10 6.293 7.707l1.414-1.414L10 8.586l2.293-2.293 1.414 1.414L11.414 10l2.293 2.293z"/>
      </svg>
    `
  }

  if (type === "info") {
    borderColor = "border-blue-500"
    textColor = "text-blue-900"
    titleText = "Información"
    iconSVG = `
      <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
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

function showAlert(message, type = "success") {
  showFlowbiteAlert(type, message)
}

function validateMaterialPayload(data, { isEdit = false, id = null } = {}) {
  const nameRegex = /^[A-Za-zÁÉÍÓÚÜÑñáéíóúüñ\s\-.]{3,80}$/
  const codeRegex = /^[A-Za-z0-9_-]{3,30}$/

  if (!data.nombre) {
    showAlert("El nombre es obligatorio", "error")
    document.getElementById(isEdit ? "editNombre" : "nombre")?.focus()
    return false
  }

  if (!nameRegex.test(data.nombre)) {
    showAlert("El nombre solo puede tener letras/números y 3-80 caracteres", "error")
    document.getElementById(isEdit ? "editNombre" : "nombre")?.focus()
    return false
  }

  if (!data.descripcion || data.descripcion.length < 5) {
    showAlert("La descripción debe tener al menos 5 caracteres", "error")
    document.getElementById(isEdit ? "editDescripcion" : "descripcion")?.focus()
    return false
  }

  if (!data.clasificacion) {
    showAlert("Seleccione la clasificación", "error")
    document.getElementById(isEdit ? "editClasificacion" : "clasificacion")?.focus()
    return false
  }

  if (!data.unidad_medida) {
    showAlert("Seleccione la unidad de medida", "error")
    document.getElementById(isEdit ? "editUnidad" : "unidad")?.focus()
    return false
  }

  if (data.clasificacion === "Inventariado") {
    if (!data.codigo_inventario) {
      showAlert("El código es obligatorio para inventariados", "error")
      document.getElementById(isEdit ? "editCodigo" : "codigo")?.focus()
      return false
    }
    if (!codeRegex.test(data.codigo_inventario)) {
      showAlert("Código inválido: 3-30 caracteres alfanuméricos, guion o guion bajo", "error")
      document.getElementById(isEdit ? "editCodigo" : "codigo")?.focus()
      return false
    }
  }

  const nombreDuplicado = materialsData.some(
    (m) => m.name.trim().toLowerCase() === data.nombre.trim().toLowerCase() && (!isEdit || m.id !== id),
  )

  if (nombreDuplicado) {
    showAlert("Ya existe un material con ese nombre", "error")
    return false
  }

  if (data.codigo_inventario) {
    const codigoDuplicado = materialsData.some(
      (m) => m.codigo && m.codigo.toLowerCase() === data.codigo_inventario.toLowerCase() && (!isEdit || m.id !== id),
    )
    if (codigoDuplicado) {
      showAlert("Ya existe un material con ese código", "error")
      return false
    }
  }

  return true
}

function getDataToRender() {
  return filteredData
}

/* =========================
   Íconos SVG
   ========================= */
const icons = {
  eye: '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>',
  edit: '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
  trash:
    '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>',
  menu: '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>',
  package:
    '<svg class="w-6 h-6 text-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>',
  email:
    '<svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4z"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 6l8 6 8-6"/></svg>',
  ruler:
    '<svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><rect x="2" y="7" width="20" height="10" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 12h.01M10 12h.01M14 12h.01M18 12h.01"/></svg>',
}

/* =================================================
   RENDER TABLE / CARDS
   ================================================= */
function renderTable() {
  const dataToRender = getDataToRender()
  const start = (currentPage - 1) * itemsPerPage
  const end = start + itemsPerPage
  const paginatedData = dataToRender.slice(start, end)

  const tableBody = document.getElementById("tableBody")
  tableBody.innerHTML = ""

  paginatedData.forEach((material) => {
    const statusClass = material.enabled ? "bg-success text-success-foreground" : "bg-gray-300 text-gray-600"
    const statusText = material.enabled ? "Disponible" : "Agotado"
    const rowClass = !material.enabled ? "disabled-row" : ""
    const codigoDisplay = material.codigo || "-"

    const row = document.createElement("tr")
    row.className = `hover:bg-muted transition-colors ${rowClass}`
    row.dataset.materialId = material.id

    row.innerHTML = `
      <td class="px-4 py-3">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center material-icon-bg" style="background-color: rgba(57, 169, 0, 0.1);">
            ${icons.package}
          </div>
          <div>
            <p class="font-medium">${material.name}</p>
          </div>
        </div>
      </td>
      <td class="px-4 py-3 text-sm max-w-xs truncate" title="${material.description}">${material.description}</td>
      <td class="px-4 py-3">
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${material.clasificacion === "Inventariado" ? "bg-blue-100 text-blue-700" : "bg-orange-100 text-orange-700"}">
          ${material.clasificacion}
        </span>
      </td>
      <td class="px-4 py-3 text-sm font-medium">${codigoDisplay}</td>
      <td class="px-4 py-3 text-sm">${material.unit}</td>
      <td class="px-4 py-3">
        <span class="inline-flex items-center gap-1 px-3 py-1 ${statusClass} text-xs font-medium rounded-full status-badge ${!material.enabled ? "inactive" : "active"}">
          <span class="w-2 h-2 ${!material.enabled ? "bg-red-700" : "bg-green-500"} rounded-full"></span>
          ${statusText}
        </span>
      </td>
      <td class="px-4 py-3 text-right">
        <div class="relative">
          <button class="text-muted-foreground hover:text-foreground transition-colors p-2 rounded-full hover:bg-muted card-menu-btn" onclick="toggleMenu(event)">
            ${icons.menu}
          </button>
          
          <div class="hidden absolute right-0 top-full mt-2 w-48 bg-card border border-border rounded-lg shadow-lg z-50 dropdown-menu">
            <button class="w-full px-4 py-2 text-left hover:bg-muted transition-colors flex items-center gap-2 text-foreground rounded-t-lg text-sm" onclick="openDetailsModal(${material.id})">
              ${icons.eye}
              Ver detalle
            </button>
            <button class="w-full px-4 py-2 text-left hover:bg-muted transition-colors flex items-center gap-2 text-foreground text-sm" onclick="openEditModal(${material.id})">
              ${icons.edit}
              Editar
            </button>
            <button class="w-full px-4 py-2 text-left transition-colors flex items-center gap-2 text-sm rounded-b-lg toggle-status-table-btn" data-id="${material.id}" onclick="toggleMaterialStatus(${material.id}, event)" style="color: ${!material.enabled ? "#16a34a" : "#dc2626"}; background-color: ${!material.enabled ? "#dcfce7" : "#fee2e2"}; border-radius: 0 0 0.5rem 0.5rem;">
              ${icons.trash}
              <span class="toggle-text">${!material.enabled ? "Activar" : "Desactivar"}</span>
            </button>
          </div>
        </div>
      </td>
    `

    tableBody.appendChild(row)
  })

  renderPagination()
}

function renderCards() {
  const dataToRender = getDataToRender()
  const start = (currentCardPage - 1) * cardsPerPage
  const end = start + cardsPerPage
  const paginatedData = dataToRender.slice(start, end)

  const cardsContainer = document.getElementById("cardsContainer")
  cardsContainer.innerHTML = ""

  paginatedData.forEach((material) => {
    const statusClass = material.enabled ? "bg-success text-success-foreground" : "bg-gray-300 text-gray-600"
    const statusText = material.enabled ? "Disponible" : "Agotado"
    const codigoDisplay = material.codigo || "Sin código"

    const card = document.createElement("div")
    card.className = `rounded-2xl border border-border bg-card p-3 shadow-sm flex flex-col gap-2 ${
      !material.enabled ? "disabled" : ""
    }`
    card.dataset.materialId = material.id

    card.innerHTML = `
      <div class="flex items-start justify-between gap-2">
        <div class="flex items-center gap-2">
          <div class="flex h-10 w-10 items-center justify-center rounded-full material-icon-bg" style="background-color: rgba(57, 169, 0, 0.1);">
            ${icons.package}
          </div>
          <div class="space-y-0.5">
            <p class="font-semibold text-xs sm:text-sm leading-snug">${material.name}</p>
            <p class="text-[11px] sm:text-xs text-muted-foreground">${codigoDisplay}</p>
          </div>
        </div>

        <div class="relative inline-block text-left">
          <button
            type="button"
            class="inline-flex h-6 w-6 items-center justify-center rounded-md hover:bg-muted text-slate-800"
            data-menu-trigger="${material.id}"
            onclick="toggleCardMenu(event)"
          >
            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
              <circle cx="5" cy="12" r="1.5"></circle>
              <circle cx="12" cy="12" r="1.5"></circle>
              <circle cx="19" cy="12" r="1.5"></circle>
            </svg>
          </button>
          <div
            class="dropdown-menu hidden absolute right-0 mt-2 w-40 rounded-xl border border-border bg-popover shadow-md py-1"
            data-menu="${material.id}"
          >
            <button
              type="button"
              class="flex w-full items-center px-3 py-2 text-xs text-slate-700 hover:bg-muted"
              onclick="event.stopPropagation(); openDetailsModal(${material.id})"
            >
              ${icons.eye}
              Ver detalles
            </button>
            <button
              type="button"
              class="flex w-full items-center px-3 py-2 text-xs text-slate-700 hover:bg-muted"
              onclick="event.stopPropagation(); openEditModal(${material.id})"
            >
              ${icons.edit}
              Editar
            </button>
            <hr class="border-border my-1">
            <button
              type="button"
              class="flex w-full items-center px-3 py-2 text-xs text-slate-700 hover:bg-muted toggle-status-card-btn"
              data-id="${material.id}"
              onclick="event.stopPropagation(); toggleMaterialStatus(${material.id}, event)"
            >
              ${icons.trash}
              ${material.enabled ? "Desactivar" : "Activar"}
            </button>
          </div>
        </div>
      </div>

      <div class="space-y-1 text-[11px] sm:text-xs text-muted-foreground">
        <div class="flex items-center gap-2">
          ${icons.email}
          <span>${material.description}</span>
        </div>
        <div class="flex items-center gap-2">
          ${icons.ruler}
          <span>${material.unit}</span>
        </div>
      </div>

      <div class="flex items-center justify-between mt-1">
        <div class="flex flex-wrap gap-2">
          <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium ${material.clasificacion === "Inventariado" ? "bg-blue-100 text-blue-700" : "bg-orange-100 text-orange-700"}">
            ${material.clasificacion}
          </span>
          <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-medium ${statusClass}">
            ${statusText}
          </span>
        </div>
      </div>

      <hr class="border-border my-1" />

      <div class="flex justify-end">
        <button
          type="button"
          class="switch-siga ${material.enabled ? "on" : "off"}"
          onclick="toggleMaterialStatus(${material.id}, event)"
        >
          <span class="thumb" style="transform: translateX(${material.enabled ? "18px" : "0px"});"></span>
        </button>
      </div>
    `

    cardsContainer.appendChild(card)
  })

  renderCardPagination()
}

/* =========================
   PAGINATION
   ========================= */
function renderPagination() {
  const dataToRender = getDataToRender()
  const totalPages = Math.ceil(dataToRender.length / itemsPerPage)
  const paginationContainer = document.getElementById("pagination")
  paginationContainer.innerHTML = ""

  if (totalPages <= 1) return

  const prevBtn = document.createElement("button")
  prevBtn.textContent = "Anterior"
  prevBtn.disabled = currentPage === 1
  prevBtn.onclick = () => {
    if (currentPage > 1) {
      currentPage--
      renderTable()
    }
  }
  paginationContainer.appendChild(prevBtn)

  for (let i = 1; i <= totalPages; i++) {
    const pageBtn = document.createElement("button")
    pageBtn.textContent = i
    if (i === currentPage) pageBtn.classList.add("active")
    pageBtn.onclick = () => {
      currentPage = i
      renderTable()
    }
    paginationContainer.appendChild(pageBtn)
  }

  const nextBtn = document.createElement("button")
  nextBtn.textContent = "Siguiente"
  nextBtn.disabled = currentPage === totalPages
  nextBtn.onclick = () => {
    if (currentPage < totalPages) {
      currentPage++
      renderTable()
    }
  }
  paginationContainer.appendChild(nextBtn)
}

function renderCardPagination() {
  const dataToRender = getDataToRender()
  const totalPages = Math.ceil(dataToRender.length / cardsPerPage)
  const paginationContainer = document.getElementById("cardPagination")
  paginationContainer.innerHTML = ""

  if (totalPages <= 1) return

  const prevBtn = document.createElement("button")
  prevBtn.textContent = "Anterior"
  prevBtn.disabled = currentCardPage === 1
  prevBtn.onclick = () => {
    if (currentCardPage > 1) {
      currentCardPage--
      renderCards()
    }
  }
  paginationContainer.appendChild(prevBtn)

  for (let i = 1; i <= totalPages; i++) {
    const pageBtn = document.createElement("button")
    pageBtn.textContent = i
    if (i === currentCardPage) pageBtn.classList.add("active")
    pageBtn.onclick = () => {
      currentCardPage = i
      renderCards()
    }
    paginationContainer.appendChild(pageBtn)
  }

  const nextBtn = document.createElement("button")
  nextBtn.textContent = "Siguiente"
  nextBtn.disabled = currentCardPage === totalPages
  nextBtn.onclick = () => {
    if (currentCardPage < totalPages) {
      currentCardPage++
      renderCards()
    }
  }
  paginationContainer.appendChild(nextBtn)
}

/* =========================
   MODALS
   ========================= */
function openCreateModal() {
  document.getElementById("createModal").classList.add("active")
}

function closeCreateModal() {
  document.getElementById("createModal").classList.remove("active")
  document.getElementById("nombre").value = ""
  document.getElementById("descripcion").value = ""
  document.getElementById("clasificacion").value = ""
  document.getElementById("codigo").value = ""
  document.getElementById("unidad").value = ""
  document.getElementById("codigoContainer").style.display = "none"
}

function openDetailsModal(id) {
  const material = materialsData.find((m) => m.id === id)
  if (!material) return

  document.getElementById("detailName").textContent = material.name
  document.getElementById("detailCode").textContent = material.codigo || "Sin código"
  document.getElementById("detailDescription").textContent = material.description
  document.getElementById("detailClasificacion").textContent = material.clasificacion
  document.getElementById("detailUnidad").textContent = material.unit
  document.getElementById("detailStatus").textContent = material.enabled ? "Disponible" : "Agotado"
  document.getElementById("detailStatus").className = material.enabled
    ? "inline-block px-2 py-1 rounded text-xs font-medium bg-success text-success-foreground"
    : "inline-block px-2 py-1 rounded text-xs font-medium bg-gray-300 text-gray-600"

  document.getElementById("detailsModal").classList.add("active")
}

function closeDetailsModal() {
  document.getElementById("detailsModal").classList.remove("active")
}

function openEditModal(id) {
  const material = materialsData.find((m) => m.id === id)
  if (!material) return

  document.getElementById("editId").value = material.id
  document.getElementById("editNombre").value = material.name
  document.getElementById("editDescripcion").value = material.description
  document.getElementById("editClasificacion").value = material.clasificacion
  document.getElementById("editCodigo").value = material.codigo || ""
  document.getElementById("editUnidad").value = material.unit

  toggleEditCodigoField()
  document.getElementById("editModal").classList.add("active")
}

function closeEditModal() {
  document.getElementById("editModal").classList.remove("active")
}

function toggleCodigoField() {
  const clasificacion = document.getElementById("clasificacion").value
  const codigoContainer = document.getElementById("codigoContainer")
  const codigoInput = document.getElementById("codigo")

  if (clasificacion === "Inventariado") {
    codigoContainer.style.display = "block"
    codigoInput.required = true
  } else {
    codigoContainer.style.display = "none"
    codigoInput.required = false
    codigoInput.value = ""
  }
}

function toggleEditCodigoField() {
  const clasificacion = document.getElementById("editClasificacion").value
  const codigoContainer = document.getElementById("editCodigoContainer")
  const codigoInput = document.getElementById("editCodigo")

  if (clasificacion === "Inventariado") {
    codigoContainer.style.display = "block"
    codigoInput.required = true
  } else {
    codigoContainer.style.display = "none"
    codigoInput.required = false
    codigoInput.value = ""
  }
}

/* =========================
   CRUD OPERATIONS
   ========================= */
async function createMaterial() {
  const materialData = {
    nombre: document.getElementById("nombre").value.trim(),
    descripcion: document.getElementById("descripcion").value.trim(),
    clasificacion: document.getElementById("clasificacion").value,
    codigo_inventario: document.getElementById("codigo").value.trim() || null,
    unidad_medida: document.getElementById("unidad").value,
  }

  if (!validateMaterialPayload(materialData)) return

  const result = await createMaterialAPI(materialData)

  if (result.success) {
    showAlert(result.message || "Material creado exitosamente", "success")
    closeCreateModal()
    await fetchMaterials()
  } else {
    showAlert(result.message || "Error al crear el material", "error")
  }
}

async function updateMaterial() {
  const id = Number.parseInt(document.getElementById("editId").value)
  const materialData = {
    nombre: document.getElementById("editNombre").value.trim(),
    descripcion: document.getElementById("editDescripcion").value.trim(),
    clasificacion: document.getElementById("editClasificacion").value,
    codigo_inventario: document.getElementById("editCodigo").value.trim() || null,
    unidad_medida: document.getElementById("editUnidad").value,
    estado: materialsData.find((m) => m.id === id)?.enabled ? "Disponible" : "Agotado",
  }

  if (!validateMaterialPayload(materialData, { isEdit: true, id })) return

  const result = await updateMaterialAPI(id, materialData)

  if (result.success) {
    showAlert(result.message || "Material actualizado exitosamente", "success")
    closeEditModal()
    await fetchMaterials()
  } else {
    showAlert(result.message || "Error al actualizar el material", "error")
  }
}

async function toggleMaterialStatus(id, event) {
  event?.stopPropagation()

  const result = await toggleMaterialStatusAPI(id)

  if (result.success) {
    showAlert(result.message || "Estado actualizado", "success")
    await fetchMaterials()
  } else {
    showAlert(result.message || "Error al cambiar el estado", "error")
  }
}

/* =========================
   MENU TOGGLES
   ========================= */
function toggleMenu(event) {
  event.stopPropagation()
  const button = event.currentTarget
  const dropdown = button.nextElementSibling

  document.querySelectorAll(".dropdown-menu").forEach((menu) => {
    if (menu !== dropdown) menu.classList.add("hidden")
  })

  dropdown.classList.toggle("hidden")
  if (!dropdown.classList.contains("hidden")) {
    dropdown.classList.add("show")
  } else {
    dropdown.classList.remove("show")
  }
}

function toggleCardMenu(event) {
  event.stopPropagation()
  const button = event.currentTarget
  const menuId = button.getAttribute("data-menu-trigger")
  const dropdown = document.querySelector(`[data-menu="${menuId}"]`)

  document.querySelectorAll(".dropdown-menu").forEach((menu) => {
    if (menu !== dropdown) menu.classList.add("hidden")
  })

  if (dropdown) {
    dropdown.classList.toggle("hidden")
  }
}

document.addEventListener("click", (event) => {
  if (
    !event.target.closest(".dropdown-menu") &&
    !event.target.closest("button[data-menu-trigger]") &&
    !event.target.closest(".card-menu-btn")
  ) {
    document.querySelectorAll(".dropdown-menu").forEach((menu) => {
      menu.classList.add("hidden")
      menu.classList.remove("show")
    })
  }
})

/* =========================
   VIEW TOGGLE
   ========================= */
function switchToTableView() {
  currentView = "table"
  document.getElementById("tableView").classList.remove("hidden")
  document.getElementById("cardView").classList.add("hidden")
  document.getElementById("tableViewBtn").classList.add("bg-muted", "text-foreground")
  document.getElementById("tableViewBtn").classList.remove("text-muted-foreground")
  document.getElementById("cardViewBtn").classList.remove("bg-muted", "text-foreground")
  document.getElementById("cardViewBtn").classList.add("text-muted-foreground")
  renderTable()
}

function switchToCardView() {
  currentView = "card"
  document.getElementById("tableView").classList.add("hidden")
  document.getElementById("cardView").classList.remove("hidden")
  document.getElementById("cardViewBtn").classList.add("bg-muted", "text-foreground")
  document.getElementById("cardViewBtn").classList.remove("text-muted-foreground")
  document.getElementById("tableViewBtn").classList.remove("bg-muted", "text-foreground")
  document.getElementById("tableViewBtn").classList.add("text-muted-foreground")
  renderCards()
}

/* =========================
   SEARCH & FILTER
   ========================= */
function applyFilters() {
  const searchTerm = document.getElementById("inputBuscar").value.toLowerCase()
  const filterClasificacion = document.getElementById("selectFiltroRol").value

  filteredData = materialsData.filter((material) => {
    const matchesSearch =
      material.name.toLowerCase().includes(searchTerm) || material.description.toLowerCase().includes(searchTerm)

    const matchesClasificacion = !filterClasificacion || material.clasificacion === filterClasificacion

    return matchesSearch && matchesClasificacion
  })

  currentPage = 1
  currentCardPage = 1

  if (currentView === "table") {
    renderTable()
  } else {
    renderCards()
  }
}

/* =========================
   EVENT LISTENERS
   ========================= */
document.addEventListener("DOMContentLoaded", () => {
  fetchMaterials()

  document.getElementById("tableViewBtn").addEventListener("click", switchToTableView)
  document.getElementById("cardViewBtn").addEventListener("click", switchToCardView)

  document.getElementById("inputBuscar").addEventListener("input", applyFilters)
  document.getElementById("selectFiltroRol").addEventListener("change", applyFilters)
})
