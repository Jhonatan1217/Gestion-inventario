<?php
// Datos de ejemplo para los RAEs (Resultados de Aprendizaje)
$raes = [
    [
        'id' => '#001',
        'descripcion' => 'Realizar instalaciones eléctricas residenciales básicas',
        'programa' => 'Técnico en Construcción',
        'estado' => 'Activo'
    ],
    [
        'id' => '#002',
        'descripcion' => 'Aplicar acabados en superficies según normativa vigente',
        'programa' => 'Técnico en Instalaciones Eléctricas',
        'estado' => 'Activo'
    ],
    [
        'id' => '#003',
        'descripcion' => 'Gestionar proyectos de construcción de mediana escala',
        'programa' => 'Técnico en Acabados de Construcción',
        'estado' => 'Activo'
    ],
    [
        'id' => '#004',
        'descripcion' => 'Realizar instalaciones eléctricas residenciales básicas',
        'programa' => 'Técnico en Construcción',
        'estado' => 'Activo'
    ],
    [
        'id' => '#005',
        'descripcion' => 'Aplicar acabados en superficies según normativa vigente',
        'programa' => 'Técnico en Instalaciones Eléctricas',
        'estado' => 'Inactivo'
    ],
    [
        'id' => '#006',
        'descripcion' => 'Gestionar proyectos de construcción de mediana escala',
        'programa' => 'Técnico en Acabados de Construcción',
        'estado' => 'Activo'
    ],
    [
        'id' => '#007',
        'descripcion' => 'Realizar instalaciones eléctricas residenciales básicas',
        'programa' => 'Técnico en Construcción',
        'estado' => 'Activo'
    ],
    [
        'id' => '#008',
        'descripcion' => 'Aplicar acabados en superficies según normativa vigente',
        'programa' => 'Técnico en Instalaciones Eléctricas',
        'estado' => 'Activo'
    ],
    [
        'id' => '#009',
        'descripcion' => 'Gestionar proyectos de construcción de mediana escala',
        'programa' => 'Técnico en Acabados de Construcción',
        'estado' => 'Activo'
    ],
    [
        'id' => '#010',
        'descripcion' => 'Realizar instalaciones eléctricas residenciales básicas',
        'programa' => 'Técnico en Construcción',
        'estado' => 'Activo'
    ],
    [
        'id' => '#011',
        'descripcion' => 'Aplicar acabados en superficies según normativa vigente',
        'programa' => 'Técnico en Instalaciones Eléctricas',
        'estado' => 'Activo'
    ],
    [
        'id' => '#012',
        'descripcion' => 'Gestionar proyectos de construcción de mediana escala',
        'programa' => 'Técnico en Acabados de Construcción',
        'estado' => 'Activo'
    ],
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Aprendizaje (RAE) - SENA</title>
    
    <!-- Configuración de Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Colores del tema SENA desde global.css
                        background: '#f9fafb',
                        foreground: '#0f172a',
                        card: '#ffffff',
                        'card-foreground': '#0f172a',
                        popover: '#ffffff',
                        'popover-foreground': '#0f172a',
                        primary: '#39A935',
                        'primary-foreground': '#ffffff',
                        secondary: '#047857',
                        'secondary-foreground': '#ffffff',
                        muted: '#f1f5f9',
                        'muted-foreground': '#64748b',
                        accent: '#f1f5f9',
                        'accent-foreground': '#0f172a',
                        destructive: '#ef4444',
                        'destructive-foreground': '#ffffff',
                        border: '#e2e8f0',
                        input: '#e2e8f0',
                        ring: '#39A935',
                        success: '#22c55e',
                        warning: '#f59e0b',
                        info: '#3b82f6',
                    }
                }
            }
        }
    </script>
    
    <!-- Solo importamos global.css del SENA -->
    <link rel="stylesheet" href="src/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-background text-foreground">
    
    <!-- CONTENIDO PRINCIPAL - Sin header ni sidebar (componentes separados) -->
    <main class="p-8">
        
        <!-- Título de la página -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-foreground">Resultados de Aprendizaje (RAE)</h1>
            <p class="text-muted-foreground mt-1">Administra los RAEs asociados a los programas de formación</p>
        </div>

        <!-- Contenedor principal con bordes y sombra -->
        <div class="bg-card rounded-lg shadow-sm border border-border p-6">
            
            <!-- Barra de filtros y acciones -->
            <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
                
                <!-- Barra de búsqueda -->
                <div class="relative flex-1 max-w-md">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground"></i>
                    <input 
                        type="text" 
                        placeholder="Buscar por nombre..." 
                        class="pl-10 pr-4 py-2 w-full border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-ring transition-all"
                    >
                </div>
                
                <!-- Botones de acción -->
                <div class="flex items-center gap-3">
                    
                    <!-- Botones de vista (tabla/grid) -->
                    <button 
                        onclick="toggleView('table')" 
                        id="viewTableBtn" 
                        class="p-2 hover:bg-muted rounded-lg transition-colors bg-muted"
                        title="Vista de tabla"
                    >
                        <i class="fas fa-list text-muted-foreground"></i>
                    </button>
                    <button 
                        onclick="toggleView('grid')" 
                        id="viewGridBtn" 
                        class="p-2 hover:bg-muted rounded-lg transition-colors"
                        title="Vista de bloques"
                    >
                        <i class="fas fa-th-large text-muted-foreground"></i>
                    </button>
                    
                    <!-- Botón nuevo RAE -->
                    <!-- Agregando evento onclick para abrir modal de creación -->
                    <button onclick="openCreateModal()" class="bg-primary hover:bg-secondary text-primary-foreground px-4 py-2 rounded-lg flex items-center gap-2 transition-colors font-medium">
                        <i class="fas fa-plus"></i>
                        Nuevo RAE
                    </button>
                </div>
            </div>

            <!-- VISTA DE TABLA -->
            <div id="tableView" class="overflow-x-auto">
                <table class="w-full border-collapse">
                    
                    <!-- Encabezados de la tabla -->
                    <thead>
                        <tr class="border-b border-border bg-muted">
                            <th class="text-left py-3 px-4 font-medium text-muted-foreground text-sm">ID</th>
                            <th class="text-left py-3 px-4 font-medium text-muted-foreground text-sm">Descripción</th>
                            <th class="text-left py-3 px-4 font-medium text-muted-foreground text-sm">Programa</th>
                            <th class="text-left py-3 px-4 font-medium text-muted-foreground text-sm">Estado</th>
                            <th class="text-left py-3 px-4 font-medium text-muted-foreground text-sm">Acciones</th>
                        </tr>
                    </thead>
                    
                    <!-- Filas de datos -->
                    <tbody>
                        <?php foreach ($raes as $index => $rae): ?>
                        <tr class="border-b border-border hover:bg-muted transition-colors">
                            
                            <!-- ID del RAE -->
                            <td class="py-4 px-4 text-sm font-medium text-foreground">
                                <?php echo $rae['id']; ?>
                            </td>
                            
                            <!-- Descripción del RAE con badge verde -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <!-- Badge verde con icono de libro -->
                                    <div class="w-8 h-8 bg-[#c8e6c9] rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-primary w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                        </svg>
                                    </div>
                                    <span class="text-sm text-foreground"><?php echo $rae['descripcion']; ?></span>
                                </div>
                            </td>
                            
                            <!-- Programa asociado con ícono de gorro de graduación -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2 text-sm text-foreground">
                                    <i class="fas fa-graduation-cap text-foreground"></i>
                                    <?php echo $rae['programa']; ?>
                                </div>
                            </td>
                            
                            <!-- Badge de estado (Activo/Inactivo) -->
                            <td class="py-4 px-4">
                                <?php if (strtolower($rae['estado']) === 'activo'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success">
                                        <?php echo $rae['estado']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#ef444426] text-destructive">
                                        <?php echo $rae['estado']; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            
                            <!-- Menú de acciones -->
                            <td class="py-4 px-4">
                                <div class="relative">
                                    <button 
                                        onclick="toggleActionMenu(<?php echo $index; ?>)" 
                                        class="text-muted-foreground hover:text-foreground transition-colors p-2 hover:bg-muted rounded"
                                    >
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    
                                    <!-- Menú desplegable -->
                                    <div 
                                        id="actionMenu<?php echo $index; ?>" 
                                        class="hidden absolute right-0 mt-2 w-48 bg-card rounded-lg shadow-lg border border-border z-50"
                                    >
                                        <!-- Agregando evento onclick para abrir modal de detalles -->
                                        <button onclick="openDetailsModal('<?php echo $rae['id']; ?>', '<?php echo addslashes($rae['descripcion']); ?>', '<?php echo $rae['programa']; ?>', '<?php echo $rae['estado']; ?>')" class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground rounded-t-lg transition-colors">
                                            <i class="far fa-eye text-muted-foreground"></i>
                                            Ver detalles
                                        </button>
                                        
                                        <!-- Agregando evento onclick para abrir modal de edición -->
                                        <button onclick="openEditModal('<?php echo $rae['id']; ?>', '<?php echo addslashes($rae['descripcion']); ?>', '<?php echo $rae['programa']; ?>')" class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground transition-colors">
                                            <i class="far fa-edit text-muted-foreground"></i>
                                            Editar
                                        </button>
                                        <button class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground rounded-b-lg transition-colors">
                                            <i class="fas fa-ban text-muted-foreground"></i>
                                            Deshabilitar
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- VISTA DE GRID (Bloques) -->
            <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 hidden">
                <?php foreach ($raes as $index => $rae): ?>
                
                <!-- Tarjeta individual del RAE -->
                <div class="bg-card border border-border rounded-lg p-6 hover:shadow-md transition-all hover:-translate-y-1">
                    
                    <!-- Header de la tarjeta -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <!-- Badge verde con icono de libro -->
                                <div class="w-10 h-10 bg-[#c8e6c9] rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="5" stroke="currentColor" class="text-primary w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                        </svg>
                                </div>
                            <div>
                                <h3 class="font-semibold text-foreground text-sm"><?php echo $rae['id']; ?></h3>
                            </div>
                        </div>
                        
                        <!-- Botón de editar directo -->
                        <button 
                            onclick="openEditModal('<?php echo $rae['id']; ?>', '<?php echo addslashes($rae['descripcion']); ?>', '<?php echo $rae['programa']; ?>')" 
                            class="text-muted-foreground hover:text-foreground p-2 hover:bg-muted rounded-lg transition-colors"
                            title="Editar RAE"
                        >
                            <i class="far fa-edit"></i>
                        </button>
                    </div>

                    <!-- Descripción del RAE -->
                    <p class="text-sm text-foreground mb-4 font-medium"><?php echo $rae['descripcion']; ?></p>

                    <!-- Información del programa -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                            <!-- Cambio de icono de ojo a gorro de graduación -->
                            <i class="fas fa-graduation-cap"></i>
                            <span class="text-foreground"><?php echo $rae['programa']; ?></span>
                        </div>
                        
                        <!-- Badge de estado -->
                        <div class="flex items-center justify-between pt-2 border-t border-border">
                            <?php if (strtolower($rae['estado']) === 'activo'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success">
                                    <?php echo $rae['estado']; ?>
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#ef444426] text-destructive">
                                    <?php echo $rae['estado']; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- Modal de Detalles del RAE -->
    <div id="detailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-card rounded-lg shadow-xl max-w-md w-full relative animate-fadeIn">
            <!-- Contenido del modal -->
            <div class="p-6 space-y-4">
                <h2 class="text-xl font-semibold text-foreground">Detalles del RAE</h2>
                
                <!-- ID del RAE con badge de estado -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-[#c8e6c9] rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-graduation-cap text-primary text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-foreground" id="detailsRaeId">RAE #001</h3>
                        <span id="detailsRaeStatus" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success mt-1">
                            Activo
                        </span>
                    </div>
                </div>
                
                <!-- Descripción del RAE -->
                <div>
                    <label class="text-sm font-medium text-muted-foreground block mb-2">Descripción:</label>
                    <p class="text-sm text-foreground" id="detailsRaeDescription">
                        Preparar mezclas de concreto según especificaciones técnicas
                    </p>
                </div>
                
                <!-- Información del programa con icono de gorro -->
                <div class="mb-3">
                    <label class="block text-sm font-medium text-muted-foreground mb-1">Programa:</label>
                    <div class="flex items-center gap-2 text-sm text-foreground">
                        <!-- Cambio de icono de ojo a gorro de graduación (SVG) -->
                         <i class="fas fa-graduation-cap text-foreground"></i>
                        <span id="detailsPrograma"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Editar RAE -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-card rounded-lg shadow-xl max-w-md w-full relative animate-fadeIn">
            
            <!-- Header del modal -->
            <div class="flex items-center justify-between p-6 border-b border-border">
                <div>
                    <h2 class="text-xl font-bold text-foreground">Editar RAE</h2>
                    <p class="text-sm text-muted-foreground mt-1">Modifica la información del RAE</p>
                </div>
                <button onclick="closeEditModal()" class="text-muted-foreground hover:text-foreground transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Formulario de edición -->
            <div class="p-6 space-y-4">
                
                <!-- Campo de programa de formación -->
                <div>
                    <label class="text-sm font-medium text-foreground block mb-2">
                        Programa de formación <span class="text-destructive">*</span>
                    </label>
                    <select id="editRaeProgram" class="w-full px-4 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-ring transition-all">
                        <option value="Técnico en Construcción">Técnico en Construcción</option>
                        <option value="Técnico en Instalaciones Eléctricas">Técnico en Instalaciones Eléctricas</option>
                        <option value="Técnico en Acabados de Construcción">Técnico en Acabados de Construcción</option>
                    </select>
                </div>
                
                <!-- Campo de descripción -->
                <div>
                    <label class="text-sm font-medium text-foreground block mb-2">
                        Descripción del RAE <span class="text-destructive">*</span>
                    </label>
                    <textarea 
                        id="editRaeDescription" 
                        rows="4" 
                        class="w-full px-4 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-ring transition-all resize-none"
                        placeholder="Ingrese la descripción del RAE..."
                    >Preparar mezclas de concreto según especificaciones técnicas</textarea>
                </div>
                
                <!-- Botones de acción -->
                <div class="flex items-center justify-end gap-3 pt-4">
                    <button onclick="closeEditModal()" class="px-4 py-2 text-sm font-medium text-foreground hover:bg-muted rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button class="px-4 py-2 text-sm font-medium bg-primary hover:bg-secondary text-primary-foreground rounded-lg transition-colors">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Crear Nuevo RAE -->
    <div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-card rounded-lg shadow-xl max-w-md w-full relative animate-fadeIn">
            
            <!-- Header del modal -->
            <div class="flex items-center justify-between p-6 border-b border-border">
                <div>
                    <h2 class="text-xl font-bold text-foreground">Crear Nuevo RAE</h2>
                    <p class="text-sm text-muted-foreground mt-1">Completa los datos para registrar un nuevo resultado de aprendizaje</p>
                </div>
                <button onclick="closeCreateModal()" class="text-muted-foreground hover:text-foreground transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Formulario de creación -->
            <div class="p-6 space-y-4">
                
                <!-- Campo de programa de formación -->
                <div>
                    <label class="text-sm font-medium text-foreground block mb-2">
                        Programa de formación <span class="text-destructive">*</span>
                    </label>
                    <select id="createRaeProgram" class="w-full px-4 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-ring transition-all">
                        <option value="">Seleccione un programa</option>
                        <option value="Técnico en Construcción">Técnico en Construcción</option>
                        <option value="Técnico en Instalaciones Eléctricas">Técnico en Instalaciones Eléctricas</option>
                        <option value="Técnico en Acabados de Construcción">Técnico en Acabados de Construcción</option>
                    </select>
                </div>
                
                <!-- Campo de descripción -->
                <div>
                    <label class="text-sm font-medium text-foreground block mb-2">
                        Descripción del RAE <span class="text-destructive">*</span>
                    </label>
                    <textarea 
                        id="createRaeDescription" 
                        rows="4" 
                        class="w-full px-4 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-ring transition-all resize-none"
                        placeholder="Describe el resultado de aprendizaje esperado"
                    ></textarea>
                </div>
                
                <!-- Botones de acción -->
                <div class="flex items-center justify-end gap-3 pt-4">
                    <button onclick="closeCreateModal()" class="px-4 py-2 text-sm font-medium text-foreground hover:bg-muted rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button class="px-4 py-2 text-sm font-medium bg-primary hover:bg-secondary text-primary-foreground rounded-lg transition-colors">
                        Crear RAE
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript para interactividad -->
    <script>
        // Función para alternar entre vista de tabla y vista de grid
        function toggleView(view) {
            const tableView = document.getElementById('tableView');
            const gridView = document.getElementById('gridView');
            const tableBtn = document.getElementById('viewTableBtn');
            const gridBtn = document.getElementById('viewGridBtn');
            
            // Cerrar todos los menús desplegables al cambiar de vista
            const allMenus = document.querySelectorAll('[id^="actionMenu"]');
            allMenus.forEach(menu => menu.classList.add('hidden'));
            
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
                tableBtn.classList.remove('bg-muted');
                gridBtn.classList.add('bg-muted');
            }
        }

        // Función para mostrar/ocultar el menú de acciones
        function toggleActionMenu(id) {
            const menu = document.getElementById('actionMenu' + id);
            const allMenus = document.querySelectorAll('[id^="actionMenu"]');
            
            // Cerrar todos los demás menús
            allMenus.forEach(m => {
                if (m.id !== 'actionMenu' + id) {
                    m.classList.add('hidden');
                }
            });
            
            // Toggle del menú actual
            menu.classList.toggle('hidden');
        }

        // Cerrar menús al hacer clic fuera de ellos
        document.addEventListener('click', function(event) {
            const isMenuButton = event.target.closest('[onclick^="toggleActionMenu"]');
            const isInsideMenu = event.target.closest('[id^="actionMenu"]');
            
            if (!isMenuButton && !isInsideMenu) {
                const allMenus = document.querySelectorAll('[id^="actionMenu"]');
                allMenus.forEach(menu => menu.classList.add('hidden'));
            }
        });

        function openDetailsModal(id, descripcion, programa, estado) {
            // Cerrar todos los menús desplegables
            const allMenus = document.querySelectorAll('[id^="actionMenu"]');
            allMenus.forEach(menu => menu.classList.add('hidden'));
            
            // Actualizar contenido del modal
            document.getElementById('detailsRaeId').textContent = 'RAE ' + id;
            document.getElementById('detailsRaeDescription').textContent = descripcion;
            document.getElementById('detailsPrograma').textContent = programa;
            
            // Actualizar badge de estado
            const statusBadge = document.getElementById('detailsRaeStatus');
            if (estado.toLowerCase() === 'activo') {
                statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success mt-1';
                statusBadge.textContent = 'Activo';
            } else {
                statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#ef444426] text-destructive mt-1';
                statusBadge.textContent = 'Inactivo';
            }
            
            // Mostrar modal
            document.getElementById('detailsModal').classList.remove('hidden');
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }

        function openEditModal(id, descripcion, programa) {
            // Cerrar todos los menús desplegables
            const allMenus = document.querySelectorAll('[id^="actionMenu"]');
            allMenus.forEach(menu => menu.classList.add('hidden'));
            
            // Actualizar contenido del formulario
            document.getElementById('editRaeDescription').value = descripcion;
            document.getElementById('editRaeProgram').value = programa;
            
            // Mostrar modal
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openCreateModal() {
            // Limpiar campos del formulario
            document.getElementById('createRaeProgram').value = '';
            document.getElementById('createRaeDescription').value = '';
            
            // Mostrar modal
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }

        // Cerrar modales al hacer clic fuera de ellos
        document.getElementById('detailsModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeDetailsModal();
            }
        });

        document.getElementById('editModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeEditModal();
            }
        });

        document.getElementById('createModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeCreateModal();
            }
        });

        // Cerrar modales con la tecla Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDetailsModal();
                closeEditModal();
                closeCreateModal();
            }
        });
    </script>
</body>
</html>