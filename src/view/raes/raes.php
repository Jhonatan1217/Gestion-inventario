<?php
$collapsed = isset($_GET["coll"]) && $_GET["coll"] == "1";
$sidebarWidth = $collapsed ? "70px" : "260px";
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
                        background: 'var(--background)',
                        foreground: 'var(--foreground)',
                        card: 'var(--card)',
                        'card-foreground': 'var(--card-foreground)',
                        popover: 'var(--popover)',
                        'popover-foreground': 'var(--popover-foreground)',
                        primary: 'var(--primary)',
                        'primary-foreground': 'var(--primary-foreground)',
                        secondary: 'var(--secondary)',
                        'secondary-foreground': 'var(--secondary-foreground)',
                        muted: 'var(--muted)',
                        'muted-foreground': 'var(--muted-foreground)',
                        accent: 'var(--accent)',
                        'accent-foreground': 'var(--accent-foreground)',
                        destructive: 'var(--destructive)',
                        'destructive-foreground': 'var(--destructive-foreground)',
                        border: 'var(--border)',
                        input: 'var(--input)',
                        ring: 'var(--ring)',
                        success: 'var(--success)',
                        warning: 'var(--warning)',
                        info: 'var(--info)',
                    }
                }
            }
        }
    </script>
    
    <!-- Solo importamos global.css del SENA -->
    <link rel="stylesheet" href="<?= BASE_URL ?>src/assets/css/globals.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Reduciendo significativamente los márgenes laterales: 80px cuando está colapsado y 270px cuando está expandido -->
    <main class="p-6 transition-all duration-300"
      style="margin-left: <?= $collapsed ? '70px' : '260px' ?>;">
        
        <!-- Título de la página -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-foreground">Resultados de Aprendizaje (RAE)</h1>
            <p class="text-muted-foreground mt-1">Administra los RAEs asociados a los programas de formación</p>
        </div>

        <!-- Contenedor principal con bordes y sombra -->
        <div class="bg-card rounded-lg shadow-sm">
            
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
                    <div class="inline-flex rounded-lg border border-border bg-card shadow-sm overflow-hidden">

                        <!-- Vista Tabla -->
                        <button
                            type="button"
                            id="viewTableBtn"
                            onclick="toggleView('table')"
                            class="px-3 py-2 text-xs sm:text-sm flex items-center gap-1 bg-muted text-foreground"
                            title="Vista tabla"
                        >
                            <!-- Icono lista -->
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Vista Tarjetas -->
                        <button
                            type="button"
                            id="viewGridBtn"
                            onclick="toggleView('grid')"
                            class="px-3 py-2 text-xs sm:text-sm flex items-center gap-1 text-muted-foreground"
                            title="Vista tarjetas"
                        >
                            <!-- Icono grid -->
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <rect x="4" y="4" width="7" height="7" rx="1"></rect>
                                <rect x="13" y="4" width="7" height="7" rx="1"></rect>
                                <rect x="4" y="13" width="7" height="7" rx="1"></rect>
                                <rect x="13" y="13" width="7" height="7" rx="1"></rect>
                            </svg>
                        </button>

                    </div>
                    
                    <!-- Botón nuevo RAE -->
                    <button onclick="openCreateModal()" class="bg-primary hover:bg-secondary text-primary-foreground px-4 py-2 rounded-lg flex items-center gap-2 transition-colors font-medium">
                        <i class="fas fa-plus"></i>
                        Nuevo RAE
                    </button>
                </div>
            </div>

            <!-- VISTA DE TABLA -->
            <div id="tableView" class="overflow-x-auto border border-border rounded-lg">
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
                                    <div class="w-12 h-12 bg-muted rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-primary w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                        </svg>
                                    </div>
                                    <span class="text-sm text-foreground"><?php echo $rae['descripcion']; ?></span>
                                </div>
                            </td>
                            
                            <!-- Programa asociado con ícono de gorro de graduación -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span><?php echo $rae['programa']; ?></span>
                                </div>
                            </td>
                            
                            <!-- Badge de estado (Activo/Inactivo) -->
                            <td class="py-4 px-4">
                                <?php if (strtolower($rae['estado']) === 'activo'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success">
                                        <?php echo $rae['estado']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400">
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
                                        <button onclick="openEditModal('<?php echo $rae['id']; ?>', '<?php echo addslashes($rae['descripcion']); ?>', '<?php echo $rae['programa']; ?>')" class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground rounded-t-lg transition-colors">
                                            <i class="far fa-edit text-muted-foreground"></i>
                                            Editar
                                        </button>
                                        <button onclick="openDetailsModal('<?php echo $rae['id']; ?>', '<?php echo addslashes($rae['descripcion']); ?>', '<?php echo $rae['programa']; ?>', '<?php echo $rae['estado']; ?>')" class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground transition-colors">
                                            <i class="far fa-eye text-muted-foreground"></i>
                                            Ver detalles
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
            <div id="gridView" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                <?php foreach ($raes as $index => $rae): ?>
                
                <!-- Rediseño completo de tarjeta para coincidir con el diseño de referencia -->
                <div class="bg-card border border-border rounded-2xl p-6 hover:shadow-lg transition-all">
                    
                    <!-- Header: Icono del libro + Descripción grande + Botón editar -->
                    <div class="flex items-start gap-4 mb-4">
                        <!-- Icono del libro con fondo verde suave -->
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0" style="background-color: color-mix(in srgb, var(--primary) 18%, transparent);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-primary w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        
                        <!-- Descripción del RAE (título grande) -->
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-foreground leading-tight"><?php echo $rae['descripcion']; ?></h3>
                        </div>
                        
                        <!-- Botón de editar en la esquina superior derecha -->
                        <button 
                            onclick="openEditModal('<?php echo $rae['id']; ?>', '<?php echo addslashes($rae['descripcion']); ?>', '<?php echo $rae['programa']; ?>')" 
                            class="text-muted-foreground hover:text-foreground transition flex-shrink-0"
                            title="Editar RAE"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </button>
                    </div>

                    <!-- Línea separadora -->
                    <div class="border-t border-border mb-4"></div>

                    <!-- Sección inferior: Estado + Programa + Switch -->
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <!-- Badge de estado arriba -->
                            <div class="mb-3">
                                <?php if (strtolower($rae['estado']) === 'activo'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-success" style="background-color: color-mix(in srgb, var(--success) 18%, transparent);">
                                        Activo
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-gray-400" style="background-color: color-mix(in srgb, #9ca3af 18%, transparent);">
                                        Inactivo
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Programa con icono de graduación abajo -->
                            <div class="flex items-center gap-2 <?php echo strtolower($rae['estado']) === 'inactivo' ? 'text-foreground' : 'text-foreground'; ?>">
                                <i class="fas fa-graduation-cap text-sm"></i>
                                <span class="text-sm"><?php echo $rae['programa']; ?></span>
                            </div>
                        </div>
                        
                        <!-- Switch en la parte derecha inferior -->
                        <div class="flex-shrink-0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" <?php echo strtolower($rae['estado']) === 'activo' ? 'checked' : ''; ?>>
                                <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-primary transition-colors peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- Modal de Detalles del RAE -->
    <div id="detailsModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-card rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-border">
            <!-- Header del modal -->
            <div class="sticky top-0 bg-card border-b border-border p-6 flex items-center justify-between z-10">
                <div>
                    <h2 id="detailsRaeId" class="text-2xl font-bold text-foreground">RAE</h2>
                    <span id="detailsRaeStatus" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success mt-1">Activo</span>
                </div>
                <button onclick="closeDetailsModal()" class="text-muted-foreground hover:text-foreground transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Contenido del modal -->
            <div class="p-6 space-y-6">
                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-medium text-muted-foreground mb-2">Descripción del RAE</label>
                    <p id="detailsRaeDescription" class="text-foreground text-base leading-relaxed"></p>
                </div>

                <!-- Programa -->
                <div>
                    <label class="block text-sm font-medium text-muted-foreground mb-2">Programa de formación</label>
                    <div class="flex items-center gap-2 text-foreground">
                        <i class="fas fa-graduation-cap text-primary"></i>
                        <span id="detailsPrograma"></span>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-border">
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Fecha de creación</label>
                        <p class="text-foreground">15 Nov 2024</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground mb-1">Última actualización</label>
                        <p class="text-foreground">20 Nov 2024</p>
                    </div>
                </div>
            </div>

            <!-- Footer del modal -->
            <div class="border-t border-border p-6 flex gap-3 justify-end">
                <button onclick="closeDetailsModal()" class="px-4 py-2 border border-border rounded-lg hover:bg-muted transition-colors text-foreground font-medium">
                    Cerrar
                </button>
                <button class="px-4 py-2 bg-primary hover:bg-secondary text-primary-foreground rounded-lg transition-colors font-medium">
                    Editar RAE
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Edición del RAE -->
    <div id="editModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-card rounded-2xl shadow-2xl max-w-2xl w-full border border-border">
            <!-- Header del modal -->
            <div class="bg-card border-b border-border p-6 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-foreground">Editar RAE</h2>
                <button onclick="closeEditModal()" class="text-muted-foreground hover:text-foreground transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Formulario de edición -->
            <div class="p-6 space-y-4">
                <!-- Programa -->
                <div>
                    <label for="editRaeProgram" class="block text-sm font-medium text-foreground mb-2">
                        Programa de formación
                    </label>
                    <select id="editRaeProgram" class="w-full px-4 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-ring transition-all">
                        <option value="">Selecciona un programa</option>
                        <option value="Técnico en Construcción">Técnico en Construcción</option>
                        <option value="Técnico en Instalaciones Eléctricas">Técnico en Instalaciones Eléctricas</option>
                        <option value="Técnico en Acabados de Construcción">Técnico en Acabados de Construcción</option>
                    </select>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="editRaeDescription" class="block text-sm font-medium text-foreground mb-2">
                        Descripción del RAE
                    </label>
                    <textarea 
                        id="editRaeDescription" 
                        rows="4" 
                        class="w-full px-4 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-ring transition-all resize-none"
                        placeholder="Describe el resultado de aprendizaje esperado..."
                    ></textarea>
                </div>
            </div>

            <!-- Footer del modal -->
            <div class="border-t border-border p-6 flex gap-3 justify-end">
                <button onclick="closeEditModal()" class="px-4 py-2 border border-border rounded-lg hover:bg-muted transition-colors text-foreground font-medium">
                    Cancelar
                </button>
                <button class="px-4 py-2 bg-primary hover:bg-secondary text-primary-foreground rounded-lg transition-colors font-medium">
                    Guardar cambios
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Creación del RAE -->
    <div id="createModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-card rounded-2xl shadow-2xl max-w-2xl w-full border border-border">
            <!-- Header del modal -->
            <div class="bg-card border-b border-border p-6 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-foreground">Crear Nuevo RAE</h2>
                <button onclick="closeCreateModal()" class="text-muted-foreground hover:text-foreground transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Formulario de creación -->
            <div class="p-6 space-y-4">
                <!-- Programa -->
                <div>
                    <label for="createRaeProgram" class="block text-sm font-medium text-foreground mb-2">
                        Programa de formación *
                    </label>
                    <select id="createRaeProgram" class="w-full px-4 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-ring transition-all">
                        <option value="">Selecciona un programa</option>
                        <option value="Técnico en Construcción">Técnico en Construcción</option>
                        <option value="Técnico en Instalaciones Eléctricas">Técnico en Instalaciones Eléctricas</option>
                        <option value="Técnico en Acabados de Construcción">Técnico en Acabados de Construcción</option>
                    </select>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="createRaeDescription" class="block text-sm font-medium text-foreground mb-2">
                        Descripción del RAE *
                    </label>
                    <textarea 
                        id="createRaeDescription" 
                        rows="4" 
                        class="w-full px-4 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-ring transition-all resize-none"
                        placeholder="Describe el resultado de aprendizaje esperado..."
                    ></textarea>
                </div>

                <!-- Estado inicial -->
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" checked class="w-4 h-4 text-primary bg-card border-border rounded focus:ring-ring focus:ring-2">
                        <span class="text-sm font-medium text-foreground">Activar RAE inmediatamente</span>
                    </label>
                </div>
            </div>

            <!-- Footer del modal -->
            <div class="border-t border-border p-6 flex gap-3 justify-end">
                <button onclick="closeCreateModal()" class="px-4 py-2 border border-border rounded-lg hover:bg-muted transition-colors text-foreground font-medium">
                    Cancelar
                </button>
                <button class="px-4 py-2 bg-primary hover:bg-secondary text-primary-foreground rounded-lg transition-colors font-medium">
                    Crear RAE
                </button>
            </div>
        </div>
    </div>
    <script src="<?= BASE_URL ?>src/assets/js/raes.js"></script>
</body>
</html>