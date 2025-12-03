// Función para cambiar entre vista de tabla y grid
function toggleView(view) {
    const tableView = document.getElementById('tableView');
    const gridView = document.getElementById('gridView');
    const tableBtn = document.getElementById('viewTableBtn');
    const gridBtn = document.getElementById('viewGridBtn');
    
    // Cerrar todos los menús abiertos al cambiar de vista
    closeAllMenus();
    
    if (view === 'table') {
        // Mostrar vista de tabla
        tableView.classList.remove('hidden');
        gridView.classList.add('hidden');
        tableBtn.classList.add('bg-muted');
        gridBtn.classList.remove('bg-muted');
    } else {
        // Mostrar vista de grid
        tableView.classList.add('hidden');
        gridView.classList.remove('hidden');
        gridBtn.classList.add('bg-muted');
        tableBtn.classList.remove('bg-muted');
    }
}

// Función para toggle del menú de acciones
function toggleActionMenu(index) {
    const menu = document.getElementById('actionMenu' + index);
    const isHidden = menu.classList.contains('hidden');
    
    // Cerrar todos los demás menús
    closeAllMenus();
    
    // Toggle del menú actual
    if (isHidden) {
        menu.classList.remove('hidden');
    }
}

// Función para cerrar todos los menús
function closeAllMenus() {
    const allMenus = document.querySelectorAll('[id^="actionMenu"]');
    allMenus.forEach(menu => {
        menu.classList.add('hidden');
    });
}

// Cerrar menús al hacer click fuera
document.addEventListener('click', function(event) {
    const isMenuButton = event.target.closest('button[onclick^="toggleActionMenu"]');
    const isMenuContent = event.target.closest('[id^="actionMenu"]');
    
    if (!isMenuButton && !isMenuContent) {
        closeAllMenus();
    }
});

// Abrir modal de edición y prellenar datos
function openEditModal(index) {
    // Cerrar menús abiertos
    closeAllMenus();
    const modal = document.getElementById('editProgramModal');
    const el = document.querySelector(`[data-index="${index}"]`);
    if (!el) return;

    // Leer datos del elemento (dataset)
    const data = el.dataset;

    document.getElementById('edit_index').value = index;
    document.getElementById('edit_codigo').value = data.codigo || '';
    document.getElementById('edit_nivel').value = data.nivel || '';
    document.getElementById('edit_nombre').value = data.nombre || '';
    document.getElementById('edit_descripcion').value = data.descripcion || '';
    document.getElementById('edit_duracion').value = data.duracion || '';
    document.getElementById('edit_instructores').value = data.instructores || '';
    document.getElementById('edit_estado').value = data.estado || '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('edit_codigo').focus();
}

function closeEditModal() {
    const modal = document.getElementById('editProgramModal');
    if (!modal) return;
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

// Abrir modal de ver detalles
function openViewModal(index) {
    closeAllMenus();
    const modal = document.getElementById('viewProgramModal');
    const el = document.querySelector(`[data-index="${index}"]`);
    if (!el || !modal) return;

    const data = el.dataset;
    document.getElementById('view_name').textContent = data.nombre || '';
    document.getElementById('view_code').textContent = data.codigo || '';
    document.getElementById('view_description').textContent = data.descripcion || '';
    document.getElementById('view_nivel').textContent = data.nivel || '';
    document.getElementById('view_duracion').textContent = data.duracion || '';
    // instructor fijo por ahora
    document.getElementById('view_instructor').textContent = 'Juan Guillermo Crespo';
    document.getElementById('view_estado').textContent = data.estado || '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeViewModal() {
    const modal = document.getElementById('viewProgramModal');
    if (!modal) return;
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

// Manejador mínimo de submit: deshabilitado por ahora (solo evita envío)
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editProgramForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        // Guardado deshabilitado — los inputs están listos para cuando implementemos la funcionalidad.
        console.log('Guardado deshabilitado: funcionalidad pendiente.');
        // Opcionalmente cerrar modal para flujo de UI, sin aplicar cambios.
        closeEditModal();
    });
});

// Funciones para abrir/cerrar modal Crear Programa (UI only)
function openCreateModal() {
    closeAllMenus();
    const modal = document.getElementById('createProgramModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    // focus en primer input
    const input = document.getElementById('create_codigo');
    if (input) input.focus();
}

function closeCreateModal() {
    const modal = document.getElementById('createProgramModal');
    if (!modal) return;
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

// Manejador mínimo del formulario Crear (previene envío)
document.addEventListener('DOMContentLoaded', function() {
    const createForm = document.getElementById('createProgramForm');
    if (!createForm) return;
    createForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Crear programa: guardado deshabilitado (UI only)');
        closeCreateModal();
    });
});