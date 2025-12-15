<?php
$collapsed = isset($_GET["coll"]) && $_GET["coll"] == "1";
$sidebarWidth = $collapsed ? "70px" : "260px";

$raes = [
    [
        "id" => 1,
        "descripcion" => "Interpretar planos arquitectónicos para la correcta ejecución del proyecto.",
        "programa" => "Técnico en Construcción",
        "estado" => "Activo"
    ],
    [
        "id" => 2,
        "descripcion" => "Instalar circuitos eléctricos básicos según normativa vigente.",
        "programa" => "Técnico en Instalaciones Eléctricas",
        "estado" => "Activo"
    ],
    [
        "id" => 3,
        "descripcion" => "Aplicar acabados en muros y cielorrasos utilizando técnicas especializadas.",
        "programa" => "Técnico en Acabados de Construcción",
        "estado" => "Inactivo"
    ],
    [
        "id" => 4,
        "descripcion" => "Elaborar mezclas de concreto de acuerdo con las especificaciones del proyecto.",
        "programa" => "Técnico en Construcción",
        "estado" => "Activo"
    ],
    [
        "id" => 5,
        "descripcion" => "Realizar mantenimiento preventivo en instalaciones eléctricas residenciales.",
        "programa" => "Técnico en Instalaciones Eléctricas",
        "estado" => "Inactivo"
    ],
    [
        "id" => 6,
        "descripcion" => "Aplicar técnicas de pintura profesional en interiores y exteriores.",
        "programa" => "Técnico en Acabados de Construcción",
        "estado" => "Activo"
    ],
];
?>
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
            <div id="tableView" class="border border-border rounded-lg">
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
                    
                    <!-- Filas de datos: cargadas dinámicamente desde la API -->
                    <tbody id="raesTableBody">
                        <!-- contenido generado por JS -->
                    </tbody>
                </table>
            </div>

            <!-- VISTA DE GRID (Bloques) -->
            <div id="gridView" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                <div id="gridViewContainer" class="col-span-1 sm:col-span-2 lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- tarjetas generadas por JS -->
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Detalles del RAE -->
    <div id="detailsModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-card rounded-2xl shadow-2xl max-w-md w-full border border-border overflow-hidden">
            <!-- Header del modal -->
            <div class="bg-card pl-6 pr-6 pt-6 flex items-start justify-between rounded-t-2xl">
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-foreground">Detalles del RAE</h2>
                </div>
                <button onclick="closeDetailsModal()" class="text-muted-foreground hover:text-foreground transition flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Contenido del modal -->
            <div class="p-6 space-y-4">
                <!-- Icono + Código + Estado -->
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-[15px] flex items-center justify-center flex-shrink-0" style="background-color: color-mix(in srgb, var(--primary) 18%, white);">
                        <i class="fas fa-graduation-cap text-primary text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 id="detailsRaeCode" class="text-lg font-semibold text-foreground">RAE #001</h3>
                        <span id="detailsRaeStatus" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success">Activo</span>
                    </div>
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-xs text-muted-foreground font-medium mb-2">Descripción:</label>
                    <p id="detailsRaeDescription" class="text-sm text-foreground leading-relaxed"></p>
                </div>

                <!-- Programa -->
                <div>
                    <label class="block text-xs text-muted-foreground font-medium mb-2">Programa:</label>
                    <div class="flex items-center gap-2 text-foreground">
                        <i class="fas fa-graduation-cap text-foreground text-sm"></i>
                        <span id="detailsPrograma" class="text-sm"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edición del RAE -->
    <div id="editModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-card rounded-2xl shadow-2xl max-w-2xl w-full border border-border overflow-hidden">
            <!-- Header del modal -->
            <div class="bg-card border-border pt-6 pl-6 pr-6 flex items-center justify-between rounded-t-2xl">
                <h2 class="text-2xl font-bold text-foreground">Editar RAE</h2>
                <button onclick="closeEditModal()" class="text-muted-foreground hover:text-foreground transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Formulario de edición -->
            <div class="p-6 pt-0 pb-2 space-y-4">
                <!-- Hidden ID del RAE -->
                <input type="hidden" id="editRaeId" value="">

                <!-- Código RAE -->
                <div>
                    <label for="editRaeCodigo" class="block text-sm font-medium text-foreground mb-2">Código RAE *</label>
                    <input id="editRaeCodigo" type="text" placeholder="Ej: RAE-001" class="w-full px-4 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-ring transition-all">
                </div>

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
            <div class="border-border p-6 pt-0 flex gap-3 justify-end">
                <button onclick="closeEditModal()" class="px-4 py-2 border border-border rounded-lg hover:bg-muted transition-colors text-foreground font-medium">
                    Cancelar
                </button>
                <button id="editRaeSubmit" class="px-4 py-2 bg-primary hover:bg-secondary text-primary-foreground rounded-lg transition-colors font-medium">
                    Guardar cambios
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Creación del RAE -->
    <div id="createModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-card rounded-2xl shadow-2xl max-w-2xl w-full border border-border overflow-hidden">
            <!-- Header del modal (sin línea debajo) -->
            <div class="bg-card pt-6 pl-6 pr-6 flex items-start justify-between rounded-t-2xl">
                <div>
                    <h2 class="text-2xl font-bold text-foreground">Crear Nuevo RAE</h2>
                    <p class="text-sm text-muted-foreground mt-1">Complete los datos para registrar un nuevo resultado de aprendizaje</p>
                </div>
                <button onclick="closeCreateModal()" class="text-muted-foreground hover:text-foreground transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Formulario de creación (orden: código, programa, descripción) -->
            <div class="p-6 space-y-4">
                <!-- Código RAE -->
                <div>
                    <label for="createRaeCodigo" class="block text-sm font-medium text-foreground mb-2">Código RAE *</label>
                    <input id="createRaeCodigo" type="text" placeholder="Ej: RAE-001" class="w-full px-4 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-ring transition-all">
                </div>

                <!-- Programa -->
                <div>
                    <label for="createRaeProgram" class="block text-sm font-medium text-foreground mb-2">Programa de formación *</label>
                    <select id="createRaeProgram" class="w-full px-4 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-ring transition-all">
                        <option value="">Selecciona un programa</option>
                        <option value="Técnico en Construcción">Técnico en Construcción</option>
                        <option value="Técnico en Instalaciones Eléctricas">Técnico en Instalaciones Eléctricas</option>
                        <option value="Técnico en Acabados de Construcción">Técnico en Acabados de Construcción</option>
                    </select>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="createRaeDescription" class="block text-sm font-medium text-foreground mb-2">Descripción del RAE *</label>
                    <textarea id="createRaeDescription" rows="4" class="w-full px-4 py-2 border border-border rounded-lg bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-ring transition-all resize-none" placeholder="Describe el resultado de aprendizaje esperado..."></textarea>
                </div>
            </div>

            <!-- Footer del modal (sin línea separadora) -->
            <div class="pl-6 pr-6 pb-6 flex gap-3 justify-end">
                <button onclick="closeCreateModal()" class="px-4 py-2 border border-border rounded-lg hover:bg-muted transition-colors text-foreground font-medium">Cancelar</button>
                <button id="createRaeSubmit" class="px-4 py-2 bg-primary hover:bg-secondary text-primary-foreground rounded-lg transition-colors font-medium">Crear RAE</button>
            </div>
        </div>
    </div>
    <script src="<?= ASSETS_URL ?>js/raes.js"></script>
</body>
</html>