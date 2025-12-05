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
    <meta charset="UTF-8" />
    <title>Gestión Bodegas</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Global tokens (colores, radios, etc.) -->
    <link rel="stylesheet" href="../../assets/css/globals.css" />

    <!-- ========== ESTILOS ESPECÍFICOS DEL MÓDULO BODEGAS ========== -->
    <style>
      /* ========== BUSCADOR ========== */
      .bodegas-search-box {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-xl);
        padding: 10px 14px;
        min-width: 320px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
      }

      .bodegas-search-icon {
        width: 18px;
        height: 18px;
        color: #666;
      }

      .bodegas-search-input {
        border: none;
        outline: none;
        background: transparent;
        width: 100%;
        font-size: 0.9rem;
      }

      /* ========== SWITCH LISTA / GRID ========== */
      .bodegas-view-switch {
        display: inline-flex;
        border-radius: 999px;
        border: 1px solid var(--border);
        overflow: hidden;
        background: var(--card);
      }

      .bodegas-switch-btn {
        width: 48px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4b5563;
        transition: background 0.15s, color 0.15s;
      }

      .bodegas-switch-btn.active {
        background: color-mix(in srgb, var(--primary) 8%, transparent);
        color: var(--foreground);
      }

      .bodegas-switch-btn:hover {
        background: #f3f4f6;
      }

      /* ========== BOTÓN NUEVA BODEGA ========== */
      #btnNuevaBodega {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        border-radius: 999px;
        background: var(--primary);
        color: var(--primary-foreground);
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
      }

      #btnNuevaBodega i {
        width: 18px;
        height: 18px;
      }

      /* ========== FILTRO ========== */
      .bodegas-filter-icon {
        width: 18px;
        height: 18px;
        color: #4b5563;
      }

      .bodegas-filter-container {
        position: relative;
        display: flex;
        align-items: center;
        background: var(--card);
        border-radius: var(--radius-xl);
        border: 1px solid var(--border);
        padding: 6px 14px;
        min-width: 140px;
      }

      .bodegas-filter-select {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        font-size: 0.9rem;
        color: #4b5563;
        appearance: none;
      }

      .bodegas-filter-arrow {
        position: absolute;
        right: 10px;
        width: 16px;
        height: 16px;
        color: #6b7280;
      }

      /* ========== TABLA LISTA ========== */
      .bodegas-table-wrapper {
        background: var(--card);
        border-radius: var(--radius-xl);
        border: 1px solid var(--border);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        overflow: hidden;
      }

      .bodegas-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
      }

      .bodegas-table thead {
        background: #f4f6f9;
      }

      .bodegas-th {
        padding: 12px 16px;
        font-weight: 600;
        color: #4b5563;
        text-align: left;
      }

      .bodegas-td {
        padding: 12px 16px;
        border-top: 1px solid var(--border);
      }

      .bodegas-row:hover {
        background: #f9fafb;
      }

      /* Icono bodega pequeño */
      .bodegas-icon {
        width: 26px;
        height: 26px;
        border-radius: 10px;
        background: color-mix(in srgb, var(--primary) 12%, transparent);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .bodegas-icon svg {
        width: 14px;
        height: 14px;
      }

      /* TAGS */
      .bodegas-tag-soft {
        padding: 3px 10px;
        border-radius: 999px;
        background: #f3f4f6;
        font-size: 0.78rem;
      }

      /* ESTADO */
      .bodegas-tag-status {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 500;
      }

      .bodegas-tag-status-active {
        background: #d1fae5;
        color: #166534;
      }

      .bodegas-tag-status-inactive {
        background: #fee2e2;
        color: #b91c1c;
      }

      /* BOTÓN 3 PUNTOS */
      .bodegas-btn-dots {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s;
      }

      .bodegas-btn-dots:hover {
        background: #f3f4f6;
      }

      /* ========== TARJETAS GRID ========== */
      .bodegas-card {
        background: var(--card);
        border-radius: var(--radius-xl);
        border: 1px solid var(--border);
        padding: 16px 18px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        display: flex;
        flex-direction: column;
        gap: 8px;
      }

      .bodegas-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
      }

      .bodegas-icon-lg {
        width: 36px;
        height: 36px;
        border-radius: 14px;
        background: color-mix(in srgb, var(--primary) 18%, transparent);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .bodegas-icon-lg svg {
        width: 18px;
        height: 18px;
      }

      .bodegas-card-main {
        margin-top: 4px;
      }

      .bodegas-card-title {
        font-size: 1rem;
        font-weight: 600;
      }

      .bodegas-card-id {
        font-size: 0.8rem;
        color: #6b7280;
      }

      .bodegas-card-location {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
        font-size: 0.88rem;
        color: #4b5563;
      }

      .bodegas-icon-location {
        width: 16px;
        height: 16px;
        color: #6b7280;
      }

      .bodegas-card-tags {
        display: flex;
        gap: 8px;
        margin-top: 8px;
      }

      .bodegas-chip {
        padding: 3px 9px;
        font-size: 0.78rem;
        border-radius: 999px;
      }

      .bodegas-chip-blue {
        background: #e0edff;
        color: #1d4ed8;
      }

      .bodegas-chip-gray {
        background: #f3f4f6;
        color: #4b5563;
      }

      .bodegas-card-divider {
        margin-top: 10px;
        margin-bottom: 8px;
        border: none;
        border-top: 1px solid var(--border);
      }

      .bodegas-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .bodegas-materials {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.86rem;
        color: #4b5563;
      }

      .bodegas-icon-material {
        width: 16px;
        height: 16px;
        color: #6b7280;
      }

      .bodegas-estado {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.86rem;
        font-weight: 500;
      }

      /* ========== SWITCH (solo en GRID) ========== */
      .bodegas-switch {
        position: relative;
        width: 40px;
        height: 20px;
      }

      .bodegas-switch input {
        opacity: 0;
        width: 0;
        height: 0;
      }

      .bodegas-slider {
        position: absolute;
        inset: 0;
        background: #d1d5db;
        border-radius: 999px;
        transition: 0.25s;
      }

      .bodegas-slider::before {
        content: "";
        position: absolute;
        height: 16px;
        width: 16px;
        left: 2px;
        top: 2px;
        background: white;
        border-radius: 999px;
        transition: 0.25s;
      }

      .bodegas-switch input:checked + .bodegas-slider {
        background: var(--primary);
      }

      .bodegas-switch input:checked + .bodegas-slider::before {
        transform: translateX(18px);
      }

      .bodegas-estado-text {
        font-size: 0.86rem;
      }

      /* ========== MENÚ CONTEXTUAL ========== */
      .bodegas-context-menu {
        position: absolute;
        background: var(--card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        padding: 6px 0;
        width: 200px;
        z-index: 50;
      }

      .bodegas-context-menu.hidden {
        display: none;
      }

      .bodegas-ctx-btn {
        width: 100%;
        padding: 8px 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.9rem;
        color: var(--foreground);
      }

      .bodegas-ctx-btn i {
        width: 18px;
        height: 18px;
      }

      .bodegas-ctx-btn:hover {
        background: #f3f4f6;
      }

      /* ========== MODALES (CREAR + DETALLE + EDITAR) ========== */
      .bodegas-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 60;
      }

      .bodegas-modal.hidden {
        display: none;
      }

      .bodegas-modal-content {
        background: var(--card);
        border-radius: var(--radius-xl);
        padding: 24px;
        width: 440px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);
      }

      .bodegas-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .bodegas-modal-title {
        font-size: 1.25rem;
        font-weight: 600;
      }

      .bodegas-modal-subtitle {
        margin-top: 4px;
        font-size: 0.9rem;
        color: var(--muted-foreground);
      }

      .bodegas-btn-close {
        padding: 4px;
        border-radius: 8px;
      }

      .bodegas-btn-close:hover {
        background: #f3f3f3;
      }

      .bodegas-modal-label {
        font-size: 0.9rem;
        font-weight: 500;
      }

      .bodegas-modal-input,
      .bodegas-modal-select {
        width: 100%;
        border: 1px solid var(--border);
        background: var(--card);
        border-radius: var(--radius-lg);
        padding: 8px 12px;
        margin-top: 3px;
        font-size: 0.9rem;
      }

      .bodegas-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 18px;
      }

      .bodegas-btn-cancelar {
        padding: 8px 18px;
        background: #f3f4f6;
        border-radius: var(--radius-lg);
      }

      .bodegas-btn-cancelar:hover {
        background: #e5e7eb;
      }

      .bodegas-btn-confirm {
        padding: 8px 22px;
        border-radius: var(--radius-lg);
        background: var(--primary);
        color: var(--primary-foreground);
        font-weight: 500;
      }

      /* ===== DETALLE BODEGA ===== */
      .bodegas-detail-modal {
        width: 460px;
      }

      .bodegas-detail-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 16px;
        margin-bottom: 12px;
      }

      .bodegas-detail-title h3 {
        font-size: 1.05rem;
        font-weight: 600;
      }

      .bodegas-detail-body {
        margin-top: 8px;
        display: flex;
        flex-direction: column;
        gap: 10px;
      }

      .bodegas-detail-row {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.92rem;
      }

      .bodegas-detail-label {
        min-width: 110px;
        font-weight: 500;
      }
    </style>

    <!-- JS del módulo -->
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
    <div class="flex justify-between items-start mb-6 gap-4">

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
                    <select id="bodegasFilter" class="bodegas-filter-select">
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
                        <?php $activo = $b[5] === "Activo"; ?>

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
                            <span class="bodegas-estado-text">
                                <?= $estadoActivo ? "Activa" : "Inactiva" ?>
                            </span>

                            <label class="bodegas-switch">
                                <input
                                    type="checkbox"
                                    data-id="<?= $b[0] ?>"
                                    <?= $estadoActivo ? "checked" : "" ?>
                                >
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
            <i data-lucide="eye"></i> <span class="bodegas-ctx-text">Ver detalles</span>
        </button>
        <button class="bodegas-ctx-btn" data-action="editar">
            <i data-lucide="square-pen"></i> <span class="bodegas-ctx-text">Editar</span>
        </button>
        <button class="bodegas-ctx-btn" data-action="deshabilitar">
            <i data-lucide="power"></i> <span class="bodegas-ctx-text">Deshabilitar</span>
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
