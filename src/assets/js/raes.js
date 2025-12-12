// Función para alternar entre vista de tabla y vista de grid
function toggleView(view) {
  const tableView = document.getElementById("tableView")
  const gridView = document.getElementById("gridView")
  const tableBtn = document.getElementById("viewTableBtn")
  const gridBtn = document.getElementById("viewGridBtn")

  // Cerrar todos los menús desplegables al cambiar de vista
  const allMenus = document.querySelectorAll('[id^="actionMenu"]')
  allMenus.forEach((menu) => menu.classList.add("hidden"))

  if (view === "table") {
    // Mostrar vista de tabla
    tableView.classList.remove("hidden")
    gridView.classList.add("hidden")
    tableBtn.classList.add("bg-muted")
    gridBtn.classList.remove("bg-muted")
  } else {
    // Mostrar vista de grid
    tableView.classList.add("hidden")
    gridView.classList.remove("hidden")
    tableBtn.classList.remove("bg-muted")
    gridBtn.classList.add("bg-muted")
  }
}

// Función para mostrar/ocultar el menú de acciones
function toggleActionMenu(id) {
  const menu = document.getElementById("actionMenu" + id)
  const allMenus = document.querySelectorAll('[id^="actionMenu"]')

  // Cerrar todos los demás menús
  allMenus.forEach((m) => {
    if (m.id !== "actionMenu" + id) {
      m.classList.add("hidden")
    }
  })

  // Toggle del menú actual
  menu.classList.toggle("hidden")
}

// Cerrar menús al hacer clic fuera de ellos
document.addEventListener("click", (event) => {
  const isMenuButton = event.target.closest('[onclick^="toggleActionMenu"]')
  const isInsideMenu = event.target.closest('[id^="actionMenu"]')

  if (!isMenuButton && !isInsideMenu) {
    const allMenus = document.querySelectorAll('[id^="actionMenu"]')
    allMenus.forEach((menu) => menu.classList.add("hidden"))
  }
})

function openDetailsModal(id, descripcion, programa, estado) {
  // Cerrar todos los menús desplegables
  const allMenus = document.querySelectorAll('[id^="actionMenu"]')
  allMenus.forEach((menu) => menu.classList.add("hidden"))

  // Actualizar contenido del modal
  document.getElementById("detailsRaeId").textContent = "RAE " + id
  document.getElementById("detailsRaeDescription").textContent = descripcion
  document.getElementById("detailsPrograma").textContent = programa

  // Actualizar badge de estado
  const statusBadge = document.getElementById("detailsRaeStatus")
  if (estado.toLowerCase() === "activo") {
    statusBadge.className =
      "inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success mt-1"
    statusBadge.textContent = "Activo"
  } else {
    statusBadge.className =
      "inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#ef444426] text-destructive mt-1"
    statusBadge.textContent = "Inactivo"
  }

  // Mostrar modal
  document.getElementById("detailsModal").classList.remove("hidden")
}

function closeDetailsModal() {
  document.getElementById("detailsModal").classList.add("hidden")
}

function openEditModal(id, descripcion, programa) {
  // Cerrar todos los menús desplegables
  const allMenus = document.querySelectorAll('[id^="actionMenu"]')
  allMenus.forEach((menu) => menu.classList.add("hidden"))

  // Actualizar contenido del formulario
  document.getElementById("editRaeDescription").value = descripcion
  document.getElementById("editRaeProgram").value = programa

  // Mostrar modal
  document.getElementById("editModal").classList.remove("hidden")
}

function closeEditModal() {
  document.getElementById("editModal").classList.add("hidden")
}

function openCreateModal() {
  // Limpiar campos del formulario
  document.getElementById("createRaeProgram").value = ""
  document.getElementById("createRaeDescription").value = ""

  // Mostrar modal
  document.getElementById("createModal").classList.remove("hidden")
}

function closeCreateModal() {
  document.getElementById("createModal").classList.add("hidden")
}

// Cerrar modales al hacer clic fuera de ellos
document.getElementById("detailsModal").addEventListener("click", function (event) {
  if (event.target === this) {
    closeDetailsModal()
  }
})

document.getElementById("editModal").addEventListener("click", function (event) {
  if (event.target === this) {
    closeEditModal()
  }
})

document.getElementById("createModal").addEventListener("click", function (event) {
  if (event.target === this) {
    closeCreateModal()
  }
})

// Cerrar modales con la tecla Escape
document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    closeDetailsModal()
    closeEditModal()
    closeCreateModal()
  }
})