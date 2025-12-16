// ========== HELPER FUNCTION: Filter and show/hide empty states ==========
function applyFilterAndUpdateEmptyStates() {
  const searchInput = document.querySelector('input[placeholder="Buscar por nombre..."]')
  const searchTerm = (searchInput?.value ?? '').toLowerCase().trim()
  const filterEstado = document.getElementById('selectFiltroEstado').value
  
  // Get all table rows and grid cards
  const tableRows = document.querySelectorAll('#tableView tbody tr[data-index]')
  const gridCards = document.querySelectorAll('#gridView [data-index]')
  const tableView = document.getElementById('tableView')
  const gridView = document.getElementById('gridView')
  
  let visibleRowCount = 0
  let visibleCardCount = 0
  
  // Filter table rows
  tableRows.forEach(row => {
    const nombre = row.dataset.nombre?.toLowerCase() ?? ''
    const estado = String(row.dataset.estado ?? '')
    
    const matchesSearch = searchTerm === '' || nombre.includes(searchTerm)
    const matchesFilter = filterEstado === '' || estado === filterEstado
    
    if (matchesSearch && matchesFilter) {
      row.classList.remove('hidden')
      visibleRowCount++
    } else {
      row.classList.add('hidden')
    }
  })
  
  // Filter grid cards
  gridCards.forEach(card => {
    const nombre = card.dataset.nombre?.toLowerCase() ?? ''
    const estado = String(card.dataset.estado ?? '')
    
    const matchesSearch = searchTerm === '' || nombre.includes(searchTerm)
    const matchesFilter = filterEstado === '' || estado === filterEstado
    
    if (matchesSearch && matchesFilter) {
      card.classList.remove('hidden')
      visibleCardCount++
    } else {
      card.classList.add('hidden')
    }
  })
  
  // Show/hide empty states and tables
  const emptyState = document.getElementById('emptyStateProgramas')
  const emptySearch = document.getElementById('emptySearchProgramas')
  
  const totalRows = tableRows.length
  const totalCards = gridCards.length
  const totalProgramas = totalRows + totalCards > 0 ? totalRows : totalCards
  
  if (totalProgramas === 0) {
    // No programas in system
    emptyState?.classList.remove('hidden')
    emptySearch?.classList.add('hidden')
    tableView?.classList.add('hidden')
    gridView?.classList.add('hidden')
  } else if (visibleRowCount === 0 && visibleCardCount === 0) {
    // Programas exist but no results for current search/filter
    emptyState?.classList.add('hidden')
    emptySearch?.classList.remove('hidden')
    tableView?.classList.add('hidden')
    gridView?.classList.add('hidden')
  } else {
    // Results found
    emptyState?.classList.add('hidden')
    emptySearch?.classList.add('hidden')
    tableView?.classList.remove('hidden')
    // Nota: gridView será mostrado/ocultado por toggleView()
    if (!gridView?.classList.contains('hidden')) {
      gridView?.classList.remove('hidden')
    }
  }
}

// Function to switch between table and grid view
function toggleView(view) {
  const tableView = document.getElementById("tableView")
  const gridView = document.getElementById("gridView")
  const tableBtn = document.getElementById("viewTableBtn")
  const gridBtn = document.getElementById("viewGridBtn")

  // Close all open menus when changing view
  closeAllMenus()

  if (view === "table") {
    // Show table view
    tableView.classList.remove("hidden")
    gridView.classList.add("hidden")
    tableBtn.classList.add("bg-muted")
    gridBtn.classList.remove("bg-muted")
  } else {
    // Show grid view
    tableView.classList.add("hidden")
    gridView.classList.remove("hidden")
    gridBtn.classList.add("bg-muted")
    tableBtn.classList.remove("bg-muted")
  }
}

// Function to toggle action menu
function toggleActionMenu(index) {
  const menu = document.getElementById("actionMenu" + index)
  const isHidden = menu.classList.contains("hidden")

  // Close all other menus
  closeAllMenus()

  // Toggle current menu
  if (isHidden) {
    menu.classList.remove("hidden")
  }
}

// Function to close all menus
function closeAllMenus() {
  const allMenus = document.querySelectorAll('[id^="actionMenu"]')
  allMenus.forEach((menu) => {
    menu.classList.add("hidden")
  })
}

// Close menus when clicking outside
document.addEventListener("click", (event) => {
  const isMenuButton = event.target.closest('button[onclick^="toggleActionMenu"]')
  const isMenuContent = event.target.closest('[id^="actionMenu"]')

  if (!isMenuButton && !isMenuContent) {
    closeAllMenus()
  }
})

// ========== MODAL FUNCTIONS ==========

// Open edit program modal
function openEditModal(index) {
  const modal = document.getElementById("editProgramModal")
  const row =
    document.querySelector(`tr[data-index="${index}"]`) || document.querySelector(`div[data-index="${index}"]`)

  if (row) {
    document.getElementById("edit_index").value = index
    document.getElementById("edit_codigo").value = row.dataset.codigo
    document.getElementById("edit_nombre").value = row.dataset.nombre
    document.getElementById("edit_descripcion").value = row.dataset.descripcion
    document.getElementById("edit_nivel").value = row.dataset.nivel
    document.getElementById("edit_duracion").value = row.dataset.duracion
  }

  modal.classList.remove("hidden")
  modal.classList.add("flex")
}

// Close edit program modal
function closeEditModal() {
  const modal = document.getElementById("editProgramModal")
  modal.classList.add("hidden")
  modal.classList.remove("flex")
}

// ========== FUNCIÓN PARA ASIGNAR COLORES SEGÚN NIVEL ==========
function getLevelStyles(nivel) {
    const nivelLower = nivel.toLowerCase();
    
    if (nivelLower.includes('técnico') || nivelLower.includes('tecnico')) {
        return {
            bgColor: 'bg-[#007832]',
            textColor: 'text-primary',
            badgeClass: 'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium badge-estado-activo'
        };
    } else if (nivelLower.includes('tecnólogo') || nivelLower.includes('tecnologo')) {
        return {
            bgColor: 'bg-[#00304D]',
            textColor: 'text-info',
            badgeClass: 'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium badge-role-parendiz'
        };
    } else {
        return {
            bgColor: 'bg-muted',
            textColor: 'text-muted-foreground',
            badgeClass: 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400'
        };
    }
}

// Modifica la función openViewModal para usar los estilos según nivel
function openViewModal(index) {
    const modal = document.getElementById("viewProgramModal");
    const row =
        document.querySelector(`tr[data-index="${index}"]`) || document.querySelector(`div[data-index="${index}"]`);

    if (row) {
        document.getElementById("view_name").textContent = row.dataset.nombre;
        document.getElementById("view_code").textContent = row.dataset.codigo;
        document.getElementById("view_description").textContent = row.dataset.descripcion;
        document.getElementById("view_nivel").textContent = row.dataset.nivel;
        document.getElementById("view_duracion").textContent = row.dataset.duracion;

        // Normalize state and display human-friendly badge (Activo / Inactivo)
        const estadoAttrView = String(row.dataset.estado ?? '').trim();
        const estadoHuman = (estadoAttrView === '1' || estadoAttrView === '0')
            ? (estadoAttrView === '1' ? 'Activo' : 'Inactivo')
            : (estadoAttrView.toLowerCase() === 'activo' ? 'Activo' : 'Inactivo');

        const viewEstadoEl = document.getElementById('view_estado');
        viewEstadoEl.textContent = estadoHuman;
        if (estadoHuman === 'Activo') {
            viewEstadoEl.className = 'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium badge-estado-activo';
        } else {
            viewEstadoEl.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400';
        }

        // ========== APLICAR ESTILOS SEGÚN NIVEL ==========
        const nivel = row.dataset.nivel;
        const levelStyles = getLevelStyles(nivel);
        
        // 1. Cambiar fondo del icono circular
        const iconContainer = modal.querySelector('.w-12.h-12');
        if (iconContainer) {
            // Remover clases anteriores de color
            iconContainer.className = iconContainer.className.replace(/bg-\[[^\]]*\]/g, '').replace(/bg-[a-z-]+/g, '');
            // Agregar nueva clase
            iconContainer.classList.add(levelStyles.bgColor);
        }
        
        // 2. Cambiar color del icono
        const icon = modal.querySelector('.fa-graduation-cap');
        if (icon) {
            // Remover clases anteriores de color
            icon.className = icon.className.replace(/text-[a-z-]+/g, '');
            // Agregar nueva clase según nivel
            if (nivel.toLowerCase().includes('técnico') || nivel.toLowerCase().includes('tecnico')) {
                icon.classList.add('text-primary');
            } else {
                icon.classList.add('text-info');
            }
        }
        
        // 3. Cambiar estilo del badge de nivel
        const nivelBadge = document.getElementById('view_nivel');
        if (nivelBadge) {
            nivelBadge.className = levelStyles.badgeClass;
        }
    }

    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

// Close view program details modal
function closeViewModal() {
  const modal = document.getElementById("viewProgramModal")
  modal.classList.add("hidden")
  modal.classList.remove("flex")
}

// Open create program modal
function openCreateModal() {
  const modal = document.getElementById("createProgramModal")
  modal.classList.remove("hidden")
  modal.classList.add("flex")
}

// Close create program modal
function closeCreateModal() {
  const modal = document.getElementById("createProgramModal")
  modal.classList.add("hidden")
  modal.classList.remove("flex")
}

// ************************************** Programs Creation ***********************************************

document.addEventListener("DOMContentLoaded", () => {
  const pathParts = window.location.pathname.split("/")
  const basePath =
    pathParts.slice(0, pathParts.findIndex((p) => p === "views" || p === "programas.php") || -1).join("/") || ""
  const BASE_URL = window.location.origin + basePath + "/"

  console.log("[v0] BASE_URL configured as:", BASE_URL)

  // Create Program Form
  const createForm = document.getElementById("createProgramForm")
  if (createForm) {
    createForm.addEventListener("submit", async (e) => {
      e.preventDefault()

      const data = {
        codigo_programa: document.getElementById("create_codigo").value,
        nombre_programa: document.getElementById("create_nombre").value,
        nivel_programa: document.getElementById("create_nivel").value,
        descripcion_programa: document.getElementById("create_descripcion").value,
        duracion_horas: Number.parseInt(document.getElementById("create_duracion").value.replace(/[^\d]/g, "")),
        estado: 1,
      }

      console.log("[v0] Creating program with data:", data)

      try {
        const response = await fetch(`${BASE_URL}src/controllers/programa_controller.php?accion=crear`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data),
        })

        const result = await response.json()
        console.log("[v0] Create response:", result)

        if (result.mensaje) {
          alert("Programa creado correctamente")
          closeCreateModal()
          location.reload()
        } else {
          alert("Error: " + (result.error || "No se pudo crear"))
        }
      } catch (error) {
        console.error("[v0] Error creating program:", error)
        alert("Error al crear el programa")
      }
    })
  }

  // ************************************** Programs Update ***********************************************

  // Edit Program Form
  const editForm = document.getElementById("editProgramForm")
  if (editForm) {
    editForm.addEventListener("submit", async (e) => {
      e.preventDefault()

      const index = document.getElementById("edit_index").value
      const row =
        document.querySelector(`tr[data-index="${index}"]`) || document.querySelector(`div[data-index="${index}"]`)

      if (!row) {
        alert("Error: No se encontró el programa")
        return
      }

      const idPrograma = row.dataset.idPrograma
      if (!idPrograma) {
        console.error("[v0] No id_programa found in row:", row)
        alert("Error: No se pudo obtener el ID del programa")
        return
      }

      // Normalize level
      const nivelSelect = document.getElementById("edit_nivel").value
      const nivelNormalized = nivelSelect.toLowerCase().includes("técnico") ? "Técnico" : "Tecnólogo"

      // Duration
      const duracionText = document.getElementById("edit_duracion").value
      const duracionHoras = Number.parseInt(duracionText.replace(/[^\d]/g, ""))

      // Actual State from the dataset (supports '1'/'0' or 'Active'/'Inactive')
      const estadoAttrEdit = String(row.dataset.estado ?? '').trim()
      const estadoValue = (estadoAttrEdit === '1' || estadoAttrEdit === '0')
        ? Number(estadoAttrEdit)
        : (estadoAttrEdit.toLowerCase() === 'activo' ? 1 : 0)

      const data = {
        id_programa: idPrograma,
        codigo_programa: document.getElementById("edit_codigo").value,
        nombre_programa: document.getElementById("edit_nombre").value,
        nivel_programa: nivelNormalized,
        descripcion_programa: document.getElementById("edit_descripcion").value,
        duracion_horas: duracionHoras,
        estado: estadoValue
      }

      console.log("[v0] Updating program with data:", data)

      try {
        const response = await fetch(
          `${BASE_URL}src/controllers/programa_controller.php?accion=actualizar&id_programa=${idPrograma}`,
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data),
          },
        )

        const result = await response.json()
        console.log("[v0] Update response:", result)

        if (result.mensaje) {
          alert("Programa actualizado correctamente")
          closeEditModal()
          location.reload()
        } else {
          alert("Error: " + (result.error || "No se pudo actualizar"))
        }
      } catch (error) {
        console.error("[v0] Error updating program:", error)
        alert("Error al actualizar el programa")
      }
    })
  }

  // State toggle buttons (use data-action="toggle-state")
  document.querySelectorAll('[id^="actionMenu"] button[data-action="toggle-estado"]').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const row = e.target.closest('tr') || e.target.closest('div[data-index]')
      const idPrograma = row.dataset.idPrograma

      // Current state: supports '1'/'0' or 'Active'/'Inactive'
      const estadoAttr = String(row.dataset.estado ?? '').trim()
      const estadoActual = (estadoAttr === '1' || estadoAttr === '0')
        ? Number(estadoAttr)
        : (estadoAttr.toLowerCase() === 'activo' ? 1 : 0)
      const nuevoEstado = estadoActual ? 0 : 1

      try {
        const res = await fetch(`${BASE_URL}src/controllers/programa_controller.php?accion=cambiar_estado`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id_programa: idPrograma, estado: nuevoEstado })
        })
        const result = await res.json()
        if(result.mensaje) location.reload()
        else alert(result.error || 'No se pudo cambiar estado')
      } catch(err) {
        console.error(err)
        alert('Error al cambiar estado')
      }
    })
  })

  // State filter (table + grid)
  const selectFiltroEstado = document.getElementById('selectFiltroEstado')
  if (selectFiltroEstado) {
    const applyFilter = () => {
      const val = selectFiltroEstado.value // '' | '1' | '0'

      // Table rows
      document.querySelectorAll('#tableView tbody tr[data-index]').forEach(row => {
        const estado = String(row.dataset.estado ?? '').trim()
        if (val === '') {
          row.style.display = ''
        } else if (estado === val) {
          row.style.display = ''
        } else {
          row.style.display = 'none'
        }
      })

      // Grid cards
      document.querySelectorAll('#gridView [data-index]').forEach(card => {
        const estado = String(card.dataset.estado ?? '').trim()
        if (val === '') {
          card.style.display = ''
        } else if (estado === val) {
          card.style.display = ''
        } else {
          card.style.display = 'none'
        }
      })
    }

    selectFiltroEstado.addEventListener('change', applyFilter)
  }

  // Checkboxes in grid view
  document.querySelectorAll('#gridView input[type="checkbox"]').forEach(chk => {
    chk.addEventListener('change', async (e) => {
      const card = e.target.closest('div[data-index]')
      const idPrograma = card.dataset.idPrograma
      const nuevoEstado = e.target.checked ? 1 : 0

      try {
        const res = await fetch(`${BASE_URL}src/controllers/programa_controller.php?accion=cambiar_estado`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id_programa: idPrograma, estado: nuevoEstado })
        })
        const result = await res.json()
        if(result.mensaje) location.reload()
        else alert(result.error || 'No se pudo cambiar estado')
      } catch(err) {
        console.error(err)
        alert('Error al cambiar estado')
      }
    })
  })

  // ========== SEARCH AND FILTER EVENT LISTENERS ==========
  // Search input listener
  const searchInput = document.querySelector('input[placeholder="Buscar por nombre..."]')
  if (searchInput) {
    searchInput.addEventListener('input', applyFilterAndUpdateEmptyStates)
  }

  // State filter listener (already exists but enhance it)
  const filterSelect = document.getElementById('selectFiltroEstado')
  if (filterSelect) {
    filterSelect.addEventListener('change', applyFilterAndUpdateEmptyStates)
  }

  // Initial call to check empty states on page load
  applyFilterAndUpdateEmptyStates()
})