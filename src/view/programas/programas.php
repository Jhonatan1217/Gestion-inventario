<?php
// Datos de ejemplo para los programas
$programas = [
    [
        'codigo' => 'TEC-001',
        'nombre' => 'Técnico en Construcción',
        'descripcion' => 'Formación técnica en procesos constructivos',
        'nivel' => 'Técnico',
        'duracion' => '18 meses',
        'instructores' => 1,
        'estado' => 'Activo'
    ],
    [
        'codigo' => 'TEC-001',
        'nombre' => 'Técnico en Construcción',
        'descripcion' => 'Formación técnica en procesos constructivos',
        'nivel' => 'Técnico',
        'duracion' => '24 meses',
        'instructores' => 1,
        'estado' => 'Activo'
    ],
    [
        'codigo' => 'TEC-001',
        'nombre' => 'Técnico en Construcción',
        'descripcion' => 'Formación técnica en procesos constructivos',
        'nivel' => 'Técnico',
        'duracion' => '10 meses',
        'instructores' => 1,
        'estado' => 'Activo'
    ],
    [
        'codigo' => 'TEC-001',
        'nombre' => 'Técnico en Construcción',
        'descripcion' => 'Formación técnica en procesos constructivos',
        'nivel' => 'Tecnólogo',
        'duracion' => '11 meses',
        'instructores' => 1,
        'estado' => 'Activo'
    ],
    [
        'codigo' => 'TEC-001',
        'nombre' => 'Técnico en Construcción',
        'descripcion' => 'Formación técnica en procesos constructivos',
        'nivel' => 'Técnico',
        'duracion' => '12 meses',
        'instructores' => 1,
        'estado' => 'Inactivo'
    ],
    [
        'codigo' => 'TEC-001',
        'nombre' => 'Técnico en Construcción',
        'descripcion' => 'Formación técnica en procesos constructivos',
        'nivel' => 'Técnico',
        'duracion' => '13 meses',
        'instructores' => 1,
        'estado' => 'Activo'
    ],
    [
        'codigo' => 'TEC-001',
        'nombre' => 'Técnico en Construcción',
        'descripcion' => 'Formación técnica en procesos constructivos',
        'nivel' => 'Técnico',
        'duracion' => '14 meses',
        'instructores' => 2,
        'estado' => 'Activo'
    ],
    [
        'codigo' => 'TEC-001',
        'nombre' => 'Técnico en Construcción',
        'descripcion' => 'Formación técnica en procesos constructivos',
        'nivel' => 'Técnico',
        'duracion' => '15 meses',
        'instructores' => 1,
        'estado' => 'Activo'
    ],
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programas de Formación - SENA</title>
    
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
    <!-- Solo importamos global.css del SENA, sin styles.css personalizado -->
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-background text-foreground">
    
    <!-- CONTENIDO PRINCIPAL - Sin header ni sidebar (componentes separados) -->
    <main class="p-8">
        <!-- Título de la página -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-foreground">Programas de Formación</h1>
            <p class="text-muted-foreground mt-1">Gestiona los programas técnicos y tecnológicos</p>
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
                    
                    <!-- Botón nuevo programa -->
                    <button class="bg-primary hover:bg-secondary text-primary-foreground px-4 py-2 rounded-lg flex items-center gap-2 transition-colors font-medium">
                        <i class="fas fa-plus"></i>
                        Nuevo Programa
                    </button>
                    
                    <!-- Filtro desplegable -->
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground pointer-events-none"></i>
                        <select class="pl-10 pr-8 py-2 border border-border rounded-lg appearance-none bg-card text-foreground cursor-pointer focus:outline-none focus:ring-2 focus:ring-ring transition-all">
                            <option>Todos</option>
                            <option>Activos</option>
                            <option>Inactivos</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground text-sm pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <!-- VISTA DE TABLA -->
            <div id="tableView" class="overflow-x-auto">
                <table class="w-full border-collapse">
                    
                    <!-- Encabezados de la tabla -->
                    <thead>
                        <tr class="border-b border-border bg-muted">
                            <th class="text-left py-3 px-4 font-medium text-muted-foreground text-sm">Código</th>
                            <th class="text-left py-3 px-4 font-medium text-muted-foreground text-sm">Programas de Formación</th>
                            <th class="text-left py-3 px-4 font-medium text-muted-foreground text-sm">Nivel</th>
                            <th class="text-left py-3 px-4 font-medium text-muted-foreground text-sm">Duración</th>
                            <th class="text-left py-3 px-4 font-medium text-muted-foreground text-sm">Instructores</th>
                            <th class="text-left py-3 px-4 font-medium text-muted-foreground text-sm">Estado</th>
                            <th class="text-left py-3 px-4 font-medium text-muted-foreground text-sm">Acciones</th>
                        </tr>
                    </thead>
                    
                    <!-- Filas de datos -->
                    <tbody>
                        <?php foreach ($programas as $index => $programa): ?>
                        <tr class="border-b border-border hover:bg-muted transition-colors">
                            
                            <!-- Código del programa -->
                            <td class="py-4 px-4 text-sm font-medium text-foreground">
                                <?php echo $programa['codigo']; ?>
                            </td>
                            
                            <!-- Información del programa con ícono -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <!-- Ícono circular -->
                                    <div class="w-8 h-8 bg-muted rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-graduation-cap text-primary text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-foreground"><?php echo $programa['nombre']; ?></div>
                                        <div class="text-xs text-muted-foreground"><?php echo $programa['descripcion']; ?></div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Badge de nivel (Técnico/Tecnólogo) -->
                            <td class="py-4 px-4">
                                <?php if (strtolower($programa['nivel']) === 'técnico'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#39A93526] text-primary">
                                        <?php echo $programa['nivel']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#3b82f626] text-info">
                                        <?php echo $programa['nivel']; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            
                            <!-- Duración con ícono de reloj -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <i class="far fa-clock"></i>
                                    <?php echo $programa['duracion']; ?>
                                </div>
                            </td>
                            
                            <!-- Cantidad de instructores -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <i class="fas fa-users"></i>
                                    <?php echo $programa['instructores']; ?>
                                </div>
                            </td>
                            
                            <!-- Badge de estado (Activo/Inactivo) -->
                            <td class="py-4 px-4">
                                <?php if (strtolower($programa['estado']) === 'activo'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success">
                                        <?php echo $programa['estado']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#ef444426] text-destructive">
                                        <?php echo $programa['estado']; ?>
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
                                        <button class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground rounded-t-lg transition-colors">
                                            <i class="far fa-eye text-muted-foreground"></i>
                                            Ver detalles
                                        </button>
                                        <button class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground transition-colors">
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
                <?php foreach ($programas as $index => $programa): ?>
                
                <!-- Tarjeta individual del programa -->
                <div class="bg-card border border-border rounded-lg p-6 hover:shadow-md transition-all hover:-translate-y-1">
                    
                    <!-- Header de la tarjeta -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <!-- Ícono del programa -->
                            <div class="w-12 h-12 bg-muted rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-graduation-cap text-primary text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-foreground"><?php echo $programa['nombre']; ?></h3>
                                <p class="text-xs text-muted-foreground"><?php echo $programa['codigo']; ?></p>
                            </div>
                        </div>
                        
                        <!-- Botón de menú en la tarjeta -->
                        <div class="relative">
                            <button 
                                onclick="toggleActionMenu('grid<?php echo $index; ?>')" 
                                class="text-muted-foreground hover:text-foreground p-1 hover:bg-muted rounded transition-colors"
                            >
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            
                            <!-- Menú desplegable de la tarjeta -->
                            <div 
                                id="actionMenugrid<?php echo $index; ?>" 
                                class="hidden absolute right-0 mt-2 w-48 bg-card rounded-lg shadow-lg border border-border z-50"
                            >
                                <button class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground rounded-t-lg transition-colors">
                                    <i class="far fa-eye text-muted-foreground"></i>
                                    Ver detalles
                                </button>
                                <button class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground transition-colors">
                                    <i class="far fa-edit text-muted-foreground"></i>
                                    Editar
                                </button>
                                <button class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground rounded-b-lg transition-colors">
                                    <i class="fas fa-ban text-muted-foreground"></i>
                                    Deshabilitar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción del programa -->
                    <p class="text-sm text-muted-foreground mb-4"><?php echo $programa['descripcion']; ?></p>

                    <!-- Información adicional -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-muted-foreground">Instructor:</span>
                            <span class="text-sm font-medium text-foreground">Juan Guillermo Crespo</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <!-- Badge de nivel -->
                            <?php if (strtolower($programa['nivel']) === 'técnico'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#39A93526] text-primary">
                                    <?php echo $programa['nivel']; ?>
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#3b82f626] text-info">
                                    <?php echo $programa['nivel']; ?>
                                </span>
                            <?php endif; ?>
                            
                            <!-- Duración -->
                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <i class="far fa-clock"></i>
                                <?php echo $programa['duracion']; ?>
                            </div>
                        </div>
                        
                        <!-- Cantidad de instructores -->
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-muted-foreground">Instructor/es</span>
                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                <i class="fas fa-users"></i>
                                <?php echo $programa['instructores']; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Badge de estado y toggle -->
                    <div class="flex items-center justify-between pt-4 border-t border-border">
                        <?php if (strtolower($programa['estado']) === 'activo'): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success">
                                <?php echo $programa['estado']; ?>
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#ef444426] text-destructive">
                                <?php echo $programa['estado']; ?>
                            </span>
                        <?php endif; ?>
                        
                        <!-- Toggle switch -->
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                class="sr-only peer" 
                                <?php echo strtolower($programa['estado']) === 'activo' ? 'checked' : ''; ?>
                            >
                            <div class="w-11 h-6 bg-muted peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-ring rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
    <script src="../../assets/js/programas.js"></script>
</body>
</html>