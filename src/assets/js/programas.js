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