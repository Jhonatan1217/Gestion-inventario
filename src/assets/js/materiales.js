/* materiales.js — integrado con backend (mantiene todas las funciones UI) */

/* ============ CONFIG / API ============ */
const API_URL = "http://localhost/Gestion-inventario/src/controllers/material_formacion_controller.php";

let materialsData = []; // ahora poblado desde el backend
let currentPage = 1
let currentCardPage = 1
const itemsPerPage = 10
const cardsPerPage = 10
const disabledMaterials = new Set()
let currentView = "table"

const icons = {
  eye: '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>',
  edit: '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>',
  trash:
    '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>',
  menu: '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>',
  package:
    '<svg class="w-6 h-6 text-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>',
  clock:
    '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
}

function isActiveStatus(val) {
  if (!val) return true
  const v = val.toString().trim().toLowerCase()
  return v === 'activo' || v === 'disponible'
}

/* =========================
   Helpers: normalizar data
   ========================= */
function normalizeMaterial(m) {
  // Backend fields: id_material, nombre, descripcion, unidad_medida, clasificacion, codigo_inventario, estado
  // Split clasificacion "Categoria / Tipo" en category y type si aplica
  let category = m.categoria ?? ''
  let type = m.tipo ?? ''
  if (!category || !type) {
    const cls = m.clasificacion ?? ''
    if (cls && cls.includes('/')) {
      const parts = cls.split('/').map(s => s.trim())
      category = category || parts[0] || ''
      type = type || parts[1] || ''
    } else if (cls) {
      // si viene solo un valor, úsalo como categoría
      category = category || cls
    }
  }

  // Extraer posibles piezas desde descripcion compuesta para edición
  const desc = m.descripcion ?? m.description ?? ''
  let minStock = m.stock_minimo ?? 0
  let warehouse = m.bodega ?? 'Bodega Principal'
  let stockActual = m.stock_total ?? m.stock_actual ?? 0
  let observacion = ''
  if (desc) {
    const obsMatch = desc.match(/\bOBS:\s*([^|]+)\b/i)
    const bodMatch = desc.match(/\bBODEGA:\s*([^|]+)\b/i)
    const minMatch = desc.match(/\bSTOCK_MIN:\s*([^|]+)\b/i)
    const actMatch = desc.match(/\bSTOCK_ACT:\s*([^|]+)\b/i)
    if (obsMatch) observacion = obsMatch[1].trim()
    if (bodMatch) warehouse = bodMatch[1].trim()
    if (minMatch) {
      const num = parseInt(minMatch[1].trim(), 10)
      if (!isNaN(num)) minStock = num
    }
    if (actMatch) {
      const numA = parseInt(actMatch[1].trim(), 10)
      if (!isNaN(numA)) stockActual = numA
    }
  }

  const unitKey = (() => {
    const keys = Object.keys(m || {})
    for (const k of keys) {
      const kl = k.toLowerCase()
      if (kl.includes('unidad') || kl.includes('unit')) return k
    }
    return null
  })()

  const unitVal = unitKey ? m[unitKey] : (m.unidad_medida ?? m.unidad ?? m.unit)

  return {
    id_material: m.id_material ?? m.id ?? null,
    code: m.codigo_inventario ?? `MAT-${m.id_material ?? m.id ?? Math.random().toString(36).slice(2,8)}`,
    name: m.nombre ?? m.name ?? '',
    category,
    type,
    unit: (unitVal ?? '').toString().trim(),
    // stock/minStock/warehouse: derivar de descripcion o campos directos
    stock: stockActual,
    minStock,
    warehouse,
    description: desc,
    observacion,
    estado: m.estado ?? 'Activo'
  }
}

// Asegura que un select tenga la opción indicada; si no existe, la crea y la selecciona
function ensureSelectOption(selectEl, value) {
  if (!selectEl) return
  const val = (value ?? '').toString().trim()
  if (val === '') return
  const exists = Array.from(selectEl.options).some(opt => (opt.value || opt.text).trim() === val)
  if (!exists) {
    const opt = document.createElement('option')
    opt.value = val
    opt.text = val
    selectEl.appendChild(opt)
  }
  selectEl.value = val
}

/* ================
   FETCH / BACKEND
   ================ */
async function loadMaterials() {
  try {
    const res = await fetch(`${API_URL}?accion=listar`, { cache: "no-store" })
    if (!res.ok) throw new Error("Error al obtener materiales")
    const raw = await res.json()
    // normalizar todos los registros
    materialsData = raw.map(normalizeMaterial)
    // sincronizar disabledMaterials con estado del backend (si tienen campo estado)
    disabledMaterials.clear()
    materialsData.forEach(m => {
      if (!isActiveStatus(m.estado)) {
        disabledMaterials.add(m.code)
      }
    })
    // render con los datos reales
    if (currentView === "table") renderTable()
    else renderCards()
    hydrateUnitSelects()
  } catch (err) {
    console.error(err)
    showAlert("No se pudo cargar la lista de materiales.", "error")
    // igual renderizamos con lo que tengamos (por compatibilidad)
    renderTable()
  }
}

function hydrateUnitSelects() {
  try {
    const units = Array.from(new Set(materialsData.map(m => (m.unit || '').toString().trim()).filter(Boolean)))
    const createSel = document.getElementById('unidad')
    if (createSel) {
      units.forEach(u => {
        const exists = Array.from(createSel.options).some(opt => (opt.value || opt.text).trim() === u)
        if (!exists) {
          const opt = document.createElement('option')
          opt.value = u
          opt.text = u
          createSel.appendChild(opt)
        }
      })
    }
  } catch {}
}

async function fetchMaterialByCode(code) {
  // Intenta buscar en materialsData primero
  const found = materialsData.find(m => m.code === code)
  if (found) return found

  // Si no existe, usamos endpoint buscar para intentar obtenerlo por codigo
  try {
    const res = await fetch(`${API_URL}?accion=buscar&term=${encodeURIComponent(code)}`)
    if (!res.ok) throw new Error("Error buscando material")
    const raw = await res.json()
    if (!Array.isArray(raw) || raw.length === 0) return null
    const m = normalizeMaterial(raw[0])
    // opcional: cachearlo localmente
    materialsData.push(m)
    return m
  } catch (err) {
    console.error(err)
    return null
  }
}

/* =================================================
   RENDER TABLE / CARDS (sin cambios visuales)
   ================================================= */
function renderTable() {
  const start = (currentPage - 1) * itemsPerPage
  const end = start + itemsPerPage
  const paginatedData = materialsData.slice(start, end)

  const tableBody = document.getElementById("tableBody")
  if (!tableBody) return
  tableBody.innerHTML = ""

  paginatedData.forEach((material) => {
    const isDisabled = disabledMaterials.has(material.code)
    const statusClass = isDisabled ? "bg-gray-300 text-gray-600" : "bg-success text-success-foreground"
    const statusText = isDisabled ? "Deshabilitado" : "Disponible"
    const rowClass = isDisabled ? "disabled-row" : ""

    const row = document.createElement("tr")
    row.className = `hover:bg-muted transition-colors ${rowClass}`
    row.dataset.materialCode = material.code

    row.innerHTML = `
            <td class="px-4 py-3 text-sm font-medium">${material.code}</td>
            <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center material-icon-bg" style="background-color: rgba(57, 169, 0, 0.1);">
                        ${icons.package}
                    </div>
                    <div>
                        <p class="font-medium">${material.name}</p>
                        <p class="text-xs text-muted-foreground">${material.category}</p>
                    </div>
                </div>
            </td>
            <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                    <span class="font-medium">${material.stock}</span>
                    <div class="w-16 h-2 bg-muted rounded-full overflow-hidden">
                        <div class="h-full" style="width: ${(material.stock / (Math.max(material.minStock,1) * 2)) * 100}%; background-color: var(--primary);" class="rounded-full"></div>
                    </div>
                    <span class="text-xs text-muted-foreground">Mín ${material.minStock}</span>
                </div>
            </td>
            <td class="px-4 py-3 text-sm">${material.unit}</td>
            <td class="px-4 py-3 text-sm">${material.warehouse}</td>
            <td class="px-4 py-3">
                <span class="inline-flex items-center gap-1 px-3 py-1 ${statusClass} text-xs font-medium rounded-full status-badge ${isDisabled ? "inactive" : "active"}">
                    <span class="w-2 h-2 ${isDisabled ? "bg-red-700" : "bg-success-foreground"} rounded-full opacity-70"></span>
                    ${statusText}
                </span>
            </td>
            <td class="px-4 py-3 text-right">
                <div class="relative">
                    <button class="text-muted-foreground hover:text-foreground transition-colors p-2 rounded-full hover:bg-muted card-menu-btn" onclick="toggleMenu(event)">
                        ${icons.menu}
                    </button>
                    
                    <div class="hidden absolute right-0 top-full mt-2 w-48 bg-card border border-border rounded-lg shadow-lg z-50 dropdown-menu">
                        <button class="w-full px-4 py-2 text-left hover:bg-muted transition-colors flex items-center gap-2 text-foreground rounded-t-lg text-sm" onclick="openDetailsModal('${material.code}', '${material.name}')">
                            ${icons.eye}
                            Ver detalle
                        </button>
                        <button class="w-full px-4 py-2 text-left hover:bg-muted transition-colors flex items-center gap-2 text-foreground text-sm" onclick="openEditModal('${material.code}', '${material.name}')">
                            ${icons.edit}
                            Editar
                        </button>
                        <button class="w-full px-4 py-2 text-left transition-colors flex items-center gap-2 text-sm rounded-b-lg toggle-status-table-btn" data-code="${material.code}" onclick="toggleMaterialStatus('${material.code}', event)" style="color: ${isDisabled ? "#16a34a" : "#dc2626"}; background-color: ${isDisabled ? "#dcfce7" : "#fee2e2"}; border-radius: 0 0 0.5rem 0.5rem;">
                            ${icons.trash}
                            <span class="toggle-text">${isDisabled ? "Habilitar" : "Deshabilitar"}</span>
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
  const start = (currentCardPage - 1) * cardsPerPage
  const end = start + cardsPerPage
  const paginatedData = materialsData.slice(start, end)

  const cardsContainer = document.getElementById("cardsContainer")
  if (!cardsContainer) return
  cardsContainer.innerHTML = ""

  paginatedData.forEach((material) => {
    const isDisabled = disabledMaterials.has(material.code)
    const statusClass = isDisabled ? "bg-gray-300 text-gray-600" : "bg-success text-success-foreground"
    const statusText = isDisabled ? "Deshabilitado" : "Disponible"

    const card = document.createElement("div")
    card.className = `bg-card border border-border rounded-lg p-4 hover:shadow-lg transition-all relative ${
      isDisabled ? "disabled-card" : ""
    }`
    card.dataset.materialCode = material.code

    card.innerHTML = `
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center material-icon-bg" style="background-color: rgba(57, 169, 0, 0.1);">
                        ${icons.package}
                    </div>
                    <div>
                        <p class="font-semibold text-sm">${material.name}</p>
                        <p class="text-xs text-muted-foreground">${material.code}</p>
                    </div>
                </div>
                <div class="relative">
                    <button class="text-muted-foreground hover:text-foreground p-2 hover:bg-muted rounded-full transition-colors card-menu-btn" onclick="toggleCardMenu(event)">
                        ${icons.menu}
                    </button>
                    
                    <div class="hidden absolute right-0 top-full mt-2 w-48 bg-card border border-border rounded-lg shadow-lg z-50 dropdown-menu">
                        <button class="w-full px-4 py-2 text-left hover:bg-muted transition-colors flex items-center gap-2 text-foreground rounded-t-lg text-sm" onclick="event.stopPropagation(); openDetailsModal('${material.code}', '${material.name}')">
                            ${icons.eye}
                            Ver detalle
                        </button>
                        <button class="w-full px-4 py-2 text-left hover:bg-muted transition-colors flex items-center gap-2 text-foreground text-sm" onclick="event.stopPropagation(); openEditModal('${material.code}', '${material.name}')">
                            ${icons.edit}
                            Editar
                        </button>
                        <button class="w-full px-4 py-2 text-left transition-colors flex items-center gap-2 text-sm rounded-b-lg toggle-status-card-btn" data-code="${material.code}" onclick="event.stopPropagation(); toggleMaterialStatus('${material.code}', event)" style="color: ${isDisabled ? "#16a34a" : "#dc2626"}; background-color: ${isDisabled ? "#dcfce7" : "#fee2e2"}; border-radius: 0 0 0.5rem 0.5rem;">
                            ${icons.trash}
                            <span class="toggle-text">${isDisabled ? "Habilitar" : "Deshabilitar"}</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Categoría:</span>
                    <span class="font-medium">${material.category}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Stock:</span>
                    <span class="font-medium">${material.stock} ${material.unit}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Bodega:</span>
                    <span class="font-medium">${material.warehouse}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-muted-foreground">Estado:</span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 ${statusClass} text-xs font-medium rounded-full">
                        <span class="w-2 h-2 ${isDisabled ? "bg-red-700" : "bg-success-foreground"} rounded-full opacity-70"></span>
                        ${statusText}
                    </span>
                </div>
            </div>
        `

    cardsContainer.appendChild(card)
  })

  renderCardPagination()
}

/* ===========================================
   Funciones de paginación y vista (sin cambios)
   =========================================== */
function renderPagination() {
  const totalPages = Math.max(1, Math.ceil(materialsData.length / itemsPerPage))
  const paginationElement = document.getElementById("pagination")
  if (!paginationElement) return
  paginationElement.innerHTML = ""

  const prevButton = document.createElement("button")
  prevButton.textContent = "← Anterior"
  prevButton.disabled = currentPage === 1
  prevButton.className = "pagination-btn"
  prevButton.onclick = () => {
    if (currentPage > 1) {
      currentPage--
      renderTable()
      window.scrollTo(0, 0)
    }
  }
  paginationElement.appendChild(prevButton)

  const spacer1 = document.createElement("span")
  spacer1.textContent = "|"
  spacer1.style.color = "var(--muted-foreground)"
  paginationElement.appendChild(spacer1)

  for (let i = 1; i <= totalPages; i++) {
    const pageButton = document.createElement("button")
    pageButton.textContent = i
    pageButton.className = currentPage === i ? "active" : ""
    pageButton.onclick = () => {
      currentPage = i
      renderTable()
      window.scrollTo(0, 0)
    }
    paginationElement.appendChild(pageButton)
  }

  const spacer2 = document.createElement("span")
  spacer2.textContent = "|"
  spacer2.style.color = "var(--muted-foreground)"
  paginationElement.appendChild(spacer2)

  const nextButton = document.createElement("button")
  nextButton.textContent = "Siguiente →"
  nextButton.disabled = currentPage === totalPages
  nextButton.className = "pagination-btn"
  nextButton.onclick = () => {
    if (currentPage < totalPages) {
      currentPage++
      renderTable()
      window.scrollTo(0, 0)
    }
  }
  paginationElement.appendChild(nextButton)
}

function renderCardPagination() {
  const pagination = document.getElementById("cardPagination")
  if (!pagination) return
  pagination.innerHTML = ""

  const totalPages = Math.max(1, Math.ceil(materialsData.length / cardsPerPage))

  const prevButton = document.createElement("button")
  prevButton.textContent = "← Anterior"
  prevButton.disabled = currentCardPage === 1
  prevButton.className = "pagination-btn"
  prevButton.onclick = () => {
    if (currentCardPage > 1) {
      currentCardPage--
      renderCards()
      window.scrollTo(0, 0)
    }
  }
  pagination.appendChild(prevButton)

  const spacer1 = document.createElement("span")
  spacer1.textContent = "|"
  spacer1.style.color = "var(--muted-foreground)"
  pagination.appendChild(spacer1)

  for (let i = 1; i <= totalPages; i++) {
    const pageButton = document.createElement("button")
    pageButton.textContent = i
    pageButton.className = currentCardPage === i ? "active" : ""
    pageButton.onclick = () => {
      currentCardPage = i
      renderCards()
      window.scrollTo(0, 0)
    }
    pagination.appendChild(pageButton)
  }

  const spacer2 = document.createElement("span")
  spacer2.textContent = "|"
  spacer2.style.color = "var(--muted-foreground)"
  pagination.appendChild(spacer2)

  const nextButton = document.createElement("button")
  nextButton.textContent = "Siguiente →"
  nextButton.disabled = currentCardPage === totalPages
  nextButton.className = "pagination-btn"
  nextButton.onclick = () => {
    if (currentCardPage < totalPages) {
      currentCardPage++
      renderCards()
      window.scrollTo(0, 0)
    }
  }
  pagination.appendChild(nextButton)
}

function switchView(view) {
  currentView = view
  currentPage = 1
  currentCardPage = 1

  const tableView = document.getElementById("tableView")
  const cardView = document.getElementById("cardView")
  const tableViewBtn = document.getElementById("tableViewBtn")
  const cardViewBtn = document.getElementById("cardViewBtn")

  if (view === "table") {
    tableView.classList.remove("hidden")
    cardView.classList.add("hidden")
    tableViewBtn.classList.add("active")
    cardViewBtn.classList.remove("active")
    renderTable()
  } else {
    tableView.classList.add("hidden")
    cardView.classList.remove("hidden")
    tableViewBtn.classList.remove("active")
    cardViewBtn.classList.add("active")
    renderCards()
  }
}

/* ====================================================
   Funciones de apertura / cierre de modales (corregidas)
   - Usan la clase CSS .modal-overlay.active para mostrar
   - Mantienen compatibilidad si el HTML tuviera 'hidden'
   ==================================================== */
function openCreateModal() {
  const modal = document.getElementById("createModal")
  if (!modal) return
  modal.classList.add("active")
}

function closeCreateModal() {
  const modal = document.getElementById("createModal")
  if (!modal) return
  modal.classList.remove("active")
}

async function openDetailsModal(code, name) {
  // Busca el material por code; si no está en cache intenta obtenerlo del backend
  const material = await fetchMaterialByCode(code)
  if (!material) return

  const isDisabled = disabledMaterials.has(material.code)

  document.getElementById("detailName").textContent = material.name
  document.getElementById("detailCode").textContent = material.code
  document.getElementById("detailCategory").textContent = material.category
  document.getElementById("detailType").textContent = material.type
  document.getElementById("detailStock").textContent = `${material.stock} ${material.unit}`
  document.getElementById("detailMinStock").textContent = `${material.minStock} ${material.unit}`
  document.getElementById("detailWarehouse").textContent = material.warehouse

  const statusBadge = document.getElementById("detailStatus")
  statusBadge.textContent = isDisabled ? "Deshabilitado" : "Disponible"
  statusBadge.className = isDisabled
    ? "inline-block px-2 py-1 rounded text-xs font-medium bg-gray-300 text-gray-600"
    : "inline-block px-2 py-1 rounded text-xs font-medium bg-success text-success-foreground"

  const modal = document.getElementById("detailsModal")
  if (!modal) return
  modal.classList.add("active")
}

function closeDetailsModal() {
  const modal = document.getElementById("detailsModal")
  if (!modal) return
  modal.classList.remove("active")
}

async function openEditModal(code, name) {
  const material = await fetchMaterialByCode(code)
  if (!material) return

  // Rellenar campos de edición (nota: el campo editCodigo estaba disabled en la vista)
  document.getElementById("editCodigo").value = material.id_material ?? material.code
  document.getElementById("editNombre").value = material.name
  document.getElementById("editDescripcion").value = material.description || ""
  ensureSelectOption(document.getElementById("editCategoria"), material.category)
  ensureSelectOption(document.getElementById("editTipo"), material.type)
  ensureSelectOption(document.getElementById("editUnidad"), material.unit)
  document.getElementById("editStockActual").value = material.stock
  document.getElementById("editStockMinimo").value = material.minStock
  ensureSelectOption(document.getElementById("editBodega"), material.warehouse)
  // usar la observación extraída, no toda la descripción compuesta
  document.getElementById("editObservacion").value = material.observacion || ""

  const modal = document.getElementById("editModal")
  if (!modal) return
  modal.classList.add("active")
}

function closeEditModal() {
  const modal = document.getElementById("editModal")
  if (!modal) return
  modal.classList.remove("active")
}

/* ====================================================
   Toggle status / switch (sin cambios funcionales)
   Ahora sincroniza con el backend (estado)
   ==================================================== */
async function toggleMaterialStatus(materialCode, event) {
  event.preventDefault()
  event.stopPropagation()

  // buscar material y determinar id
  const material = materialsData.find(m => m.code === materialCode)
  if (!material) {
    showAlert("Material no encontrado para cambiar estado.", "error")
    return
  }

  const newEstado = (material.estado && material.estado.toLowerCase() !== "activo") ? "Activo" : "Inactivo"

  try {
    // En tu controlador 'actualizar' espera id y cuerpo JSON
    const id = material.id_material
    const payload = {
      nombre: material.name,
      descripcion: material.description,
      unidad_medida: material.unit,
      // enviar campos para que el modelo reconstituya clasificacion y descripcion
      categoria: material.category,
      tipo: material.type,
      bodega: material.warehouse,
      stock_minimo: material.minStock,
      codigo_inventario: material.code,
      estado: newEstado
    }

    const res = await fetch(`${API_URL}?accion=actualizar&id=${encodeURIComponent(id)}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    })

    const json = await res.json()
    showAlert(json.message, json.status)

    if (json.status === "success") {
      // actualizar cache local
      material.estado = newEstado
      if (newEstado.toLowerCase() !== "activo") disabledMaterials.add(material.code)
      else disabledMaterials.delete(material.code)
      if (currentView === "table") renderTable()
      else renderCards()
    }

  } catch (err) {
    console.error(err)
    showAlert("No se pudo cambiar el estado del material.", "error")
  }

  // esconder el menú contenedor de la acción (compatibilidad con clases)
  const menu = event.target.closest(".dropdown-menu")
  if (menu) {
    menu.classList.remove("show")
    menu.classList.add("hidden")
  }
}

async function toggleMaterialStatusSwitch(materialCode, switchElement) {
  // Si usas switches, esta función también sincroniza con backend
  const isChecked = switchElement.checked
  const material = materialsData.find(m => m.code === materialCode)
  if (!material) return
  const newEstado = isChecked ? "Activo" : "Inactivo"

  try {
    const id = material.id_material
    const payload = {
      nombre: material.name,
      descripcion: material.description,
      unidad_medida: material.unit,
      categoria: material.category,
      tipo: material.type,
      bodega: material.warehouse,
      stock_minimo: material.minStock,
      codigo_inventario: material.code,
      estado: newEstado
    }

    const res = await fetch(`${API_URL}?accion=actualizar&id=${encodeURIComponent(id)}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    })

    const json = await res.json()
    showAlert(json.message, json.status)

    if (json.status === "success") {
      material.estado = newEstado
      if (newEstado.toLowerCase() !== "activo") disabledMaterials.add(material.code)
      else disabledMaterials.delete(material.code)
      if (currentView === "table") renderTable()
      else renderCards()
    }

  } catch (err) {
    console.error(err)
    showAlert("No se pudo cambiar el estado del material.", "error")
  }
}

/* ====================================================
   Dropdown toggles corregidos (usa .show para CSS)
   - toggleMenu para tablas
   - toggleCardMenu para tarjetas
   ==================================================== */
function toggleMenu(event) {
  event.preventDefault()
  event.stopPropagation()
  const button = event.currentTarget || event.target
  const menu = button.nextElementSibling
  if (!menu) return

  // cerrar otros menús
  document.querySelectorAll(".dropdown-menu").forEach((m) => {
    if (m !== menu) {
      m.classList.remove("show")
      m.classList.add("hidden")
    }
  })

  const isVisible = menu.classList.contains("show")
  if (isVisible) {
    menu.classList.remove("show")
    menu.classList.add("hidden")
  } else {
    menu.classList.add("show")
    menu.classList.remove("hidden")
  }
}

function toggleCardMenu(event) {
  event.preventDefault()
  event.stopPropagation()
  const button = event.currentTarget || event.target
  const menu = button.nextElementSibling
  if (!menu) return

  // cerrar otros menús
  document.querySelectorAll(".dropdown-menu").forEach((m) => {
    if (m !== menu) {
      m.classList.remove("show")
      m.classList.add("hidden")
    }
  })

  const isVisible = menu.classList.contains("show")
  if (isVisible) {
    menu.classList.remove("show")
    menu.classList.add("hidden")
  } else {
    menu.classList.add("show")
    menu.classList.remove("hidden")
  }
}

/* ====================================================
   Comportamiento global: clic fuera cierra dropdowns,
   clic en overlay cierra modal (mantiene animación)
   ==================================================== */
document.addEventListener("click", (e) => {
  // si el click no fue en un botón de menú ni dentro de un dropdown, cerramos todos
  if (!e.target.closest(".card-menu-btn") && !e.target.closest(".dropdown-menu")) {
    document.querySelectorAll(".dropdown-menu").forEach((m) => {
      m.classList.remove("show")
      m.classList.add("hidden")
    })
  }
})

// cerrar modales clicando sobre el overlay (no sobre el contenido)
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("#createModal, #editModal, #detailsModal").forEach((modalOverlay) => {
    modalOverlay.addEventListener("click", (e) => {
      // Solo cerrar si se hace click directamente en el overlay, no en el contenido
      if (e.target === modalOverlay) {
        if (modalOverlay.id === "createModal") closeCreateModal()
        else if (modalOverlay.id === "editModal") closeEditModal()
        else if (modalOverlay.id === "detailsModal") closeDetailsModal()
      }
    })
  })

  // Bind de formularios: Crear y Editar (mantiene onsubmit="event.preventDefault();" en HTML)
  const createForm = document.querySelector("#createModal form")
  if (createForm) {
    createForm.addEventListener("submit", (e) => {
      e.preventDefault()
      createMaterial()
    })
  }

  const editForm = document.querySelector("#editModal form")
  if (editForm) {
    editForm.addEventListener("submit", (e) => {
      e.preventDefault()
      updateMaterial()
    })
  }

  // Bind input buscar  
  const searchInput = document.getElementById("inputBuscar")
  if (searchInput) {
    let searchTimeout = null
    searchInput.addEventListener("input", (e) => {
      const term = e.target.value.trim()
      clearTimeout(searchTimeout)
      searchTimeout = setTimeout(() => {
        if (term === "") loadMaterials()
        else searchMaterials(term)
      }, 250)
    })
  }

  // Render inicial -> cargar desde backend
  loadMaterials().then(() => {
    hydrateUnitSelects()
  })
})

/* ============================
   CRUD desde JS -> BACKEND
   ============================ */
async function createMaterial() {
  try {
    const data = {
      nombre: document.getElementById("nombre").value,
      // campos básicos
      descripcion: document.getElementById("descripcion").value,
      unidad_medida: document.getElementById("unidad").value,
      codigo_inventario: document.getElementById("codigo").value,
      // campos usados por el modelo para componer clasificacion y descripcion
      categoria: document.getElementById("categoria").value,
      tipo: document.getElementById("tipo").value,
      bodega: document.getElementById("bodega").value,
      stock_minimo: parseInt(document.getElementById("stock_minimo").value, 10) || 0,
      stock_actual: parseInt(document.getElementById("stock_actual").value, 10) || 0,
      observacion: document.getElementById("observacion").value,
      // controller/model espera crear() -> puede setear estado por defecto en backend
    }

    const res = await fetch(`${API_URL}?accion=crear`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    })

    const json = await res.json()
    showAlert(json.message, json.status)

    if (json.status === "success") {
      closeCreateModal()
      await loadMaterials()
    }
  } catch (err) {
    console.error(err)
    showAlert("No se pudo crear el material.", "error")
  }
}

async function updateMaterial() {
  try {
    // editCodigo en tu vista está disabled; contiene id o código
    const idOrCode = document.getElementById("editCodigo").value
    // preferir id si es numérico
    const id = isNaN(Number(idOrCode)) ? null : Number(idOrCode)

    // extrayendo valores del modal
    const payload = {
      nombre: document.getElementById("editNombre").value,
      descripcion: document.getElementById("editDescripcion").value,
      unidad_medida: document.getElementById("editUnidad").value,
      // enviar campos de composición esperados por el modelo
      categoria: document.getElementById("editCategoria").value,
      tipo: document.getElementById("editTipo").value,
      bodega: document.getElementById("editBodega").value,
      stock_minimo: parseInt(document.getElementById("editStockMinimo").value, 10) || 0,
      stock_actual: parseInt(document.getElementById("editStockActual").value, 10) || 0,
      observacion: document.getElementById("editObservacion").value,
      codigo_inventario: document.getElementById("editCodigo").value,
      // no forzar estado aquí; el cambio de estado se maneja por acciones específicas
    }

    const targetId = id ?? (function(){
      // intentar localizar por codigo en cache
      const m = materialsData.find(x => x.code === idOrCode)
      return m ? m.id_material : null
    })()

    if (!targetId) {
      showAlert("No se pudo identificar el material a actualizar.", "error")
      return
    }

    const res = await fetch(`${API_URL}?accion=actualizar&id=${encodeURIComponent(targetId)}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    })

    const json = await res.json()
    showAlert(json.message, json.status)

    if (json.status === "success") {
      closeEditModal()
      await loadMaterials()
    }

  } catch (err) {
    console.error(err)
    showAlert("No se pudo actualizar el material.", "error")
  }
}

async function searchMaterials(term) {
  try {
    const res = await fetch(`${API_URL}?accion=buscar&term=${encodeURIComponent(term)}`, { cache: "no-store" })
    if (!res.ok) throw new Error("Error buscando")
    const raw = await res.json()
    materialsData = Array.isArray(raw) ? raw.map(normalizeMaterial) : []
    // actualizar disabled set
    disabledMaterials.clear()
    materialsData.forEach(m => {
      if (!isActiveStatus(m.estado)) disabledMaterials.add(m.code)
    })
    if (currentView === "table") renderTable()
    else renderCards()
    hydrateUnitSelects()
  } catch (err) {
    console.error(err)
    showAlert("Error buscando materiales.", "error")
  }
}

/* ============================
   Util: mostrar alertas Flowbite
   ============================ */
function showAlert(message = "", status = "info", ttl = 3500) {
  const container = document.getElementById("flowbite-alert-container")
  if (!container) {
    // fallback simple
    alert(message)
    return
  }

  const id = "alert-" + Date.now()
  const bg = status === "success" ? "bg-emerald-50 border-emerald-200" :
             status === "error" ? "bg-red-50 border-red-200" :
             "bg-slate-50 border-slate-200"

  const inner = document.createElement("div")
  inner.id = id
  inner.className = `rounded-md p-3 border ${bg} shadow-sm`
  inner.innerHTML = `<div class="text-sm">${message}</div>`

  container.appendChild(inner)

  setTimeout(() => {
    const el = document.getElementById(id)
    if (el) el.remove()
  }, ttl)
}

/* ============================
   Fin archivo (se mantienen exports / funciones globales)
   ============================ */
