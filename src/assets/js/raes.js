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

  // Decodificar valores en caso de que vengan codificados desde el HTML
  try { descripcion = decodeURIComponent(descripcion) } catch (e) {}
  try { programa = decodeURIComponent(programa) } catch (e) {}

  // Actualizar contenido del modal
  document.getElementById("detailsRaeCode").textContent = "RAE #" + id
  document.getElementById("detailsRaeDescription").textContent = descripcion
  document.getElementById("detailsPrograma").textContent = programa

  // Actualizar badge de estado
  const statusBadge = document.getElementById("detailsRaeStatus")
  if (estado.toLowerCase() === "activo") {
    statusBadge.className =
      "inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success"
    statusBadge.textContent = "Activo"
  } else {
    statusBadge.className =
      "inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#ef444426] text-destructive"
    statusBadge.textContent = "Inactivo"
  }

  // Mostrar modal
  document.getElementById("detailsModal").classList.remove("hidden")
}

function closeDetailsModal() {
  document.getElementById("detailsModal").classList.add("hidden")
}

function openEditModal(id, descripcion, programa, codigo, programId) {
  // Cerrar todos los menús desplegables
  const allMenus = document.querySelectorAll('[id^="actionMenu"]')
  allMenus.forEach((menu) => menu.classList.add("hidden"))

  // Decodificar valores en caso de que vengan codificados desde el HTML
  try { descripcion = decodeURIComponent(descripcion) } catch (e) {}
  try { programa = decodeURIComponent(programa) } catch (e) {}
  try { codigo = decodeURIComponent(codigo) } catch (e) {}

  // Actualizar contenido del formulario
  const idEl = document.getElementById('editRaeId')
  const codigoEl = document.getElementById('editRaeCodigo')
  if (idEl) idEl.value = id || ''
  if (codigoEl) codigoEl.value = codigo || ''

  document.getElementById("editRaeDescription").value = descripcion
  const programSelect = document.getElementById('editRaeProgram')
  if (programSelect) {
    // Preferir programId si viene (valor numérico que coincide con option.value)
    if (programId !== undefined && programId !== null && programId !== '') {
      programSelect.value = programId
    } else {
      // Intentar seleccionar por texto (nombre del programa)
      const name = programa || ''
      let matched = false
      for (const opt of Array.from(programSelect.options)) {
        if (opt.textContent.trim() === decodeURIComponent(name).trim()) {
          programSelect.value = opt.value
          matched = true
          break
        }
      }
      if (!matched) {
        // dejar vacío
        programSelect.value = ''
      }
    }
  }

  // Mostrar modal
  document.getElementById("editModal").classList.remove("hidden")
}

function closeEditModal() {
  document.getElementById("editModal").classList.add("hidden")
}

function openCreateModal() {
  // Limpiar campos del formulario
  document.getElementById("createRaeCodigo").value = ""
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

// -------------------------
// Carga dinámica de RAEs
// -------------------------

function _getField(obj, ...names) {
  for (const n of names) {
    if (obj[n] !== undefined && obj[n] !== null) return obj[n]
  }
  return ''
}

async function loadRaes() {
  try {
    const res = await fetch(`${BASE_URL}src/controllers/rae_controller.php?accion=listar`, {
      headers: { 'Accept': 'application/json' }
    })
    if (!res.ok) throw new Error('Error al obtener RAEs')
    const data = await res.json()
    renderTable(data)
    renderGrid(data)
  } catch (err) {
    console.error(err)
  }
}

// Cargar programas activos para los selects (creación/edición)
async function loadPrograms() {
  try {
    const res = await fetch(`${BASE_URL}src/controllers/programa_controller.php?accion=listar`, { headers: { 'Accept': 'application/json' } })
    if (!res.ok) throw new Error('Error al obtener programas')
    const data = await res.json()
    // Filtrar activos (campo puede ser 'estado')
    const activos = data.filter(p => (p.estado || '').toString().toLowerCase() === 'activo')

    // Guardar mapa id->nombre para uso en renderizado de RAEs
    window._programMap = {}
    activos.forEach(p => {
      const id = p.id_programa ?? p.id ?? p.id_programa
      const nombre = p.nombre_programa ?? p.nombre ?? p.codigo_programa ?? ''
      if (id != null) window._programMap[id] = nombre
    })

    // Helper para poblar un select
    function populate(selectId, items) {
      const sel = document.getElementById(selectId)
      if (!sel) return
      sel.innerHTML = '<option value="">Selecciona un programa</option>'
      items.forEach(p => {
        const id = p.id_programa ?? p.id ?? p.id_programa
        const nombre = p.nombre_programa ?? p.nombre ?? p.codigo_programa ?? ''
        const opt = document.createElement('option')
        opt.value = id
        opt.textContent = nombre
        sel.appendChild(opt)
      })
    }

    populate('createRaeProgram', activos)
    populate('editRaeProgram', activos)
  } catch (err) {
    console.error(err)
  }
}

// Crear RAE: leer inputs y enviar a la API
async function createRae() {
  const codigo = (document.getElementById('createRaeCodigo')?.value || '').trim()
  const id_programa = (document.getElementById('createRaeProgram')?.value || '').trim()
  const descripcion = (document.getElementById('createRaeDescription')?.value || '').trim()

  if (!codigo) return alert('El código del RAE es obligatorio')
  if (!id_programa) return alert('Selecciona un programa de formación')
  if (!descripcion) return alert('La descripción del RAE es obligatoria')

  const payload = {
    codigo_rae: codigo,
    descripcion_rae: descripcion,
    id_programa: parseInt(id_programa, 10),
    estado: 'Activo'
  }

  try {
    const res = await fetch(`${BASE_URL}src/controllers/rae_controller.php?accion=crear`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(payload)
    })

    const data = await res.json()
    if (!res.ok || data.error) {
      console.error(data)
      return alert(data.error || data || 'Error al crear RAE')
    }

    // Éxito
    alert(data.message || data.mensaje || 'RAE creada correctamente')
    closeCreateModal()
    loadRaes()
  } catch (err) {
    console.error(err)
    alert('Error en la solicitud')
  }
}

// Actualizar RAE (edición)
async function updateRae() {
  const id = (document.getElementById('editRaeId')?.value || '').trim()
  const descripcion = (document.getElementById('editRaeDescription')?.value || '').trim()
  const codigo = (document.getElementById('editRaeCodigo')?.value || '').trim()
  const id_programa = (document.getElementById('editRaeProgram')?.value || '').trim()

  if (!id) return alert('ID del RAE faltante')
  if (!descripcion) return alert('La descripción del RAE es obligatoria')

  const payload = {
    id_rae: parseInt(id, 10),
    descripcion_rae: descripcion
  }
  if (codigo) payload.codigo_rae = codigo
  if (id_programa) payload.id_programa = parseInt(id_programa, 10)

  try {
    const res = await fetch(`${BASE_URL}src/controllers/rae_controller.php?accion=actualizar`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(payload)
    })

    const data = await res.json()
    if (!res.ok || data.error) {
      console.error(data)
      return alert(data.error || data || 'Error al actualizar RAE')
    }

    alert(data.mensaje || data.message || 'RAE actualizado correctamente')
    closeEditModal()
    loadRaes()
  } catch (err) {
    console.error(err)
    alert('Error en la solicitud')
  }
}

// Cambiar estado de un RAE (activar / inactivar)
async function changeRaeEstado(id, estado) {
  if (!id) return alert('ID del RAE faltante')

  const payload = {
    id_rae: parseInt(id, 10),
    estado: estado
  }

  try {
    const res = await fetch(`${BASE_URL}src/controllers/rae_controller.php?accion=cambiar_estado`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(payload)
    })

    const data = await res.json()
    if (!res.ok || data.error) {
      console.error(data)
      return alert(data.error || data || 'Error al cambiar estado')
    }

    alert(data.mensaje || data.message || 'Estado actualizado correctamente')
    // Refrescar listado para actualizar UI
    loadRaes()
  } catch (err) {
    console.error(err)
    alert('Error en la solicitud')
  }
}

function renderTable(items) {
  const tbody = document.getElementById('raesTableBody')
  if (!tbody) return
  tbody.innerHTML = ''

  items.forEach((r, idx) => {
    const id = _getField(r, 'id', 'id_rae')
    const codigo = _getField(r, 'codigo_rae', 'codigo')
    const descripcion = _getField(r, 'descripcion', 'descripcion_rae')
    let programa = _getField(r, 'programa', 'nombre_programa') || ''
    // id del programa (si viene)
    const pid = _getField(r, 'id_programa', 'id_programa', 'id_programa')
    // si no viene el nombre, intentar mapear por id usando _programMap
    if (!programa) {
      if (pid != null && window._programMap && window._programMap[pid]) {
        programa = window._programMap[pid]
      } else if (pid != null) {
        programa = pid
      }
    }
    const estado = _getField(r, 'estado') || ''
    const ed = encodeURIComponent(descripcion)
    const ep = encodeURIComponent(programa)

    const tr = document.createElement('tr')
    tr.className = 'border-b border-border hover:bg-muted transition-colors'
    tr.innerHTML = `
      <td class="py-4 px-4 text-sm font-medium text-foreground">${codigo || id}</td>
      <td class="py-4 px-4">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-muted rounded-full flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-primary w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
            </svg>
          </div>
          <span class="text-sm text-foreground">${descripcion}</span>
        </div>
      </td>
      <td class="py-4 px-4">
        <div class="flex items-center gap-2 text-sm text-muted-foreground">
          <i class="fas fa-graduation-cap"></i>
          <span>${programa}</span>
        </div>
      </td>
      <td class="py-4 px-4">
        ${estado.toLowerCase() === 'activo' ?
          `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success">${estado}</span>` :
          `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400">${estado}</span>`}
      </td>
      <td class="py-4 px-4">
        <div class="relative">
          <button onclick="toggleActionMenu(${idx})" class="text-muted-foreground hover:text-foreground transition-colors p-2 hover:bg-muted rounded">
            <i class="fas fa-ellipsis-h"></i>
          </button>
          <div id="actionMenu${idx}" class="hidden absolute right-0 mt-2 w-48 bg-card rounded-lg shadow-lg border border-border z-50">
            <button onclick="openEditModal('${id}', '${ed}', '${ep}', '${codigo ? encodeURIComponent(codigo) : ''}', '${pid ?? ''}')" class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground rounded-t-lg transition-colors">
              <i class="far fa-edit text-muted-foreground"></i>
              Editar
            </button>
            <button onclick="openDetailsModal('${id}', '${ed}', '${ep}', '${estado}')" class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground transition-colors">
              <i class="far fa-eye text-muted-foreground"></i>
              Ver detalles
            </button>
            <button onclick="changeRaeEstado('${id}', '${estado.toLowerCase() === 'activo' ? 'Inactivo' : 'Activo'}')" class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground rounded-b-lg transition-colors">
              <i class="fas fa-ban text-muted-foreground"></i>
              ${estado.toLowerCase() === 'activo' ? 'Deshabilitar' : 'Habilitar'}
            </button>
          </div>
        </div>
      </td>
    `

    tbody.appendChild(tr)
  })
}

function renderGrid(items) {
  const container = document.getElementById('gridViewContainer')
  if (!container) return
  container.innerHTML = ''

  items.forEach((r, idx) => {
    const id = _getField(r, 'id', 'id_rae')
    const codigo = _getField(r, 'codigo_rae', 'codigo')
    const descripcion = _getField(r, 'descripcion', 'descripcion_rae')
    let programa = _getField(r, 'programa', 'nombre_programa') || ''
    const ed = encodeURIComponent(descripcion)
    const ep = encodeURIComponent(programa)
    if (!programa) {
      const pid = _getField(r, 'id_programa', 'id_programa', 'id_programa')
      if (pid != null && window._programMap && window._programMap[pid]) {
        programa = window._programMap[pid]
      } else if (pid != null) {
        programa = pid
      }
    }
    const estado = _getField(r, 'estado') || ''

    const card = document.createElement('div')
    card.className = 'bg-card border border-border rounded-2xl p-6 hover:shadow-lg transition-all'
    card.innerHTML = `
      <div class="flex items-start gap-4 mb-4">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0" style="background-color: color-mix(in srgb, var(--primary) 18%, transparent);">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-primary w-8 h-8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
          </svg>
        </div>
        <div class="flex-1"><h3 class="text-lg font-semibold text-foreground leading-tight">${descripcion}</h3></div>
        <button onclick="openEditModal('${id}', '${ed}', '${ep}', '${codigo ? encodeURIComponent(codigo) : ''}', '${_getField(r, 'id_programa') ?? ''}')" class="text-muted-foreground hover:text-foreground transition flex-shrink-0" title="Editar RAE">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
          </svg>
        </button>
      </div>
      <div class="border-t border-border mb-4"></div>
      <div class="flex items-center justify-between">
        <div class="flex-1">
          <div class="mb-3">
            ${estado.toLowerCase() === 'activo' ?
              `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-success" style="background-color: color-mix(in srgb, var(--success) 18%, transparent);">Activo</span>` :
              `<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-gray-400" style="background-color: color-mix(in srgb, #9ca3af 18%, transparent);">Inactivo</span>`}
          </div>
          <div class="flex items-center gap-2 text-foreground">
            <i class="fas fa-graduation-cap text-sm"></i>
            <span class="text-sm">${programa}</span>
          </div>
        </div>
        <div class="flex-shrink-0">
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" class="sr-only peer" ${estado.toLowerCase() === 'activo' ? 'checked' : ''}>
            <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-primary transition-colors peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
          </label>
        </div>
      </div>
    `

    container.appendChild(card)

    // Conectar el switch de la tarjeta para cambiar estado
    const cb = card.querySelector('input[type="checkbox"]')
    if (cb) {
      cb.addEventListener('change', function () {
        const newState = this.checked ? 'Activo' : 'Inactivo'
        changeRaeEstado(id, newState)
      })
    }
  })
}

// Al cargar la página, obtener y renderizar RAEs
document.addEventListener('DOMContentLoaded', async () => {
  // Vista por defecto: tabla
  try { toggleView('table') } catch (e) { }
  
  await loadPrograms()
  await loadRaes()

  // Attach create handler
  const createBtn = document.getElementById('createRaeSubmit')
  if (createBtn) createBtn.addEventListener('click', (e) => { e.preventDefault(); createRae() })
  // Attach edit handler
  const editBtn = document.getElementById('editRaeSubmit')
  if (editBtn) editBtn.addEventListener('click', (e) => { e.preventDefault(); updateRae() })
})