// Function to switch between table and grid view
function toggleView(view) {
    const tableView = document.getElementById('tableView');
    const gridView = document.getElementById('gridView');
    const tableBtn = document.getElementById('viewTableBtn');
    const gridBtn = document.getElementById('viewGridBtn');
    
    // Close all open menus when changing view
    closeAllMenus();
    
    if (view === 'table') {
        // Show table view
        tableView.classList.remove('hidden');
        gridView.classList.add('hidden');
        tableBtn.classList.add('bg-muted');
        gridBtn.classList.remove('bg-muted');
    } else {
        // Show grid view
        tableView.classList.add('hidden');
        gridView.classList.remove('hidden');
        gridBtn.classList.add('bg-muted');
        tableBtn.classList.remove('bg-muted');
    }
}

// Function to toggle action menu
function toggleActionMenu(index) {
    const menu = document.getElementById('actionMenu' + index);
    const isHidden = menu.classList.contains('hidden');
    
    // Close all other menus
    closeAllMenus();
    
    // Toggle current menu
    if (isHidden) {
        menu.classList.remove('hidden');
    }
}

// Function to close all menus
function closeAllMenus() {
    const allMenus = document.querySelectorAll('[id^="actionMenu"]');
    allMenus.forEach(menu => {
        menu.classList.add('hidden');
    });
}

// Close menus when clicking outside
document.addEventListener('click', function(event) {
    const isMenuButton = event.target.closest('button[onclick^="toggleActionMenu"]');
    const isMenuContent = event.target.closest('[id^="actionMenu"]');
    
    if (!isMenuButton && !isMenuContent) {
        closeAllMenus();
    }
});

// ========== MODAL FUNCTIONS ==========

// Open edit program modal
function openEditModal(index) {
    const modal = document.getElementById('editProgramModal');
    const row = document.querySelector(`tr[data-index="${index}"]`) || 
                document.querySelector(`div[data-index="${index}"]`);
    
    if (row) {
        document.getElementById('edit_index').value = index;
        document.getElementById('edit_codigo').value = row.dataset.codigo;
        document.getElementById('edit_nombre').value = row.dataset.nombre;
        document.getElementById('edit_descripcion').value = row.dataset.descripcion;
        document.getElementById('edit_nivel').value = row.dataset.nivel;
        document.getElementById('edit_duracion').value = row.dataset.duracion;
        document.getElementById('edit_instructores').value = row.dataset.instructores;
        document.getElementById('edit_estado').value = row.dataset.estado;
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

// Close edit program modal
function closeEditModal() {
    const modal = document.getElementById('editProgramModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Open view program details modal
function openViewModal(index) {
    const modal = document.getElementById('viewProgramModal');
    const row = document.querySelector(`tr[data-index="${index}"]`) || 
                document.querySelector(`div[data-index="${index}"]`);
    
    if (row) {
        document.getElementById('view_name').textContent = row.dataset.nombre;
        document.getElementById('view_code').textContent = row.dataset.codigo;
        document.getElementById('view_description').textContent = row.dataset.descripcion;
        document.getElementById('view_nivel').textContent = row.dataset.nivel;
        document.getElementById('view_duracion').textContent = row.dataset.duracion;
        document.getElementById('view_estado').textContent = row.dataset.estado;
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

// Close view program details modal
function closeViewModal() {
    const modal = document.getElementById('viewProgramModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Open create program modal
function openCreateModal() {
    const modal = document.getElementById('createProgramModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

// Close create program modal
function closeCreateModal() {
    const modal = document.getElementById('createProgramModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}