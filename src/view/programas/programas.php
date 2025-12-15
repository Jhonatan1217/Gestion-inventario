<?php
require_once __DIR__ . '../../../../Config/database.php';

// Final array that will be used by the HTML
$programas = [];

try {
    $sql = "SELECT 
                id_programa, 
                codigo_programa, 
                nombre_programa, 
                nivel_programa, 
                descripcion_programa, 
                duracion_horas, 
                estado 
            FROM programas_formacion";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Fetch raw DB results
    $raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Map DB fields → HTML expected fields
    foreach ($raw as $r) {
        $programas[] = [
            'id_programa' => $r['id_programa'],  // Added id_programa
            'codigo'      => $r['codigo_programa'],
            'nombre'      => $r['nombre_programa'],
            'descripcion' => $r['descripcion_programa'],
            'nivel'       => $r['nivel_programa'],
            'duracion'    => $r['duracion_horas'] . ' horas',
            'instructores'=> 0,
            'estado'      => $r['estado']
        ];
    }

} catch (PDOException $e) {
    die("Error al cargar programas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Programas de Formación - SENA</title>
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
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
    <!-- Import SENA global.css only, without custom styles.css -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>css/globals.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-background text-foreground min-h-screen flex flex-col">
    
    <!-- MAIN CONTENT - Without header or sidebar (separate components) -->
    <main class="p-6 transition-all duration-300"
      style="margin-left: <?= $collapsed ? '70px' : '260px' ?>;">
        <?php
            // include_once __DIR__ . '/../../includes/footer.php';
        ?>
        <!-- Page title -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-foreground">Programas de Formación</h1>
            <p class="text-muted-foreground mt-1">Gestiona los programas técnicos y tecnológicos</p>
        </div>

        <!-- Main container with borders and shadow -->
        <div class="bg-card rounded-lg shadow-sm">
            <!-- Filter bar and actions -->
            <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
                
                <!-- Search bar -->
                <div class="relative flex-1 max-w-md">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground"></i>
                    <input 
                        type="text" 
                        placeholder="Buscar por nombre..." 
                        class="pl-10 pr-4 py-2 w-full border border-border rounded-lg bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-ring transition-all"
                    >
                </div>
                
                <!-- Action buttons -->
                <div class="flex items-center gap-3">
                    
                    <!-- View buttons (table/grid) -->
                    <div class="inline-flex rounded-lg border border-border bg-card shadow-sm overflow-hidden">

                    <!-- Table View -->
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

                    <!-- Card View -->
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
                    
                    <!-- New program button -->
                    <button onclick="openCreateModal()" id="btnNewProgram" class="inline-flex items-center justify-center rounded-md bg-secondary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm hover:opacity-90 gap-2">
                        <i class="fas fa-plus"></i>
                        Nuevo Programa
                    </button>
                    
                    <!-- Dropdown filter -->
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground pointer-events-none"></i>
                        <select id="selectFiltroEstado" class="pl-10 pr-8 py-2 border border-border rounded-lg appearance-none bg-background text-foreground cursor-pointer focus:outline-none focus:ring-2 focus:ring-ring transition-all">
                            <option value="">Todos</option>
                            <option value="1">Activos</option>
                            <option value="0">Inactivos</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- TABLE VIEW -->
            <div id="tableView" class="overflow-x-auto border border-border rounded-lg">
                <table class="w-full border-collapse">
                    
                    <!-- Table headers -->
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
                    
                    <!-- Data rows -->
                    <tbody>
                        <?php foreach ($programas as $index => $programa): ?>
                        <?php $isActive = (isset($programa['estado']) && (strtolower(trim((string)$programa['estado'])) === 'activo' || (string)$programa['estado'] === '1' || $programa['estado'] == 1)); ?>
                        <tr 
                            class="border-b border-border hover:bg-muted transition-colors"
                            data-index="<?php echo $index; ?>"
                            data-id-programa="<?php echo htmlspecialchars($programa['id_programa']); ?>"
                            data-codigo="<?php echo htmlspecialchars($programa['codigo']); ?>"
                            data-nombre="<?php echo htmlspecialchars($programa['nombre']); ?>"
                            data-descripcion="<?php echo htmlspecialchars($programa['descripcion']); ?>"
                            data-nivel="<?php echo htmlspecialchars($programa['nivel']); ?>"
                            data-duracion="<?php echo htmlspecialchars($programa['duracion']); ?>"
                            data-instructores="<?php echo htmlspecialchars($programa['instructores']); ?>"
                            data-estado="<?php echo $isActive ? 1 : 0; ?>"
                        >
                            <!-- Program code -->
                            <td class="py-4 px-4 text-sm font-medium text-foreground">
                                <span class="js-code"><?php echo $programa['codigo']; ?></span>
                            </td>
                            
                            <!-- Program information with icon -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <!-- Circular icon -->
                                    <div class="w-8 h-8 bg-muted rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-graduation-cap text-secondary text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-foreground js-name"><?php echo $programa['nombre']; ?></div>
                                        <div class="text-xs text-muted-foreground js-descripcion"><?php echo $programa['descripcion']; ?></div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Level badge (Técnico/Tecnólogo) -->
                            <td class="py-4 px-4">
                                <span class="js-nivel">
                                <?php if (strtolower($programa['nivel']) === 'técnico'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#39A93526] text-primary">
                                        <?php echo $programa['nivel']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#3b82f626] text-info">
                                        <?php echo $programa['nivel']; ?>
                                    </span>
                                <?php endif; ?>
                                </span>
                            </td>
                            
                            <!-- Duration with clock icon -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <i class="far fa-clock"></i>
                                    <span class="js-duracion"><?php echo $programa['duracion']; ?></span>
                                </div>
                            </td>
                            
                            <!-- Number of instructors -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <i class="fas fa-users"></i>
                                    <span class="js-instructores"><?php echo $programa['instructores']; ?></span>
                                </div>
                            </td>

                            
                            <!-- Status badge (Active/Inactive) -->
                            <td class="py-4 px-4">
                                <span class="js-estado">
                                <?php if ($isActive): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success">
                                        Activo
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400">
                                        Inactivo
                                    </span>
                                <?php endif; ?>
                                </span>
                            </td>
                            
                            <!-- Actions menu -->
                            <td class="py-4 px-4">
                                <div class="relative">
                                    <button onclick="toggleActionMenu(<?php echo $index; ?>)" class="text-muted-foreground hover:text-foreground transition-colors p-2 hover:bg-muted rounded">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div id="actionMenu<?php echo $index; ?>" class="hidden absolute right-0 mt-2 w-48 bg-card rounded-lg shadow-lg border border-border z-50">
                                        <button onclick="openEditModal(<?php echo $index; ?>)" class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground rounded-t-lg transition-colors">
                                            <i class="far fa-edit text-muted-foreground"></i>
                                            Editar
                                        </button>
                                        <button onclick="openViewModal(<?php echo $index; ?>)" class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground transition-colors">
                                            <i class="far fa-eye text-muted-foreground"></i>
                                            Ver detalles
                                        </button>
                                        <button data-action="toggle-estado" class="w-full text-left px-4 py-2 hover:bg-muted flex items-center gap-3 text-sm text-foreground rounded-b-lg transition-colors">
                                            <i class="fas fa-ban text-muted-foreground"></i>
                                            <?php echo $isActive ? 'Deshabilitar' : 'Habilitar'; ?>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- GRID VIEW -->
            <div id="gridView" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">

                <?php foreach ($programas as $index => $programa): ?>
                <?php $isActive = (isset($programa['estado']) && (strtolower(trim((string)$programa['estado'])) === 'activo' || (string)$programa['estado'] === '1' || $programa['estado'] == 1)); ?>
                <div class="bg-card border border-border rounded-lg p-6 hover:shadow-md transition-all hover:-translate-y-1"
                    data-index="<?php echo $index; ?>"
                    data-id-programa="<?php echo htmlspecialchars($programa['id_programa']); ?>"
                    data-codigo="<?php echo htmlspecialchars($programa['codigo']); ?>"
                    data-nombre="<?php echo htmlspecialchars($programa['nombre']); ?>"
                    data-descripcion="<?php echo htmlspecialchars($programa['descripcion']); ?>"
                    data-nivel="<?php echo htmlspecialchars($programa['nivel']); ?>"
                    data-duracion="<?php echo htmlspecialchars($programa['duracion']); ?>"
                    data-instructores="<?php echo htmlspecialchars($programa['instructores']); ?>"
                    data-estado="<?php echo $isActive ? 1 : 0; ?>"
                    <!-- ICONO + TÍTULO + EDIT -->
                    <div class="flex justify-between items-start mb-3">

                        <!-- Info + Icon -->
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 bg-muted rounded-full flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-secondary text-lg"></i>
                            </div>

                            <div>
                                <h3 class="font-semibold text-foreground js-name"><?php echo $programa['nombre']; ?></h3>
                                <p class="text-sm text-muted-foreground js-descripcion"><?php echo $programa['descripcion']; ?></p>
                                <p class="text-xs text-muted-foreground js-code"><?php echo $programa['codigo']; ?></p>
                            </div>
                        </div>

                        <!-- Changed link to button that opens edit modal -->
                        <button onclick="openEditModal(<?php echo $index; ?>)" 
                        class="text-muted-foreground hover:text-foreground transition">
                            <i class="fas fa-edit text-lg"></i>
                        </button>
                    </div>

                    <!-- Level + Duration -->
                    <div class="flex items-center justify-between mb-3">

                        <!-- Nivel -->
                        <div class="flex items-center gap-2">
                            <span class="js-nivel">
                                <?php if (strtolower($programa['nivel']) === 'técnico'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#39A93526] text-primary">
                                        <?php echo $programa['nivel']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#3b82f626] text-info">
                                        <?php echo $programa['nivel']; ?>
                                    </span>
                                <?php endif; ?>
                            </span>

                            <!-- State (badge igual same at the table) -->
                            <?php if ($isActive): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success js-estado">
                                    Activo
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400 js-estado">
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Duration -->
                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                            <i class="far fa-clock"></i>
                            <span class="js-duracion"><?php echo $programa['duracion']; ?></span>
                        </div>
                    </div>

                    <hr class="border-border mb-3">
                    <!-- Instructors + Toggle -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                            <i class="fas fa-users"></i>
                            <span class="js-instructores"><?php echo $programa['instructores']; ?></span>
                        </div>

                        <div class="flex flex-col items-end">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" <?php echo $isActive ? 'checked' : ''; ?>>

                                <div class="w-11 h-6 bg-gray-500/20 rounded-full peer-checked:bg-secondary transition-all"></div>

                                <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-all peer-checked:translate-x-5"></div>
                            </label>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Program edit modal -->
        <div id="editProgramModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/40" onclick="closeEditModal()"></div>
            <div class="relative max-w-lg w-full bg-card rounded-lg shadow-lg border border-border p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Editar Programa</h3>
                    <button onclick="closeEditModal()" class="text-muted-foreground hover:text-foreground"><i class="fas fa-times"></i></button>
                </div>
                <form id="editProgramForm" class="space-y-3">
                    <input type="hidden" id="edit_index">
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1">Código *</label>
                        <input id="edit_codigo" type="text" class="w-full border border-border rounded-[10px] px-3 py-2 bg-card text-foreground" required>
                    </div>
                    <div class="relative">
                        <label class="block text-xs text-muted-foreground mb-1">Nivel *</label>
                        <select id="edit_nivel" class="w-full border border-border rounded-[10px] px-3 py-2 bg-card text-foreground appearance-none">
                            <option>Técnico</option>
                            <option>Tecnólogo</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-muted-foreground mb-1">Nombre del programa *</label>
                        <input id="edit_nombre" type="text" class="w-full border border-border rounded-[10px] px-3 py-2 bg-card text-foreground" required>
                    </div>
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1">Descripción *</label>
                        <textarea id="edit_descripcion" rows="3" class="w-full border border-border rounded-[10px] px-3 py-2 bg-card text-foreground"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-muted-foreground mb-1">Duración *</label>
                            <input id="edit_duracion" type="text" class="w-full border border-border rounded-[10px] px-3 py-2 bg-card text-foreground">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-4">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-border rounded">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-primary bg-secondary-foreground rounded">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- View details modal -->
        <div id="viewProgramModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/40" onclick="closeViewModal()"></div>
            <div class="relative max-w-md w-full bg-card rounded-lg shadow-lg border border-border p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold" id="view_title">Detalles del Programa</h3>
                        <p class="text-xs text-muted-foreground">Modifica la información del programa</p>
                    </div>
                    <button onclick="closeViewModal()" class="text-muted-foreground hover:text-foreground"><i class="fas fa-times"></i></button>
                </div>

                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-[#e6f7ea] rounded-full flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-secondary text-xl"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-foreground" id="view_name">Nombre Programa</div>
                        <div class="text-xs text-muted-foreground" id="view_code">COD-000</div>
                    </div>
                </div>

                <p class="text-sm text-muted-foreground mb-4" id="view_description">Descripción del programa</p>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted-foreground">Nivel:</span>
                        <span id="view_nivel" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#39A93526] text-primary">Técnico</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted-foreground">Duración:</span>
                        <span id="view_duracion" class="text-sm font-medium text-foreground">0 Horas</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted-foreground">Instructor:</span>
                        <span id="view_instructor" class="text-sm font-medium text-foreground">Juan Guillermo Crespo</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-muted-foreground">Estado:</span>
                        <span id="view_estado" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#22c55e26] text-success">Activo</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create New Program Modal (UI only) -->
        <div id="createProgramModal" class="fixed inset-0 z-50 hidden items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/40" onclick="closeCreateModal()"></div>
            <div class="relative max-w-lg w-full bg-card rounded-lg shadow-lg border border-border p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold">Crear Nuevo Programa</h3>
                        <p class="text-xs text-muted-foreground">Complete los datos para registrar un nuevo programa de formación</p>
                    </div>
                    <button onclick="closeCreateModal()" class="text-muted-foreground hover:text-foreground"><i class="fas fa-times"></i></button>
                </div>

                <form id="createProgramForm" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-muted-foreground mb-1">Código *</label>
                            <input id="create_codigo" type="text" class="w-full border border-border rounded-[10px] px-3 py-2 bg-card text-foreground" placeholder="TEC-001">
                        </div>
                        <div>
                            <label class="block text-xs text-muted-foreground mb-1">Nivel *</label>
                            <select id="create_nivel" class="w-full border border-border rounded-[10px] px-3 py-2 bg-card text-foreground">
                                <option value="Tecnico">Técnico</option>
                                <option value="Tecnologo">Tecnólogo</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-muted-foreground mb-1">Nombre del programa *</label>
                        <input id="create_nombre" type="text" class="w-full border border-border rounded-[10px] px-3 py-2 bg-card text-foreground" placeholder="Técnico en Construcción">
                    </div>

                    <div>
                        <label class="block text-xs text-muted-foreground mb-1">Descripción *</label>
                        <textarea id="create_descripcion" rows="4" class="w-full border border-border rounded-[10px] px-3 py-2 bg-card text-foreground" placeholder="Formación técnica de procesos constructivos"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs text-muted-foreground mb-1">Duración *</label>
                        <input id="create_duracion" type="text" class="w-full border border-border rounded-[10px] px-3 py-2 bg-card text-foreground" placeholder="X Horas">
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-4">
                        <button type="button" onclick="closeCreateModal()" class="px-4 py-2 border border-border rounded">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-secondary text-primary-foreground rounded">Crear Programa</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <!-- Changed script src from toggle-view.js to programas.js -->
    <script src="<?= ASSETS_URL ?>js/programas/programas.js"></script>
</body>
</html>