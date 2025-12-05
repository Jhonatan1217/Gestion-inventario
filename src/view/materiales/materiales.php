<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Materiales de Formación</title>

  <!-- Tailwind desde CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Fuente Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- CSS global con paleta y utilidades -->
  <link rel="stylesheet" href="../../assets/css/globals.css">
</head>
<body class="bg-background text-foreground">
  <div class="flex h-screen">
    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto">
        <div class="p-8">
          <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2">Materiales de Formación</h1>
            <p class="text-muted-foreground">Gestiona el inventario de materiales y herramientas</p>
          </div>

          <!-- Search and Add Button -->
          <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
            <div class="flex-1">
              <div class="relative">
                <!-- Search icon from lucide -->
                <svg class="absolute left-3 top-3 w-5 h-5 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
                <input type="text" placeholder="Buscar por nombre o código..." class="w-full pl-10 pr-4 py-2.5 bg-card border border-border rounded-lg text-sm placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary">
              </div>
            </div>
            
            <!-- View toggle buttons -->
            <div class="flex items-center gap-2">
              <div class="flex gap-1 border border-border rounded-lg p-1 bg-card">
                <button id="tableViewBtn" onclick="switchView('table')" class="view-toggle-btn active px-3 py-2 rounded text-sm font-medium transition-colors" title="Vista de tabla">
                  <!-- Table view icon from lucide -->
                  <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="8" y1="12" x2="16" y2="12"></line><line x1="8" y1="18" x2="16" y2="18"></line></svg>
                </button>
                <button id="cardViewBtn" onclick="switchView('card')" class="view-toggle-btn px-3 py-2 rounded text-sm font-medium transition-colors" title="Vista de tarjetas">
                    <!-- Card view icon from lucide -->
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"></path><rect x="4" y="4" width="16" height="16" rx="2"></rect><path d="M4 9h16"></path><path d="M4 15h16"></path></svg>
                </button>
              </div>
              
              <button onclick="openCreateModal()" class="flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-secondary text-primary-foreground font-medium rounded-lg transition-colors whitespace-nowrap">
                <!-- Add/Plus icon from lucide -->
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Nuevo material
              </button>
            </div>
          </div>

          <!-- Materials Table -->
          <div id="tableView" class="bg-card rounded-lg border border-border overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="bg-muted border-b border-border">
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Código</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Material</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Stock</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Unidad</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Bodega</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Estado</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Acciones</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-border" id="tableBody">
                  <!-- Rows will be populated by JavaScript -->
                </tbody>
              </table>
            </div>
            <div class="pagination" id="pagination"></div>
          </div>

          <!-- Card view -->
          <div id="cardView" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="cardsContainer">
              <!-- Cards will be populated by JavaScript -->
            </div>
            <div class="pagination" id="cardPagination"></div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- CREATE MODAL -->
<div id="createModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full modal-compact">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-foreground">Crear Nuevo Material</h2>
                <p class="text-xs text-muted-foreground">Complete los datos para registrar un nuevo material</p>
            </div>
            <button onclick="closeCreateModal()" class="text-gray-500 hover:text-gray-700">
                <!-- Close/X icon from lucide -->
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <form class="space-y-3">
            <!-- Código y Nombre -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Código *</label>
                    <input type="text" placeholder="TEC-001" class="input text-xs" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Nombre *</label>
                    <input type="text" placeholder="Ej:cemento gris" class="input text-xs" required>
                </div>
            </div>
            
            <!-- Descripción -->
            <div>
                <label class="block text-xs font-semibold text-foreground mb-1">Descripción *</label>
                <textarea placeholder="Descripción técnica del material" class="input h-16 text-xs" required></textarea>
            </div>
            
            <!-- Categoría, Tipo de bien, Unidad -->
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Categoría *</label>
                    <select class="input text-xs" required>
                        <option>Construcción</option>
                        <option>Herramientas</option>
                        <option>Eléctrico</option>
                        <option>Pintura</option>
                        <option>Sanitario</option>
                        <option>Maquinaria</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Tipo de bien *</label>
                    <select class="input text-xs" required>
                        <option>Consumible</option>
                        <option>Herramienta</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Unidad de medida *</label>
                    <select class="input text-xs" required>
                        <option>Bolsa</option>
                        <option>Unidad</option>
                        <option>Metro</option>
                        <option>Kg</option>
                    </select>
                </div>
            </div>
            
            <!-- Stock Actual, Stock Mínimo, Bodega -->
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Stock Actual *</label>
                    <input type="number" class="input text-xs" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Stock Mínimo *</label>
                    <input type="number" class="input text-xs" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Bodega *</label>
                    <select class="input text-xs" required>
                        <option>Bodega Principal</option>
                        <option>Sub-bodega Construcción</option>
                        <option>Sub-bodega Herramientas</option>
                        <option>Sub-bodega Eléctrico</option>
                        <option>Sub-bodega Pintura</option>
                        <option>Sub-bodega Sanitario</option>
                        <option>Sub-bodega Maquinaria</option>
                    </select>
                </div>
            </div>
            
            <!-- Observación -->
            <div>
                <label class="block text-xs font-semibold text-foreground mb-1">Observación *</label>
                <textarea placeholder="Formación técnica de procesos constructivos" class="input h-14 text-xs" required></textarea>
            </div>
            
            <!-- Buttons -->
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeCreateModal()" class="px-3 py-2 text-xs font-medium rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-3 py-2 text-xs font-medium rounded-lg bg-primary text-primary-foreground hover:bg-secondary transition-colors">
                    Crear Material
                </button>
            </div>
        </form>
    </div>
</div>

<!-- DETAILS MODAL -->
<div id="detailsModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full modal-compact">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-foreground">Detalles del Material</h2>
            <button onclick="closeDetailsModal()" class="text-gray-500 hover:text-gray-700">
                <!-- Close/X icon from lucide -->
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <div class="space-y-3" id="detailsContent">
            <div class="flex items-center gap-3 pb-3 border-b border-border">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: rgba(57, 169, 0, 0.1);">
                    <!-- Package/box icon from lucide -->
                    <svg class="w-6 h-6 text-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                </div>
                <div>
                    <p class="font-bold text-foreground text-sm" id="detailName">Cemento gris</p>
                    <p class="text-xs text-muted-foreground" id="detailCode">MAT-001</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-xs text-muted-foreground font-semibold">Categoría:</p>
                    <p class="text-foreground" id="detailCategory">Construcción</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground font-semibold">Tipo:</p>
                    <p class="text-foreground" id="detailType">Consumo</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground font-semibold">Stock:</p>
                    <p class="text-foreground" id="detailStock">45 bolsa</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground font-semibold">Stock mínimo:</p>
                    <p class="text-foreground" id="detailMinStock">20 bolsa</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground font-semibold">Bodega:</p>
                    <p class="text-foreground" id="detailWarehouse">Bodega Construcción</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground font-semibold">Estado:</p>
                    <span class="inline-block px-2 py-1 rounded text-xs font-medium bg-success text-success-foreground" id="detailStatus">Disponible</span>
                </div>
            </div>
        </div>
        
        <div class="flex gap-2 pt-4 mt-4 border-t border-border">
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full modal-compact">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-foreground">Editar Material</h2>
                <p class="text-xs text-muted-foreground">Modifica la información del material</p>
            </div>
            <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                <!-- Close/X icon from lucide -->
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <form class="space-y-3">
            <!-- Código y Nombre -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Código *</label>
                    <input id="editCodigo" type="text" class="input text-xs" disabled>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Nombre *</label>
                    <input id="editNombre" type="text" class="input text-xs" required>
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-xs font-semibold text-foreground mb-1">Descripción *</label>
                <textarea id="editDescripcion" class="input h-16 text-xs" required></textarea>
            </div>

            <!-- Categoría, Tipo de bien, Unidad -->
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Categoría *</label>
                    <select id="editCategoria" class="input text-xs" required>
                        <option>Construcción</option>
                        <option>Herramientas</option>
                        <option>Eléctrico</option>
                        <option>Pintura</option>
                        <option>Sanitario</option>
                        <option>Maquinaria</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Tipo de bien *</label>
                    <select id="editTipo" class="input text-xs" required>
                        <option>Consumible</option>
                        <option>Herramienta</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Unidad de medida *</label>
                    <select id="editUnidad" class="input text-xs" required>
                        <option>Bolsa</option>
                        <option>Unidad</option>
                        <option>Metro</option>
                        <option>Kg</option>
                    </select>
                </div>
            </div>

            <!-- Stock Actual, Stock Mínimo, Bodega -->
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Stock Actual *</label>
                    <input id="editStock" type="number" class="input text-xs" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Stock Mínimo *</label>
                    <input id="editStockMin" type="number" class="input text-xs" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Bodega *</label>
                    <select id="editBodega" class="input text-xs" required>
                        <option>Bodega Principal</option>
                        <option>Sub-bodega Construcción</option>
                        <option>Sub-bodega Herramientas</option>
                        <option>Sub-bodega Eléctrico</option>
                        <option>Sub-bodega Pintura</option>
                        <option>Sub-bodega Sanitario</option>
                        <option>Sub-bodega Maquinaria</option>
                    </select>
                </div>
            </div>

            <!-- Observación -->
            <div>
                <label class="block text-xs font-semibold text-foreground mb-1">Observación *</label>
                <textarea id="editObservacion" class="input h-14 text-xs" required></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-3 py-2 text-xs font-medium rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-3 py-2 text-xs font-medium rounded-lg bg-primary text-primary-foreground hover:bg-secondary transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script src="../../assets/js/materiales.js"></script>

</body>
</html>
