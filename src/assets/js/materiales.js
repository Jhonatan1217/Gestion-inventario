        const materialsData = [
            { code: 'MAT-001', name: 'Cemento gris', category: 'Construcción', type: 'Consumible', unit: 'Bolsa', stock: 42, minStock: 20, warehouse: 'Sub-bodega Construcción', description: 'Cemento gris uso general 50kg' },
            { code: 'MAT-002', name: 'Arena Fina', category: 'Construcción', type: 'Consumible', unit: 'Bolsa', stock: 28, minStock: 20, warehouse: 'Sub-bodega Construcción', description: 'Arena fina para mezcla' },
            { code: 'MAT-003', name: 'Taladro Percutor', category: 'Herramientas', type: 'Herramienta', unit: 'Unidad', stock: 5, minStock: 2, warehouse: 'Sub-bodega Herramientas', description: 'Taladro percutor profesional 1100W' },
            { code: 'MAT-004', name: 'Cable Eléctrico', category: 'Eléctrico', type: 'Consumible', unit: 'Metro', stock: 35, minStock: 10, warehouse: 'Sub-bodega Eléctrico', description: 'Cable eléctrico calibre 12' },
            { code: 'MAT-005', name: 'Pintura Blanca', category: 'Pintura', type: 'Consumible', unit: 'Galón', stock: 15, minStock: 5, warehouse: 'Sub-bodega Pintura', description: 'Pintura blanca interior 1 galón' },
            { code: 'MAT-006', name: 'Nivel Láser', category: 'Herramientas', type: 'Herramienta', unit: 'Unidad', stock: 8, minStock: 2, warehouse: 'Sub-bodega Herramientas', description: 'Nivel láser rotativo automático' },
            { code: 'MAT-007', name: 'Tubo PVC 4"', category: 'Sanitario', type: 'Consumible', unit: 'Unidad', stock: 22, minStock: 10, warehouse: 'Sub-bodega Sanitario', description: 'Tubo PVC 4 pulgadas clase 315' },
            { code: 'MAT-008', name: 'Concretera', category: 'Maquinaria', type: 'Herramienta', unit: 'Unidad', stock: 3, minStock: 1, warehouse: 'Sub-bodega Maquinaria', description: 'Concretera eléctrica 150 litros' },
            { code: 'MAT-009', name: 'Cemento blanco', category: 'Construcción', type: 'Consumible', unit: 'Bolsa', stock: 12, minStock: 5, warehouse: 'Sub-bodega Construcción', description: 'Cemento blanco cementicio 50kg' },
            { code: 'MAT-010', name: 'Motosierra', category: 'Herramientas', type: 'Herramienta', unit: 'Unidad', stock: 4, minStock: 1, warehouse: 'Sub-bodega Herramientas', description: 'Motosierra gasolina 45cc' },
            { code: 'MAT-011', name: 'Varilla de acero', category: 'Construcción', type: 'Consumible', unit: 'Metro', stock: 156, minStock: 50, warehouse: 'Sub-bodega Construcción', description: 'Varilla de acero corrugada #4' },
            { code: 'MAT-012', name: 'Masilla', category: 'Pintura', type: 'Consumible', unit: 'Kg', stock: 30, minStock: 10, warehouse: 'Sub-bodega Pintura', description: 'Masilla para pared 30 kg' }
        ];

        let currentPage = 1;
        const itemsPerPage = 10;
        const disabledMaterials = new Set();
        let currentView = 'table';

        function renderTable() {
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedData = materialsData.slice(start, end);
            
            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = '';

            paginatedData.forEach(material => {
                const isDisabled = disabledMaterials.has(material.code);
                const statusClass = isDisabled ? 'bg-gray-300 text-gray-600' : 'bg-success text-success-foreground';
                const statusText = isDisabled ? 'Deshabilitado' : 'Disponible';
                const rowClass = isDisabled ? 'disabled-row' : '';

                const row = document.createElement('tr');
                row.className = `hover:bg-muted transition-colors ${rowClass}`;
                row.dataset.materialCode = material.code;

                row.innerHTML = `
                    <td class="px-6 py-4 text-sm font-medium">${material.code}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center material-icon-bg" style="background-color: rgba(57, 169, 0, 0.1);">
                                <!-- Replace material icon SVG with empty image -->
                                <img src="/icon-material.png" alt="Material" class="w-5 h-5 text-primary">
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
                        <span class="inline-flex items-center gap-1 px-3 py-1 ${statusClass} text-xs font-medium rounded-full status-badge ${isDisabled ? 'inactive' : 'active'}">
                            <span class="w-2 h-2 ${isDisabled ? 'bg-red-700' : 'bg-success-foreground'} rounded-full opacity-70"></span>
                            ${statusText}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="relative">
                            <button class="text-muted-foreground hover:text-foreground transition-colors p-2 rounded hover:bg-accent card-menu-btn" onclick="toggleMenu(event)">
                                <!-- Replace menu icon SVG with empty image -->
                                <img src="/icon-menu.png" alt="Menu" class="w-5 h-5">
                            </button>
                            
                            <div class="hidden absolute top-full mt-2 w-48 bg-white border border-border rounded-lg shadow-lg z-50 dropdown-menu">
                                <button class="w-full px-4 py-2 text-left hover:bg-muted transition-colors flex items-center gap-2 text-foreground rounded-t-lg text-sm" onclick="openDetailsModal('${material.code}', '${material.name}')">
                                    <!-- Replace eye icon SVG with empty image -->
                                    <img src="/icon-eye.png" alt="View" class="w-4 h-4">
                                    Ver detalle
                                </button>
                                <button class="w-full px-4 py-2 text-left hover:bg-muted transition-colors flex items-center gap-2 text-foreground text-sm" onclick="openEditModal('${material.code}', '${material.name}')">
                                    <!-- Replace edit icon SVG with empty image -->
                                    <img src="/icon-edit.png" alt="Edit" class="w-4 h-4">
                                    Editar
                                </button>
                                <button class="w-full px-4 py-2 text-left hover:bg-red-50 transition-colors flex items-center gap-2 text-red-600 rounded-b-lg text-sm toggle-status-btn" onclick="toggleMaterialStatus('${material.code}', event)">
                                    <!-- Replace disable icon SVG with empty image -->
                                    <img src="/icon-disable.png" alt="Toggle" class="w-4 h-4">
                                    <span class="toggle-text">${isDisabled ? 'Habilitar' : 'Deshabilitar'}</span>
                                </button>
                            </div>
                        </div>
                    </td>
                `;

                tableBody.appendChild(row);
            });

            renderPagination();
        }

        function renderCards() {
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const paginatedData = materialsData.slice(start, end);
            
            const cardsContainer = document.getElementById('cardsContainer');
            cardsContainer.innerHTML = '';

            paginatedData.forEach((material, index) => {
                const isDisabled = disabledMaterials.has(material.code);
                const statusClass = isDisabled ? 'inactive' : 'active';
                const cardClass = isDisabled ? 'material-card disabled' : 'material-card';

                const card = document.createElement('div');
                card.className = `${cardClass} bg-card border rounded-lg p-6 hover:shadow-lg transition-all hover:-translate-y-1 cursor-pointer`;
                card.dataset.materialCode = material.code;
                card.onclick = () => openDetailsModal(material.code, material.name);

                card.innerHTML = `
                    <!-- Card header with icon and menu -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0 material-icon-bg">
                                <!-- Replace material icon SVG with empty image -->
                                <img src="/icon-material.png" alt="Material" class="w-6 h-6 text-primary">
                            </div>
                            <div>
                                <h3 class="font-semibold text-foreground">${material.name}</h3>
                                <p class="text-xs text-muted-foreground">${material.code}</p>
                            </div>
                        </div>
                        
                        <!-- Menu button -->
                        <div class="relative">
                            <button class="text-muted-foreground hover:text-foreground p-2 hover:bg-muted rounded transition-colors card-menu-btn" onclick="event.stopPropagation(); toggleCardMenu(this)">
                                <!-- Replace menu icon SVG with empty image -->
                                <img src="/icon-menu.png" alt="Menu" class="w-5 h-5">
                            </button>
                            
                            <!-- Dropdown menu -->
                            <div class="hidden absolute right-0 mt-2 w-48 bg-card border border-border rounded-lg shadow-lg z-50 dropdown-menu">
                                <button class="w-full px-4 py-2 text-left hover:bg-muted flex items-center gap-2 text-foreground text-sm rounded-t-lg transition-colors" onclick="event.stopPropagation(); openDetailsModal('${material.code}', '${material.name}')">
                                    <!-- Replace eye icon SVG with empty image -->
                                    <img src="/icon-eye.png" alt="View" class="w-4 h-4">
                                    Ver detalle
                                </button>
                                <button class="w-full px-4 py-2 text-left hover:bg-muted flex items-center gap-2 text-foreground text-sm transition-colors" onclick="event.stopPropagation(); openEditModal('${material.code}', '${material.name}')">
                                    <!-- Replace edit icon SVG with empty image -->
                                    <img src="/icon-edit.png" alt="Edit" class="w-4 h-4">
                                    Editar
                                </button>
                                <button class="w-full px-4 py-2 text-left hover:bg-red-50 flex items-center gap-2 text-red-600 text-sm rounded-b-lg transition-colors toggle-status-btn" onclick="event.stopPropagation(); toggleMaterialStatus('${material.code}', event)">
                                    <!-- Replace disable icon SVG with empty image -->
                                    <img src="/icon-disable.png" alt="Toggle" class="w-4 h-4">
                                    <span class="toggle-text">${isDisabled ? 'Habilitar' : 'Deshabilitar'}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Material description -->
                    <p class="text-sm text-muted-foreground mb-4">${material.description || 'Sin descripción'}</p>

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
                                <!-- Replace clock icon SVG with empty image -->
                                <img src="/icon-clock.png" alt="Clock" class="w-4 h-4">
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
                            ${isDisabled ? 'Inactivo' : 'Activo'}
                        </span>
                        
                        <!-- Added onclick event to label with event.stopPropagation() to prevent opening details modal -->
                        <!-- Toggle switch -->
                        <label class="relative inline-flex items-center cursor-pointer" onclick="event.stopPropagation()">
                            <input 
                                type="checkbox" 
                                class="sr-only peer" 
                                ${isDisabled ? '' : 'checked'}
                                onchange="event.stopPropagation(); toggleMaterialStatusSwitch('${material.code}', this)"
                            >
                            <div class="w-11 h-6 bg-muted peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                `;

                cardsContainer.appendChild(card);
            });

            renderCardPagination();
        }

        function toggleCardMenu(button) {
            const menu = button.nextElementSibling;
            const isHidden = menu.classList.contains('hidden');

            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if (m !== menu) {
                    m.classList.add('hidden');
                }
            });

            if (isHidden) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        }

        function toggleMaterialStatusSwitch(materialCode, switchElement) {
            const isChecked = switchElement.checked;
            if (isChecked) {
                disabledMaterials.delete(materialCode);
            } else {
                disabledMaterials.add(materialCode);
            }
            renderCards();
        }

        function renderPagination() {
            const totalPages = Math.ceil(materialsData.length / itemsPerPage);
            const paginationElement = currentView === 'table' ? document.getElementById('pagination') : document.getElementById('cardPagination');
            paginationElement.innerHTML = '';

            const prevButton = document.createElement('button');
            prevButton.textContent = '← Anterior';
            prevButton.disabled = currentPage === 1;
            prevButton.className = 'pagination-btn';
            prevButton.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable();
                    window.scrollTo(0, 0);
                }
            };
            paginationElement.appendChild(prevButton);

            const spacer1 = document.createElement('span');
            spacer1.textContent = '|';
            spacer1.style.color = 'var(--muted-foreground)';
            paginationElement.appendChild(spacer1);

            for (let i = 1; i <= totalPages; i++) {
                const pageButton = document.createElement('button');
                pageButton.textContent = i;
                pageButton.className = currentPage === i ? 'active' : '';
                pageButton.onclick = () => {
                    currentPage = i;
                    renderTable();
                    window.scrollTo(0, 0);
                };
                paginationElement.appendChild(pageButton);
            }

            const spacer2 = document.createElement('span');
            spacer2.textContent = '|';
            spacer2.style.color = 'var(--muted-foreground)';
            paginationElement.appendChild(spacer2);

            const nextButton = document.createElement('button');
            nextButton.textContent = 'Siguiente →';
            nextButton.disabled = currentPage === totalPages;
            nextButton.className = 'pagination-btn';
            nextButton.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTable();
                    window.scrollTo(0, 0);
                }
            };
            paginationElement.appendChild(nextButton);
        }

        function renderCardPagination() {
            const pagination = document.getElementById('cardPagination');
            pagination.innerHTML = '';

            const totalPages = Math.ceil(materialsData.length / itemsPerPage);

            const prevButton = document.createElement('button');
            prevButton.textContent = '← Anterior';
            prevButton.disabled = currentPage === 1;
            prevButton.className = 'pagination-btn';
            prevButton.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderCards();
                    window.scrollTo(0, 0);
                }
            };
            pagination.appendChild(prevButton);

            const spacer1 = document.createElement('span');
            spacer1.textContent = '|';
            spacer1.style.color = 'var(--muted-foreground)';
            pagination.appendChild(spacer1);

            for (let i = 1; i <= totalPages; i++) {
                const pageButton = document.createElement('button');
                pageButton.textContent = i;
                pageButton.className = currentPage === i ? 'active' : '';
                pageButton.onclick = () => {
                    currentPage = i;
                    renderCards();
                    window.scrollTo(0, 0);
                };
                pagination.appendChild(pageButton);
            }

            const spacer2 = document.createElement('span');
            spacer2.textContent = '|';
            spacer2.style.color = 'var(--muted-foreground)';
            pagination.appendChild(spacer2);

            const nextButton = document.createElement('button');
            nextButton.textContent = 'Siguiente →';
            nextButton.disabled = currentPage === totalPages;
            nextButton.className = 'pagination-btn';
            nextButton.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderCards();
                    window.scrollTo(0, 0);
                }
            };
            pagination.appendChild(nextButton);
        }

        function switchView(view) {
            currentView = view;
            currentPage = 1;

            const tableViewBtn = document.getElementById('tableViewBtn');
            const cardViewBtn = document.getElementById('cardViewBtn');
            const tableViewContainer = document.getElementById('tableView');
            const cardViewContainer = document.getElementById('cardView');

            if (view === 'table') {
                tableViewBtn.classList.add('active');
                cardViewBtn.classList.remove('active');
                tableViewContainer.classList.remove('hidden');
                cardViewContainer.classList.add('hidden');
                renderTable();
            } else {
                tableViewBtn.classList.remove('active');
                cardViewBtn.classList.add('active');
                tableViewContainer.classList.add('hidden');
                cardViewContainer.classList.remove('hidden');
                renderCards();
            }
        }

        function toggleMaterialStatus(materialCode, event) {
            event.stopPropagation();
            
            if (disabledMaterials.has(materialCode)) {
                disabledMaterials.delete(materialCode);
            } else {
                disabledMaterials.add(materialCode);
            }

            if (currentView === 'table') {
                renderTable();
            } else {
                renderCards();
            }

            const menu = event.target.closest('.dropdown-menu');
            if (menu) menu.classList.add('hidden');
        }

        function toggleMenu(event) {
            const button = event.currentTarget;
            const menu = button.nextElementSibling;
            const isHidden = menu.classList.contains('hidden');

            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if (m !== menu) {
                    m.classList.add('hidden');
                }
            });

            if (isHidden) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.relative') && !event.target.closest('.card-menu-btn')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        document.addEventListener('click', function(event) {
            const createModal = document.getElementById('createModal');
            const detailsModal = document.getElementById('detailsModal');
            const editModal = document.getElementById('editModal');

            if (event.target === createModal) closeCreateModal();
            if (event.target === detailsModal) closeDetailsModal();
            if (event.target === editModal) closeEditModal();
        });

        document.addEventListener('DOMContentLoaded', () => {
            switchView(currentView);
        });

        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function openDetailsModal(code, name) {
            const material = materialsData.find(m => m.code === code);
            if (!material) return;

            document.getElementById('detailName').textContent = material.name;
            document.getElementById('detailCode').textContent = material.code;
            document.getElementById('detailCategory').textContent = material.category;
            document.getElementById('detailType').textContent = material.type;
            document.getElementById('detailStock').textContent = `${material.stock} ${material.unit}`;
            document.getElementById('detailMinStock').textContent = `${material.minStock} ${material.unit}`;
            document.getElementById('detailWarehouse').textContent = material.warehouse;
            
            const isMaterialDisabled = disabledMaterials.has(code);
            const statusBadge = document.getElementById('detailStatus');
            if (isMaterialDisabled) {
                statusBadge.textContent = 'Inactivo';
                statusBadge.classList.remove('bg-success', 'text-success-foreground');
                statusBadge.classList.add('bg-red-100', 'text-red-700');
            } else {
                statusBadge.textContent = 'Activo';
                statusBadge.classList.remove('bg-red-100', 'text-red-700');
                statusBadge.classList.add('bg-success', 'text-success-foreground');
            }

            document.getElementById('detailsModal').classList.remove('hidden');
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }

        function openEditModal(code, name) {
            const material = materialsData.find(m => m.code === code);
            if (!material) return;

            document.getElementById('editCodigo').value = material.code;
            document.getElementById('editNombre').value = material.name;
            document.getElementById('editDescripcion').value = material.description;
            document.getElementById('editCategoria').value = material.category;
            document.getElementById('editTipo').value = material.type;
            document.getElementById('editUnidad').value = material.unit;
            document.getElementById('editStock').value = material.stock;
            document.getElementById('editStockMin').value = material.minStock;
            document.getElementById('editBodega').value = material.warehouse;
            document.getElementById('editObservacion').value = material.observacion || "";

            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        document.querySelector('#editModal form').addEventListener('submit', function(e) {
            e.preventDefault();

            const code = document.getElementById('editCodigo').value;
            const material = materialsData.find(m => m.code === code);
            if (!material) return;

            material.name = document.getElementById('editNombre').value;
            material.description = document.getElementById('editDescripcion').value;
            material.category = document.getElementById('editCategoria').value;
            material.type = document.getElementById('editTipo').value;
            material.unit = document.getElementById('editUnidad').value;
            material.stock = parseInt(document.getElementById('editStock').value, 10);
            material.minStock = parseInt(document.getElementById('editStockMin').value, 10);
            material.warehouse = document.getElementById('editBodega').value;
            material.observacion = document.getElementById('editObservacion').value;

            if (currentView === 'table') {
                renderTable();
            } else {
                renderCards();
            }

            closeEditModal();
        });
