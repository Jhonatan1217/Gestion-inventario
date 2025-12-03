<?php
// Datos de ejemplo
$bodegas = [
    [1, "Bodega Principal - Eléctrico", "Eléctrico", "Bloque A, Piso 1", "Bodega", "Activo", 1],
    [2, "Bodega Construcción", "Construcción", "Bloque A, Piso 2", "Bodega", "Activo", 3],
    [3, "Sub-bodega Sanitario", "Sanitario", "Bloque B, Piso 1", "Sub-Bodega", "Activo", 4],
    [4, "Bodega Herramientas", "Herramientas", "Bloque C, Piso 1", "Bodega", "Activo", 2],
    [5, "Ejemplo", "Ejemplo", "Bloque A, Piso 3", "Bodega", "Inactivo", 1],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Bodegas</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Estilos -->
    <link rel="stylesheet" href="../../assets/css/globals.css">
    <link rel="stylesheet" href="../../assets/css/bodegas.css">

    <!-- JS -->
    <script src="../../assets/js/bodegas.js" defer></script>
</head>

<body class="bg-background text-foreground p-10">

    <!-- Título -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Gestión Bodegas</h1>
        <p class="text-sm text-muted-foreground">
            Administra las bodegas y sub-bodegas del inventario
        </p>
    </div>

    <!-- CONTROLES SUPERIORES -->
    <div class="flex justify-between items-start mb-6">

        <!-- Buscador -->
        <div class="bodegas-search-box">
            <i data-lucide="search" class="bodegas-search-icon"></i>
            <input
                type="text"
                placeholder="Buscar por nombre o ID"
                class="bodegas-search-input"
            />
        </div>

        <div class="flex flex-col gap-3 items-end">

            <div class="flex items-center gap-3">

                <!-- Switch Lista/Grid -->
                <div class="bodegas-view-switch">
                    <button class="bodegas-switch-btn active" data-view="list">
                        <i data-lucide="list"></i>
                    </button>
                    <button class="bodegas-switch-btn" data-view="grid">
                        <i data-lucide="layout-grid"></i>
                    </button>
                </div>

                <!-- Nueva Bodega -->
                <button id="btnNuevaBodega">
                    <i data-lucide="plus"></i>
                    Nueva Bodega
                </button>
            </div>

            <!-- Filtro -->
            <div class="flex items-center gap-2">
                <i data-lucide="filter" class="bodegas-filter-icon"></i>

                <div class="bodegas-filter-container">
                    <select class="bodegas-filter-select">
                        <option value="Todos">Todos</option>
                        <option value="Activo">Activos</option>
                        <option value="Inactivo">Inactivos</option>
                    </select>
                    <i data-lucide="chevron-down" class="bodegas-filter-arrow"></i>
                </div>
            </div>

        </div>
    </div>

    <!-- ========= VISTA LISTA ========= -->
    <div id="view-list">
        <div class="bodegas-table-wrapper">
            <table class="bodegas-table">
                <thead>
                    <tr>
                        <th class="bodegas-th">ID</th>
                        <th class="bodegas-th">Nombre</th>
                        <th class="bodegas-th">Clasificación</th>
                        <th class="bodegas-th">Ubicación</th>
                        <th class="bodegas-th">Tipo</th>
                        <th class="bodegas-th">Estado</th>
                        <th class="bodegas-th">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($bodegas as $b): ?>
                        <?php $estadoClass = $b[5] === "Activo" ? "bodegas-tag-status-active" : "bodegas-tag-status-inactive"; ?>

                        <tr class="bodegas-row">
                            <td class="bodegas-td">#<?= $b[0] ?></td>

                            <td class="bodegas-td">
                                <div class="flex items-center gap-2">
                                    <div class="bodegas-icon">
                                        <i data-lucide="warehouse"></i>
                                    </div>
                                    <?= htmlspecialchars($b[1]) ?>
                                </div>
                            </td>

                            <td class="bodegas-td">
                                <span class="bodegas-tag-soft"><?= htmlspecialchars($b[2]) ?></span>
                            </td>

                            <td class="bodegas-td">
                                <i data-lucide="map-pin" class="w-4 h-4 text-foreground/70 inline-block"></i>
                                <?= htmlspecialchars($b[3]) ?>
                            </td>

                            <td class="bodegas-td">
                                <span class="bodegas-tag-soft"><?= htmlspecialchars($b[4]) ?></span>
                            </td>

                            <td class="bodegas-td">
                                <span class="bodegas-tag-status <?= $estadoClass ?>">
                                    <?= htmlspecialchars($b[5]) ?>
                                </span>
                            </td>

                            <td class="bodegas-td">
                                <button
                                    class="bodegas-btn-dots"
                                    data-id="<?= $b[0] ?>"
                                    data-nombre="<?= htmlspecialchars($b[1]) ?>"
                                    data-clasificacion="<?= htmlspecialchars($b[2]) ?>"
                                    data-ubicacion="<?= htmlspecialchars($b[3]) ?>"
                                    data-tipo="<?= htmlspecialchars($b[4]) ?>"
                                    data-estado="<?= htmlspecialchars($b[5]) ?>"
                                >
                                    <i data-lucide="more-horizontal"></i>
                                </button>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========= VISTA GRID ========= -->
    <div id="view-grid" class="hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            <?php foreach ($bodegas as $b):
                $estadoActivo = $b[5] === "Activo";
            ?>
                <div class="bodegas-card">

                    <div class="bodegas-card-header">
                        <div class="bodegas-icon-lg">
                            <i data-lucide="warehouse"></i>
                        </div>

                        <button
                            class="bodegas-btn-dots"
                            data-id="<?= $b[0] ?>"
                            data-nombre="<?= htmlspecialchars($b[1]) ?>"
                            data-clasificacion="<?= htmlspecialchars($b[2]) ?>"
                            data-ubicacion="<?= htmlspecialchars($b[3]) ?>"
                            data-tipo="<?= htmlspecialchars($b[4]) ?>"
                            data-estado="<?= htmlspecialchars($b[5]) ?>"
                        >
                            <i data-lucide="more-horizontal"></i>
                        </button>
                    </div>

                    <div class="bodegas-card-main">
                        <h2 class="bodegas-card-title"><?= htmlspecialchars($b[1]) ?></h2>
                        <p class="bodegas-card-id">ID: <?= $b[0] ?></p>
                    </div>

                    <div class="bodegas-card-location">
                        <i data-lucide="map-pin" class="bodegas-icon-location"></i>
                        <span><?= htmlspecialchars($b[3]) ?></span>
                    </div>

                    <div class="bodegas-card-tags">
                        <span class="bodegas-chip bodegas-chip-blue"><?= htmlspecialchars($b[2]) ?></span>
                        <span class="bodegas-chip bodegas-chip-gray"><?= htmlspecialchars($b[4]) ?></span>
                    </div>

                    <hr class="bodegas-card-divider">

                    <div class="bodegas-card-footer">
                        <div class="bodegas-materials">
                            <i data-lucide="package" class="bodegas-icon-material"></i>
                            <?= $b[6] ?> Materiales
                        </div>

                        <div class="bodegas-estado">
                            <span class="bodegas-estado-text"><?= $estadoActivo ? "Activa" : "Inactiva" ?></span>

                            <label class="bodegas-switch">
                                <input type="checkbox" <?= $estadoActivo ? "checked" : "" ?>>
                                <span class="bodegas-slider"></span>
                            </label>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ========= MENÚ CONTEXTUAL ========= -->
    <div id="context-menu" class="bodegas-context-menu hidden">
        <button class="bodegas-ctx-btn" data-action="ver">
            <i data-lucide="eye"></i> Ver detalles
        </button>
        <button class="bodegas-ctx-btn" data-action="editar">
            <i data-lucide="square-pen"></i> Editar
        </button>
        <button class="bodegas-ctx-btn" data-action="deshabilitar">
            <i data-lucide="power"></i> Deshabilitar
        </button>
    </div>

    <!-- ========= MODAL CREAR BODEGA ========= -->
    <div id="modalCrear" class="bodegas-modal hidden">
        <div class="bodegas-modal-content">

            <div class="bodegas-modal-header">
                <h2 class="bodegas-modal-title">Crear Nueva Bodega</h2>
                <button id="cerrarModal" class="bodegas-btn-close">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <p class="bodegas-modal-subtitle">
                Complete los datos para registrar una nueva bodega
            </p>

            <form class="bodegas-modal-form">

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="bodegas-modal-label">ID *</label>
                        <input type="text" class="bodegas-modal-input" placeholder="C-1">
                    </div>

                    <div>
                        <label class="bodegas-modal-label">Tipo *</label>
                        <select class="bodegas-modal-select">
                            <option>Bodega</option>
                            <option>Sub-Bodega</option>
                        </select>
                    </div>

                    <div>
                        <label class="bodegas-modal-label">Clasificación *</label>
                        <select class="bodegas-modal-select">
                            <option>Eléctrico</option>
                            <option>Construcción</option>
                            <option>Sanitario</option>
                            <option>Herramientas</option>
                            <option>Ejemplo</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="bodegas-modal-label">Nombre de Bodega *</label>
                    <input type="text" class="bodegas-modal-input" placeholder="Ej: Bodega Principal - Eléctrico">
                </div>

                <div class="mt-3">
                    <label class="bodegas-modal-label">Ubicación *</label>
                    <input type="text" class="bodegas-modal-input" placeholder="Ej: Bloque A, Piso 1">
                </div>

                <div class="bodegas-modal-footer">
                    <button type="button" id="cancelarModal" class="bodegas-btn-cancelar">Cancelar</button>
                    <button type="button" class="bodegas-btn-confirm">Crear Bodega</button>
                </div>

            </form>
        </div>
    </div>

    <!-- ========= MODAL EDITAR BODEGA ========= -->
    <div id="modalEditar" class="bodegas-modal hidden">
        <div class="bodegas-modal-content">

            <div class="bodegas-modal-header">
                <h2 class="bodegas-modal-title">Editar Bodega</h2>
                <button id="cerrarEditar" class="bodegas-btn-close">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <p class="bodegas-modal-subtitle">
                Modifica la información de la bodega
            </p>

            <form class="bodegas-modal-form">

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="bodegas-modal-label">ID *</label>
                        <input id="editId" type="text" class="bodegas-modal-input">
                    </div>

                    <div>
                        <label class="bodegas-modal-label">Tipo *</label>
                        <select id="editTipo" class="bodegas-modal-select">
                            <option>Bodega</option>
                            <option>Sub-Bodega</option>
                        </select>
                    </div>

                    <div>
                        <label class="bodegas-modal-label">Clasificación *</label>
                        <select id="editClasificacion" class="bodegas-modal-select">
                            <option>Eléctrico</option>
                            <option>Construcción</option>
                            <option>Sanitario</option>
                            <option>Herramientas</option>
                            <option>Ejemplo</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="bodegas-modal-label">Nombre de Bodega *</label>
                    <input id="editNombre" type="text" class="bodegas-modal-input">
                </div>

                <div class="mt-3">
                    <label class="bodegas-modal-label">Ubicación *</label>
                    <input id="editUbicacion" type="text" class="bodegas-modal-input">
                </div>

                <div class="bodegas-modal-footer">
                    <button type="button" id="cancelarEditar" class="bodegas-btn-cancelar">
                        Cancelar
                    </button>
                    <button type="button" id="guardarEditar" class="bodegas-btn-confirm">
                        Guardar cambios
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- ========= MODAL DETALLES BODEGA ========= -->
    <div id="modalDetalle" class="bodegas-modal hidden">
        <div class="bodegas-modal-content bodegas-detail-modal">

            <div class="bodegas-modal-header">
                <h2 class="bodegas-modal-title">Detalles de la bodega</h2>
                <button id="cerrarDetalle" class="bodegas-btn-close">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <div class="bodegas-detail-header">
                <div class="bodegas-icon-lg">
                    <i data-lucide="warehouse"></i>
                </div>
                <div class="bodegas-detail-title">
                    <h3 id="detalleNombre" class="bodegas-card-title"></h3>
                    <p class="bodegas-card-id">ID: <span id="detalleId"></span></p>
                </div>
            </div>

            <div class="bodegas-detail-body">
                <div class="bodegas-detail-row">
                    <span class="bodegas-detail-label">Clasificación:</span>
                    <span id="detalleClasificacion" class="bodegas-chip bodegas-chip-blue"></span>
                </div>

                <div class="bodegas-detail-row">
                    <span class="bodegas-detail-label">Tipo:</span>
                    <span id="detalleTipo" class="bodegas-chip bodegas-chip-gray"></span>
                </div>

                <div class="bodegas-detail-row">
                    <span class="bodegas-detail-label">Ubicación:</span>
                    <span id="detalleUbicacion"></span>
                </div>

                <div class="bodegas-detail-row">
                    <span class="bodegas-detail-label">Estado:</span>
                    <span id="detalleEstado" class="bodegas-tag-status bodegas-tag-status-active"></span>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
