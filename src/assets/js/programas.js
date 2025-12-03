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