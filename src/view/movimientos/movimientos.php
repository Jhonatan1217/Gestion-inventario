<?php

$collapsed = isset($_GET["coll"]) && $_GET["coll"] == "1";
$sidebarWidth = $collapsed ? "70px" : "260px";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimientos</title>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/globals.css">
</head>
<body>
<main class="p-6 transition-all duration-300"
    style="margin-left: <?= isset($_GET['coll']) && $_GET['coll'] == "1" ? '70px' : '260px' ?>;">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Movimientos</h1>
            <p class="text-muted-foreground">Historial de entradas, salidas y devoluciones de materiales</p>
        </div>
        <div class="flex justify-end items-center gap-2 mt-4 mb-4">
        <div class="inline-flex rounded-lg border border-border bg-card shadow-sm overflow-hidden">
            <!-- Lista -->
            <button
            type="button"
            id="btnVistaTabla"
            class="px-3 py-2 text-xs sm:text-sm flex items-center gap-1 bg-muted text-foreground"
            >
            <!-- Icono lista -->
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            </button>

            <!-- Tarjetas -->
            <button
            type="button"
            id="btnVistaTarjetas"
            class="px-3 py-2 text-xs sm:text-sm flex items-center gap-1 text-muted-foreground">
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
            <!-- BOTÓN ABRIR MODAL -->
            <div>
                <button
                    type="button"
                    onclick="openMovimientoModal()"
                    class="inline-flex items-center justify-center rounded-md bg-secondary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm hover:opacity-90 gap-2">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Agregar Movimiento
                </button>
            </div>
        </div>
    </div>

    <!-- targets -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 ">
        <div class="rounded-xl border border-border bg-card p-8 flex flex-col items-center">
            <div class="flex items-start gap-3">
                <div class="p-3 rounded-2xl bg-gray-100 inline-flex items-center justify-center">
                    <i data-lucide="arrow-up-from-line" class="h-6 w-6 text-[#39A900]"></i>
                </div>
                <div class="flex flex-col justify-center">
                    <p class="text-2xl font-medium text-muted-foreground">4</p>
                    <span class="text-xs text-muted-foreground">Entrada</span>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-border bg-card p-8 flex flex-col items-center">
            <div class="flex items-start gap-3">
                <div class="p-3 rounded-2xl bg-gray-100 inline-flex items-center justify-center">
                    <i data-lucide="arrow-down-to-line" class="h-6 w-6 text-[#FF6A00]"></i>
                </div>
                <div class="flex flex-col">
                    <p class="text-2xl font-medium text-muted-foreground">2</p>
                    <span class="text-xs text-muted-foreground">Salida</span>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card p-8 flex flex-col items-center">
            <div class="flex items-start gap-3">
                <div class="p-3 rounded-2xl bg-gray-100 inline-flex items-center justify-center">
                    <i data-lucide="rotate-ccw" class=" h-6 w-6 text-[#39A900]"></i>
                </div>
                <div class="flex flex-col justify-center">
                    <p class="text-2xl font-medium text-muted-foreground">2</p>
                    <span class="text-xs text-muted-foreground">Devolver</span>
                </div>
            </div>
        </div>
    </div>

    <!-- filters -->
    <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <!-- Input -->
        <div class="relative w-full sm:max-w-xs">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-muted-foreground">
                <i data-lucide="search" class="h-4 w-4"></i>
            </span>

            <input type="text" name="buscar_material" placeholder="Buscar por material..."
                class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
        </div>

        <!-- Select -->
        <div class="flex items-center gap-2">
            <i data-lucide="filter" class="h-4 w-4 text-muted-foreground"></i>

            <div class="relative">
                <select
                    name="filtro_estado"
                    class="appearance-none rounded-lg border border-border bg-background py-2 pl-3 pr-8 text-sm
                    text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                >
                    <option value="">Todos</option>
                    <option value="disponible">Disponibles</option>
                    <option value="agotado">Agotados</option>
                </select>

                <span class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-muted-foreground">
                    <i data-lucide="chevron-down" class="h-4 w-4"></i>
                </span>
            </div>
        </div>
    </div>

    <div id="tableView" class="mt-6 rounded-2xl border border-border bg-card">
        <table class="min-w-full text-sm">
            <!-- CABECERA DE LA TABLA -->
            <thead class="bg-gray-100">
            <tr class="bg-primary/5 text-xs text-muted-foreground border-b border-border">
                <th class="px-4 py-3 text-left font-medium">Fecha/Hora</th>
                <th class="px-4 py-3 text-left font-medium">Tipo</th>
                <th class="px-4 py-3 text-left font-medium">Material</th>
                <th class="px-4 py-3 text-left font-medium">Ficha</th>
                <th class="px-4 py-3 text-left font-medium">Instructor</th>
                <th class="px-4 py-3 text-left font-medium">Bodega</th>
                <th class="px-4 py-3 text-left font-medium">Estado</th>
                <th class="px-4 py-3 text-right font-medium">Acciones</th>
            </tr>
            </thead>

            <!-- CUERPO -->
            <tbody class="divide-y divide-border">
            <!-- FILA DE EJEMPLO -->
            <tr class="hover:bg-muted/60">
                <!-- Fecha -->
                <td class="px-4 py-3 align-top">
                    <div class="flex items-start gap-2">
                        <i data-lucide="calendar" class="h-4 w-4 mt-0.5 text-muted-foreground"></i>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-foreground">2024-11-20</span>
                            <span class="text-xs text-muted-foreground">08:30</span>
                        </div>
                    </div>
                </td>

                <!-- Tipo -->
                <td class="px-4 py-3 align-top">
                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-lime-100 text-lime-700">
                        <i data-lucide="arrow-down-up" class="h-3 w-3"></i>
                        Salida
                    </span>
                </td>

                <!-- Material -->
                <td class="px-4 py-3 align-top">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium">Cemento Gris</span>
                        <span class="text-xs text-muted-foreground">Cantidad: 10</span>
                    </div>
                </td>

                <!-- Ficha -->
                <td class="px-4 py-3 align-top">
                    <span class="inline-flex items-center rounded-md border border-border px-2 py-1 text-xs font-medium">
                        2895664
                    </span>
                </td>

                <!-- Instructor -->
                <td class="px-4 py-3 align-top">
                    <div class="flex items-start gap-2">
                        <i data-lucide="users" class="h-4 w-4 mt-0.5 text-muted-foreground"></i>
                        <span class="text-sm truncate max-w-[180px]">
                            Juan Pablo Hernandez Castro
                        </span>
                    </div>
                </td>

                <!-- Bodega -->
                <td class="px-4 py-3 align-top">
                    <span class="text-sm">Bodega Construcción</span>
                </td>

                <!-- Estado -->
                <td class="px-4 py-3 align-top">
                    <span class="inline-flex items-center rounded-full px-3 py-0.5 text-xs font-medium bg-[#39A90020] text-slate-700">
                        Bueno
                    </span>
                </td>

                <!-- Acciones -->
                <td class="relative px-4 py-3 align-top text-right">
                    <!-- BOTÓN DEL ICONO -->
                    <button
                        type="button"
                        onclick="toggleMenu(this)"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-full hover:bg-muted">
                        <i data-lucide="more-horizontal" class="h-4 w-4"></i>
                    </button>

                    <!-- MENÚ -->
                    <div class="hidden absolute right-0 mt-2 w-40 rounded-xl border border-gray-200 bg-white shadow-lg p-2 z-50 menu-dropdown">
                        <!-- Ver detalle -->
                        <button type="button" class="flex items-center gap-2 w-full text-left px-2 py-2 rounded-lg hover:bg-gray-100">
                            <i data-lucide="eye" class="h-4 w-4"></i>
                            <span>Ver detalle</span>
                        </button>
                        <!-- Editar -->
                        <button type="button" class="flex items-center gap-2 w-full text-left px-2 py-2 rounded-lg hover:bg-gray-100">
                            <i data-lucide="edit" class="h-4 w-4"></i>
                            <span>Editar</span>
                        </button>
                        <!-- Deshabilitar -->
                        <button type="button" class="flex items-center gap-2 w-full text-left px-2 py-2 rounded-lg hover:bg-gray-100">
                            <i data-lucide="power" class="h-4 w-4"></i>
                            <span>Deshabilitar</span>
                        </button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <!-- GRID VIEW (TARJETAS) -->
    <div id="gridView" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
    <!-- CARD -->
    <div class="bg-card border border-border rounded-lg p-6 hover:shadow-md transition-all hover:-translate-y-1 relative">

    <!-- ✅ Botón 3 puntos arriba a la derecha -->
    <div class="absolute top-3 right-3">
        <button
        type="button"
        onclick="toggleMenu(this)"
        class="inline-flex h-8 w-8 items-center justify-center rounded-full hover:bg-muted">
        <i data-lucide="more-horizontal" class="h-4 w-4"></i>
        </button>

        <!-- ✅ Menú -->
        <div class="hidden absolute right-0 mt-2 w-40 rounded-xl border border-gray-200 bg-white shadow-lg p-2 z-50 menu-dropdown">
        <button type="button" class="flex items-center gap-2 w-full text-left px-2 py-2 rounded-lg hover:bg-gray-100">
            <i data-lucide="eye" class="h-4 w-4"></i><span>Ver detalle</span>
        </button>
        <button type="button" class="flex items-center gap-2 w-full text-left px-2 py-2 rounded-lg hover:bg-gray-100">
            <i data-lucide="edit" class="h-4 w-4"></i><span>Editar</span>
        </button>
        <button type="button" class="flex items-center gap-2 w-full text-left px-2 py-2 rounded-lg hover:bg-gray-100">
            <i data-lucide="power" class="h-4 w-4"></i><span>Deshabilitar</span>
        </button>
        </div>
    </div>

    <!-- Header -->
    <div class="flex items-start gap-3 mb-4 pr-10">
        <div class="w-12 h-12 bg-muted rounded-full flex items-center justify-center">
        <i data-lucide="package" class="h-5 w-5 text-muted-foreground text-[#39A900]"></i>
        </div>

        <div>
        <h3 class="font-semibold text-foreground">Cemento Gris</h3>
        <p class="text-sm text-muted-foreground">Cantidad: 10 • 2024-11-20 • 08:30</p> 
        </div>
    </div>

    <!-- Detalles -->
    <div class="space-y-2 text-sm text-muted-foreground">
        <div class="flex items-center gap-2">
        <i data-lucide="id-card" class="h-4 w-4"></i>
        <span class="font-medium text-foreground">Ficha:</span>
        <span class="inline-flex items-center rounded-md border border-border px-2 py-0.5 text-xs font-medium text-foreground">
            2895664
        </span>
        </div>

        <div class="flex items-center gap-2">
        <i data-lucide="users" class="h-4 w-4"></i>
        <span class="font-medium text-foreground">Instructor:</span>
        <span class="truncate">Juan Pablo Hernandez Castro</span>
        </div>

        <div class="flex items-center gap-2">
        <i data-lucide="warehouse" class="h-4 w-4"></i>
        <span class="font-medium text-foreground">Bodega:</span>
        <span class="truncate">Bodega Construcción</span>
        </div>
    </div>

    <hr class="my-4 border-border">

    <!-- Footer -->
    <div class="flex items-center justify-between">
        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-[#39A90020] text-slate-700">
        Bueno
        </span>

        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium bg-lime-100 text-lime-700">
        <i data-lucide="arrow-down-up" class="h-3 w-3"></i>
        Salida
        </span>
    </div>

</div>

    </div>

    <!-- MODAL REGISTRAR MOVIMIENTO -->
    <div id="movimientoModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
        <!-- Fondo clicable para cerrar -->
        <div
            class="absolute inset-0"
            onclick="closeMovimientoModal()"></div>

        <!-- Contenido del modal -->
        <div class="relative mx-4 w-full max-w-2xl rounded-2xl bg-white shadow-xl p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Registrar Movimiento</h2>
                    <p class="text-sm text-gray-500">
                        Registre un nuevo movimiento de inventario
                    </p>
                </div>
                <button
                    type="button"
                    onclick="closeMovimientoModal()"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-full hover:bg-gray-100">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>

            <!-- Tabs Entrada / Salida / Devolución -->
            <div class="mb-6 flex justify-center">
            <div id="tabsMovimiento"
                class="flex w-full max-w-md items-center rounded-full bg-gray-100 p-1 text-sm font-medium shadow-inner">

                <button type="button" data-tipo="entrada"
                        class="tab-mov flex-1 rounded-full py-2 text-center text-gray-600 hover:text-gray-900 transition-all">
                Entrada
                </button>

                <button type="button" data-tipo="salida"
                        class="tab-mov flex-1 rounded-full py-2 text-center text-gray-600 hover:text-gray-900 transition-all">
                Salida
                </button>

                <button type="button" data-tipo="devolucion"
                        class="tab-mov flex-1 rounded-full py-2 text-center text-gray-600 hover:text-gray-900 transition-all">
                Devolución
                </button>

            </div>
            </div>


            <!-- FORM -->
            <form class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- Material -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Material *
                        </label>
                        <select
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
                            <option>Seleccione el material</option>
                        </select>
                    </div>

                    <!-- Cantidad -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Cantidad *
                        </label>
                        <input
                            type="number"
                            min="1"
                            value="1"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]"/>
                    </div>

                    <!-- Ficha (solo salida) -->
                    <div data-field="ficha" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ficha *
                        </label>
                        <select
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
                            <option>Seleccione la ficha</option>
                        </select>
                    </div>

                    <!-- Rae (solo salida) -->
                    <div data-field="rae" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Rae *
                        </label>
                        <select
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
                            <option>Seleccione el RAE</option>
                        </select>
                    </div>

                    <!-- Bodega -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Bodega *
                        </label>
                        <select
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
                            <option>Seleccione la bodega</option>
                        </select>
                    </div>

                    <!-- Estado del material -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Estado del material *
                        </label>
                        <select class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]">
                            <option>Bueno</option>
                            <option>Regular</option>
                            <option>Malo</option>
                        </select>
                    </div>
                </div>

                <!-- Observaciones -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Observaciones
                    </label>
                    <textarea
                        rows="3"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#39A90040] focus:border-[#39A900]"
                        placeholder="Observaciones del movimiento"
                    ></textarea>
                </div>

                <!-- Hidden tipo movimiento -->
                <input type="hidden" name="tipo_movimiento" id="tipoMovimiento" value="entrada">

                <!-- Footer botones -->
                <div class="mt-4 flex items-center justify-end gap-2">
                    <button
                        type="button"
                        onclick="closeMovimientoModal()"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 border border-border">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        id="btnRegistrarMovimiento"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-secondary">
                        Registrar entrada
                    </button>
                </div>
            </form>
        </div>
    </div>

</main>

<script>
document.addEventListener("DOMContentLoaded", function () {

/* ===============================
    ICONOS LUCIDE
=============================== */
if (window.lucide && typeof lucide.createIcons === "function") {
    lucide.createIcons();
}

/* ===============================
    MENÚ DE TRES PUNTOS
=============================== */
window.toggleMenu = function (btn) {
    const dropdown = btn.parentElement.querySelector(".menu-dropdown");
    if (!dropdown) return;
    dropdown.classList.toggle("hidden");
};

document.addEventListener("click", function (e) {
    document.querySelectorAll(".menu-dropdown").forEach(menu => {
    const toggleBtn = menu.previousElementSibling;
    if (!menu.contains(e.target) && !toggleBtn.contains(e.target)) {
        menu.classList.add("hidden");
    }
    });
});

    /* ===============================
    CAMBIO DE VISTA (TABLA / TARJETAS)
    =============================== */
    const btnVistaTabla    = document.getElementById("btnVistaTabla");
    const btnVistaTarjetas = document.getElementById("btnVistaTarjetas");
    const tableView        = document.getElementById("tableView");
    const gridView         = document.getElementById("gridView");

    if (btnVistaTabla && btnVistaTarjetas && tableView && gridView) {

    const setActiveBtn = (btnActive, btnInactive) => {
        btnActive.classList.add("bg-muted", "text-foreground");
        btnActive.classList.remove("text-muted-foreground");

        btnInactive.classList.remove("bg-muted", "text-foreground");
        btnInactive.classList.add("text-muted-foreground");
    };

    const showTable = () => {
        gridView.classList.add("hidden");
        tableView.classList.remove("hidden");
        setActiveBtn(btnVistaTabla, btnVistaTarjetas);
    };

    const showGrid = () => {
        tableView.classList.add("hidden");
        gridView.classList.remove("hidden");
        setActiveBtn(btnVistaTarjetas, btnVistaTabla);

        // Para que lucide pinte iconos de las cards si aparecen nuevos
        if (window.lucide && typeof lucide.createIcons === "function") {
        lucide.createIcons();
        }
    };

    btnVistaTabla.addEventListener("click", showTable);
    btnVistaTarjetas.addEventListener("click", showGrid);

    // ✅ Vista inicial: tabla
    showTable();

    } else {
    console.warn("No se puede alternar vista. Falta algún ID:", {
        btnVistaTabla: !!btnVistaTabla,
        btnVistaTarjetas: !!btnVistaTarjetas,
        tableView: !!tableView,
        gridView: !!gridView
    });
    }


/* ===============================
    TABS MOVIMIENTO (MODAL)
=============================== */
const labelsPorTipo = {
    entrada: 'Registrar entrada',
    salida: 'Registrar salida',
    devolucion: 'Registrar devolución',
};

function initTabsMovimiento() {
    const tabsWrap = document.getElementById("tabsMovimiento");
    if (!tabsWrap) return;

    const tabs       = tabsWrap.querySelectorAll(".tab-mov");
    const hiddenTipo = document.getElementById('tipoMovimiento');
    const btnSubmit  = document.getElementById('btnRegistrarMovimiento');
    const entradaBtn = tabsWrap.querySelector('[data-tipo="entrada"]');

    const setActive = (btn) => {
    tabs.forEach(t => {
        t.classList.remove("bg-white", "shadow", "text-gray-900");
        t.classList.add("text-gray-600");
    });

    btn.classList.add("bg-white", "shadow", "text-gray-900");
    btn.classList.remove("text-gray-600");

    const tipo = btn.dataset.tipo;

    if (hiddenTipo) hiddenTipo.value = tipo;
    if (btnSubmit)  btnSubmit.textContent = labelsPorTipo[tipo];

    const filaFicha = document.querySelector('[data-field="ficha"]');
    const filaRae   = document.querySelector('[data-field="rae"]');

    if (filaFicha && filaRae) {
        if (tipo === 'salida' || tipo === 'devolucion') {
        filaFicha.classList.remove('hidden');
        filaRae.classList.remove('hidden');
        } else {
        filaFicha.classList.add('hidden');
        filaRae.classList.add('hidden');
        }
    }
    };

    if (entradaBtn) setActive(entradaBtn);

    tabs.forEach(btn => {
    btn.onclick = () => setActive(btn);
    });
}

window.initTabsMovimiento = initTabsMovimiento;
});

/* ===============================
ABRIR / CERRAR MODAL
=============================== */
function openMovimientoModal() {
const modal = document.getElementById('movimientoModal');
if (!modal) return;

modal.classList.remove('hidden');
modal.classList.add('flex');
document.body.classList.add('overflow-hidden');

if (window.initTabsMovimiento) {
    window.initTabsMovimiento();
}
}

function closeMovimientoModal() {
const modal = document.getElementById('movimientoModal');
if (!modal) return;

modal.classList.add('hidden');
modal.classList.remove('flex');
document.body.classList.remove('overflow-hidden');
}

/* ===============================
CERRAR CON ESC
=============================== */
document.addEventListener('keydown', function (e) {
if (e.key === 'Escape') {
    closeMovimientoModal();
}
});
</script>



</body>
</html>
