// Variables globales
let obras = [];
let nextId = 0;

// ==============================
// DETECCIÓN DEL SIDEBAR
// ==============================

// Función para verificar y aplicar estado del sidebar
function setupSidebarDetection() {
    // Verificar estado inicial
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('coll') === '1') {
        document.body.classList.add('sidebar-collapsed');
    }
    
    // Observar cambios futuros
    document.addEventListener('click', function(e) {
        if (e.target.closest('a[href*="coll="]')) {
            setTimeout(() => {
                const newParams = new URLSearchParams(window.location.search);
                if (newParams.get('coll') === '1') {
                    document.body.classList.add('sidebar-collapsed');
                } else {
                    document.body.classList.remove('sidebar-collapsed');
                }
            }, 50);
        }
    });
}

// Cargar datos al iniciar
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar datos desde PHP (si están embebidos)
    if (typeof window.obrasData !== 'undefined') {
        obras = window.obrasData;
        nextId = obras.length > 0 ? Math.max(...obras.map(o => o.id)) + 1 : 1;
    }
    
    updateEstadisticas();
    renderObras(obras);

    setupSidebarDetection();
});

// Actualizar estadísticas
function updateEstadisticas() {
    const total = obras.length;
    const activas = obras.filter(o => o.estado == 1).length;
    const finalizadas = obras.filter(o => o.estado == 0).length;

    document.getElementById('totalObras').textContent = total;
    document.getElementById('obrasActivas').textContent = activas;
    document.getElementById('obrasFinalizadas').textContent = finalizadas;
}

// Función renderObras
function renderObras(obrasData) {
    const container = document.getElementById('obrasContainer');

    if (obrasData.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-folder-open text-4xl mb-3"></i>
                <p>No se encontraron obras</p>
            </div>
        `;
        return;
    }

    container.innerHTML = obrasData.map(obra => `
        <div class="border border-l-4 ${obra.estado == 1 ? 'border-l-[#007832]' : 'border-l-[#64748b]'} rounded-lg p-5 mb-4 hover:shadow-md transition-shadow bg-white">
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">${obra.nombre_actividad}</h3>
                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">${obra.descripcion}</p>
                    
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-3">
                        <div>
                            <p class="flex text-sm font-medium js-name gap-2 items-center pb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-puzzle-icon lucide-puzzle"><path d="M15.39 4.39a1 1 0 0 0 1.68-.474 2.5 2.5 0 1 1 3.014 3.015 1 1 0 0 0-.474 1.68l1.683 1.682a2.414 2.414 0 0 1 0 3.414L19.61 15.39a1 1 0 0 1-1.68-.474 2.5 2.5 0 1 0-3.014 3.015 1 1 0 0 1 .474 1.68l-1.683 1.682a2.414 2.414 0 0 1-3.414 0L8.61 19.61a1 1 0 0 0-1.68.474 2.5 2.5 0 1 1-3.014-3.015 1 1 0 0 0 .474-1.68l-1.683-1.682a2.414 2.414 0 0 1 0-3.414L4.39 8.61a1 1 0 0 1 1.68.474 2.5 2.5 0 1 0 3.014-3.015 1 1 0 0 1-.474-1.68l1.683-1.682a2.414 2.414 0 0 1 3.414 0z"/></svg> Ficha
                            </p>
                            <p class="text-sm font-medium text-gray-900">${obra.ficha}</p>
                        </div>
                        <div>
                            <p class="flex text-sm font-medium js-name gap-2 items-center pb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users-icon lucide-users"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><path d="M16 3.128a4 4 0 0 1 0 7.744"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/></svg> Tipo
                            </p>
                            <span class="inline-block px-2 py-1 bg-secondary text-white text-xs font-semibold rounded-full">${obra.tipo_trabajo}</span>
                        </div>
                        <div>
                            <p class="flex text-sm font-medium js-name gap-2 items-center pb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-icon lucide-calendar"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg> Inicio
                            </p>
                            <p class="text-sm font-medium text-gray-900">${formatDate(obra.fecha_inicio)}</p>
                        </div>
                        <div>
                            <p class="flex text-sm font-medium js-name gap-2 items-center pb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-icon lucide-calendar"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg> Fin
                            </p>
                            <p class="text-sm font-medium text-gray-900">${formatDate(obra.fecha_fin)}</p>
                        </div>
                    </div>
                    
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Instructor:</span> ${obra.instructor}
                    </div>
                </div>
                
                <div class="flex flex-col items-center gap-3">
                    <div class="flex items-center gap-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                class="sr-only peer" 
                                ${obra.estado == 1 ? 'checked' : ''}
                                onchange="toggleEstado(${obra.id}, this.checked)"
                            >
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>

                        <p class="flex text-sm font-medium js-name gap-2 items-center">${obra.estado == 1 ? 'Activo' : 'Inactivo'}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button 
                            onclick="openDetailsModal(${obra.id})"
                            class="text-gray-600 hover:text-gray-900 p-2"
                            title="Ver detalles"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                        
                        <button 
                            onclick="openEditModal(${obra.id})"
                            class="text-blue-600 hover:text-blue-800 p-2"
                            title="Editar"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen-icon lucide-square-pen"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Formatear fecha
function formatDate(dateString) {
    const date = new Date(dateString + 'T00:00:00');
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

// Formatear fecha completa
function formatFullDate(dateString) {
    const date = new Date(dateString + 'T00:00:00');
    const months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 
                    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear();
    return `${day} de ${month} de ${year}`;
}

// Buscar obras
function searchObras() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();

    if (searchTerm.trim() === '') {
        renderObras(obras);
        return;
    }

    const results = obras.filter(obra => 
        obra.nombre_actividad.toLowerCase().includes(searchTerm) ||
        obra.ficha.toLowerCase().includes(searchTerm) ||
        obra.instructor.toLowerCase().includes(searchTerm) ||
        obra.descripcion.toLowerCase().includes(searchTerm)
    );

    renderObras(results);
}

// Alternar estado de obra
function toggleEstado(id, estado) {
    const obra = obras.find(o => o.id == id);
    if (!obra) return;

    obra.estado = estado ? 1 : 0;
    updateEstadisticas();
    renderObras(obras);
}

// Modal crear obra
function openCreateModal() {
    document.getElementById('modalCreate').classList.remove('hidden');
    document.getElementById('formCreate').reset();
    if (document.getElementById('create_estado')) {
        document.getElementById('create_estado').checked = true;
    }
}

function closeCreateModal() {
    document.getElementById('modalCreate').classList.add('hidden');
}

function handleCreateObra(e) {
    e.preventDefault();

    const nuevaObra = {
        id: nextId++,
        ficha: document.getElementById('create_ficha').value,
        rae: document.getElementById('create_rae').value,
        instructor: document.getElementById('create_instructor').value,
        nombre_actividad: document.getElementById('create_nombre').value,
        descripcion: document.getElementById('create_descripcion').value,
        tipo_trabajo: document.getElementById('create_tipo').value,
        fecha_inicio: document.getElementById('create_fecha_inicio').value,
        fecha_fin: document.getElementById('create_fecha_fin').value,
        estado: document.getElementById('create_estado') ? (document.getElementById('create_estado').checked ? 1 : 0) : 1
    };

    obras.push(nuevaObra);
    closeCreateModal();
    updateEstadisticas();
    renderObras(obras);
    alert('Obra creada exitosamente');
}

// Modal editar obra
function openEditModal(id) {
    console.log('Abriendo modal para ID:', id);
    console.log('Obras disponibles:', obras);
    
    const obra = obras.find(o => o.id == id);
    if (!obra) {
        console.error('No se encontró obra con ID:', id);
        return;
    }

    console.log('Obra encontrada:', obra);
    
    // Llenar los campos del formulario
    document.getElementById('edit_id').value = obra.id;
    document.getElementById('edit_ficha').value = obra.ficha;
    document.getElementById('edit_rae').value = obra.rae;
    document.getElementById('edit_instructor').value = obra.instructor;
    document.getElementById('edit_nombre').value = obra.nombre_actividad;
    document.getElementById('edit_descripcion').value = obra.descripcion;
    document.getElementById('edit_tipo').value = obra.tipo_trabajo;
    document.getElementById('edit_fecha_inicio').value = obra.fecha_inicio;
    document.getElementById('edit_fecha_fin').value = obra.fecha_fin;

    document.getElementById('modalEdit').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('modalEdit').classList.add('hidden');
}

function handleEditObra(e) {
    e.preventDefault();

    const id = parseInt(document.getElementById('edit_id').value);
    const obra = obras.find(o => o.id == id);
    if (!obra) return;

    obra.ficha = document.getElementById('edit_ficha').value;
    obra.rae = document.getElementById('edit_rae').value;
    obra.instructor = document.getElementById('edit_instructor').value;
    obra.nombre_actividad = document.getElementById('edit_nombre').value;
    obra.descripcion = document.getElementById('edit_descripcion').value;
    obra.tipo_trabajo = document.getElementById('edit_tipo').value;
    obra.fecha_inicio = document.getElementById('edit_fecha_inicio').value;
    obra.fecha_fin = document.getElementById('edit_fecha_fin').value;
    // obra.estado = document.getElementById('edit_estado') ? (document.getElementById('edit_estado').checked ? 1 : 0) : obra.estado;

    closeEditModal();
    updateEstadisticas();
    renderObras(obras);
    alert('Obra actualizada exitosamente');
}

// Modal detalles
function openDetailsModal(id) {
    const obra = obras.find(o => o.id == id);
    if (!obra) return;

    document.getElementById('details_nombre').textContent = obra.nombre_actividad;
    document.getElementById('details_badge_tipo').textContent = obra.estado == 1 ? 'Activa' : 'Inactiva';
    document.getElementById('details_badge_tipo').className = obra.estado == 1
        ? 'inline-block px-3 py-1 bg-secondary text-white text-xs font-semibold rounded-full'
        : 'inline-block px-3 py-1 bg-gray-500 text-white text-xs font-semibold rounded-full';
    document.getElementById('details_descripcion').textContent = obra.descripcion;
    document.getElementById('details_ficha').textContent = obra.ficha;
    document.getElementById('details_tipo').textContent = obra.tipo_trabajo;
    document.getElementById('details_instructor').textContent = obra.instructor;
    document.getElementById('details_rae').textContent = obra.rae;
    document.getElementById('details_fecha_inicio').textContent = formatFullDate(obra.fecha_inicio);
    document.getElementById('details_fecha_fin').textContent = formatFullDate(obra.fecha_fin);

    document.getElementById('modalDetails').classList.remove('hidden');
}

function closeDetailsModal() {
    document.getElementById('modalDetails').classList.add('hidden');
}



// Cerrar modales con tecla ESC
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeCreateModal();
        closeEditModal();
        closeDetailsModal();
    }
});