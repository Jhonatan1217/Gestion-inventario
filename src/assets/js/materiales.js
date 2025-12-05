const materialsData = [
  {
    code: "MAT-001",
    name: "Cemento gris",
    category: "Construcción",
    type: "Consumible",
    unit: "Bolsa",
    stock: 42,
    minStock: 20,
    warehouse: "Sub-bodega Construcción",
    description: "Cemento gris uso general 50kg",
  },
  {
    code: "MAT-002",
    name: "Arena Fina",
    category: "Construcción",
    type: "Consumible",
    unit: "Bolsa",
    stock: 28,
    minStock: 20,
    warehouse: "Sub-bodega Construcción",
    description: "Arena fina para mezcla",
  },
  {
    code: "MAT-003",
    name: "Taladro Percutor",
    category: "Herramientas",
    type: "Herramienta",
    unit: "Unidad",
    stock: 5,
    minStock: 2,
    warehouse: "Sub-bodega Herramientas",
    description: "Taladro percutor profesional 1100W",
  },
  {
    code: "MAT-004",
    name: "Cable Eléctrico",
    category: "Eléctrico",
    type: "Consumible",
    unit: "Metro",
    stock: 35,
    minStock: 10,
    warehouse: "Sub-bodega Eléctrico",
    description: "Cable eléctrico calibre 12",
  },
  {
    code: "MAT-005",
    name: "Pintura Blanca",
    category: "Pintura",
    type: "Consumible",
    unit: "Galón",
    stock: 15,
    minStock: 5,
    warehouse: "Sub-bodega Pintura",
    description: "Pintura blanca interior 1 galón",
  },
  {
    code: "MAT-006",
    name: "Nivel Láser",
    category: "Herramientas",
    type: "Herramienta",
    unit: "Unidad",
    stock: 8,
    minStock: 2,
    warehouse: "Sub-bodega Herramientas",
    description: "Nivel láser rotativo automático",
  },
  {
    code: "MAT-007",
    name: 'Tubo PVC 4"',
    category: "Sanitario",
    type: "Consumible",
    unit: "Unidad",
    stock: 22,
    minStock: 10,
    warehouse: "Sub-bodega Sanitario",
    description: "Tubo PVC 4 pulgadas clase 315",
  },
  {
    code: "MAT-008",
    name: "Concretera",
    category: "Maquinaria",
    type: "Herramienta",
    unit: "Unidad",
    stock: 3,
    minStock: 1,
    warehouse: "Sub-bodega Maquinaria",
    description: "Concretera eléctrica 150 litros",
  },
  {
    code: "MAT-009",
    name: "Cemento blanco",
    category: "Construcción",
    type: "Consumible",
    unit: "Bolsa",
    stock: 12,
    minStock: 5,
    warehouse: "Sub-bodega Construcción",
    description: "Cemento blanco cementicio 50kg",
  },
  {
    code: "MAT-010",
    name: "Motosierra",
    category: "Herramientas",
    type: "Herramienta",
    unit: "Unidad",
    stock: 4,
    minStock: 1,
    warehouse: "Sub-bodega Herramientas",
    description: "Motosierra gasolina 45cc",
  },
  {
    code: "MAT-011",
    name: "Varilla de acero",
    category: "Construcción",
    type: "Consumible",
    unit: "Metro",
    stock: 156,
    minStock: 50,
    warehouse: "Sub-bodega Construcción",
    description: "Varilla de acero corrugada #4",
  },
  {
    code: "MAT-012",
    name: "Masilla",
    category: "Pintura",
    type: "Consumible",
    unit: "Kg",
    stock: 30,
    minStock: 10,
    warehouse: "Sub-bodega Pintura",
    description: "Masilla para pared 30 kg",
  },
]

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
    '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 0 2-2h4a2 2 0 0 0 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>',
  menu: '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>',
  package:
    '<svg class="w-6 h-6 text-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>',
  clock:
    '<svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
}

function renderTable() {
  const start = (currentPage - 1) * itemsPerPage
  const end = start + itemsPerPage
  const paginatedData = materialsData.slice(start, end)

  const tableBody = document.getElementById("tableBody")
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
            <td class="px-6 py-4 text-sm font-medium">${material.code}</td>
            <td class="px-6 py-4">
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
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <span class="font-medium">${material.stock}</span>
                    <div class="w-16 h-2 bg-muted rounded-full overflow-hidden">
                        <div class="h-full" style="width: ${(material.stock / (material.minStock * 2)) * 100}%; background-color: var(--primary);" class="rounded-full"></div>
                    </div>
                    <span class="text-xs text-muted-foreground">Mín ${material.minStock}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-sm">${material.unit}</td>
            <td class="px-6 py-4 text-sm">${material.warehouse}</td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 px-3 py-1 ${statusClass} text-xs font-medium rounded-full status-badge ${isDisabled ? "inactive" : "active"}">
                    <span class="w-2 h-2 ${isDisabled ? "bg-red-700" : "bg-success-foreground"} rounded-full opacity-70"></span>
                    ${statusText}
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="relative">
                    <button class="text-muted-foreground hover:text-foreground transition-colors p-2 rounded hover:bg-accent card-menu-btn" onclick="toggleMenu(event)">
                        ${icons.menu}
                    </button>
                    
                    <div class="hidden absolute top-full mt-2 w-48 bg-background border border-border rounded-lg shadow-lg z-50 dropdown-menu">
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
  cardsContainer.innerHTML = ""

  paginatedData.forEach((material) => {
    const isDisabled = disabledMaterials.has(material.code)
    const statusClass = isDisabled ? "bg-gray-200 text-gray-700" : "bg-green-100 text-green-700"

    const card = document.createElement("div")
    card.className = "bg-card border border-border rounded-lg p-4 hover:shadow-md transition-shadow"
    card.dataset.materialCode = material.code

    card.innerHTML = `
            <!-- Card header with icon and menu -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0 material-icon-bg">
                        ${icons.package}
                    </div>
                    <div>
                        <h3 class="font-semibold text-foreground">${material.name}</h3>
                        <p class="text-xs text-muted-foreground">${material.code}</p>
                    </div>
                </div>
                
                <!-- Menu button -->
                <div class="relative">
                    <button class="text-muted-foreground hover:text-foreground p-2 hover:bg-muted rounded transition-colors card-menu-btn" onclick="toggleCardMenu(this)">
                        ${icons.menu}
                    </button>
                    
                    <!-- Dropdown menu -->
                    <div class="hidden absolute right-0 mt-2 w-48 bg-background border border-border rounded-lg shadow-lg z-50 dropdown-menu">
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

            <!-- Material description -->
            <p class="text-sm text-muted-foreground mb-4">${material.description || "Sin descripción"}</p>

            <!-- Material information -->
            <div class="space-y-3 mb-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-muted-foreground">Bodega</span>
                    <span class="text-sm font-medium text-foreground">${material.warehouse}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-muted-foreground">Categoría</span>
                    <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                        ${material.category}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-muted-foreground">Stock / Unidad</span>
                    <div class="flex items-center gap-2 text-sm font-medium text-foreground">
                        ${icons.clock}
                        ${material.stock} ${material.unit}
                    </div>
                </div>
            </div>

            <!-- Stock progress bar -->
            <div class="mb-4">
                <p class="text-xs text-muted-foreground mb-1">Stock: Mín ${material.minStock}</p>
                <div class="w-full h-2 bg-muted rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full" style="width: ${Math.min((material.stock / (material.minStock * 2)) * 100, 100)}%"></div>
                </div>
            </div>

            <!-- Status and toggle -->
            <div class="flex items-center justify-between pt-4 border-t border-border">
                <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full status-badge ${statusClass}">
                    <span class="w-2 h-2 rounded-full opacity-70" style="background-color: currentColor;"></span>
                    ${isDisabled ? "Inactivo" : "Activo"}
                </span>
                
                <!-- Toggle switch -->
                <label class="relative inline-flex items-center cursor-pointer" onclick="event.stopPropagation()">
                    <input 
                        type="checkbox" 
                        class="sr-only peer" 
                        ${isDisabled ? "" : "checked"}
                        onchange="event.stopPropagation(); toggleMaterialStatusSwitch('${material.code}', this)"
                    >
                    <div class="w-11 h-6 bg-muted peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>
        `

    cardsContainer.appendChild(card)
  })

  renderCardPagination()
}

function toggleCardMenu(button) {
  const menu = button.nextElementSibling
  const isHidden = menu.classList.contains("hidden")

  document.querySelectorAll(".dropdown-menu").forEach((m) => {
    if (m !== menu) {
      m.classList.add("hidden")
    }
  })

  if (isHidden) {
    menu.classList.remove("hidden")
  } else {
    menu.classList.add("hidden")
  }
}

function toggleMaterialStatusSwitch(materialCode, switchElement) {
  const isChecked = switchElement.checked
  if (isChecked) {
    disabledMaterials.delete(materialCode)
  } else {
    disabledMaterials.add(materialCode)
  }
  renderCards()
}

function renderPagination() {
  const totalPages = Math.ceil(materialsData.length / itemsPerPage)
  const paginationElement = document.getElementById("pagination")
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
  pagination.innerHTML = ""

  const totalPages = Math.ceil(materialsData.length / cardsPerPage)

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

function openCreateModal() {
  document.getElementById("createModal").classList.remove("hidden")
}

function closeCreateModal() {
  document.getElementById("createModal").classList.add("hidden")
}

function openDetailsModal(materialCode, materialName) {
  const material = materialsData.find((m) => m.code === materialCode)
  if (material) {
    document.getElementById("detailName").textContent = material.name
    document.getElementById("detailCode").textContent = material.code
    document.getElementById("detailCategory").textContent = material.category
    document.getElementById("detailType").textContent = material.type
    document.getElementById("detailStock").textContent = `${material.stock} ${material.unit}`
    document.getElementById("detailMinStock").textContent = `${material.minStock} ${material.unit}`
    document.getElementById("detailWarehouse").textContent = material.warehouse
  }
  document.getElementById("detailsModal").classList.remove("hidden")
}

function closeDetailsModal() {
  document.getElementById("detailsModal").classList.add("hidden")
}

function openEditModal(materialCode, materialName) {
  const material = materialsData.find((m) => m.code === materialCode)
  if (material) {
    document.getElementById("editCodigo").value = material.code
    document.getElementById("editNombre").value = material.name
    document.getElementById("editDescripcion").value = material.description
    document.getElementById("editCategoria").value = material.category
    document.getElementById("editTipo").value = material.type
    document.getElementById("editUnidad").value = material.unit
    document.getElementById("editStock").value = material.stock
    document.getElementById("editStockMin").value = material.minStock
    document.getElementById("editBodega").value = material.warehouse
  }
  document.getElementById("editModal").classList.remove("hidden")
}

function closeEditModal() {
  document.getElementById("editModal").classList.add("hidden")
}

function toggleMaterialStatus(materialCode, event) {
  event.preventDefault()
  event.stopPropagation()

  if (disabledMaterials.has(materialCode)) {
    disabledMaterials.delete(materialCode)
  } else {
    disabledMaterials.add(materialCode)
  }

  const menu = event.target.closest(".dropdown-menu")
  if (menu) {
    menu.classList.add("hidden")
  }

  if (currentView === "table") {
    renderTable()
  } else {
    renderCards()
  }
}

function toggleMenu(event) {
  event.preventDefault()
  const button = event.currentTarget
  const menu = button.nextElementSibling
  const isHidden = menu.classList.contains("hidden")

  document.querySelectorAll(".dropdown-menu").forEach((m) => {
    if (m !== menu) {
      m.classList.add("hidden")
    }
  })

  if (isHidden) {
    menu.classList.remove("hidden")
  } else {
    menu.classList.add("hidden")
  }
}

// Initialize
document.addEventListener("DOMContentLoaded", () => {
  renderTable()
})
