<?php
// ====================
//  MÓDULO SOLICITUDES 
// ====================

$collapsed = isset($_GET["coll"]) && $_GET["coll"] == "1";
$sidebarWidth = $collapsed ? "70px" : "260px";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes de Material</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Global Styles -->
    <link rel="stylesheet" href="src/assets/css/globals.css">

    <!-- Estilos del módulo -->
    <link rel="stylesheet" href="src/assets/css/solicitudes/solicitudes.css">
</head>

<body class="bg-background text-foreground">

<main class="transition-all duration-300 px-6 lg:px-10 py-6"
      style="margin-left: <?= $sidebarWidth ?>;">

    <!-- ===============================
         TÍTULO + BOTÓN NUEVA SOLICITUD
    ==================================== -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">
                Solicitudes de Material
            </h1>
            <p class="text-sm text-muted-foreground">
                Gestiona las solicitudes de materiales de formación
            </p>
        </div>

        <button id="sol-btn-nueva"
                class="sol-btn-primary inline-flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nueva Solicitud
        </button>
    </div>

    <!-- ============================================
         TARJETAS DE RESUMEN
         (valores iniciales, JS los reemplaza)
    ============================================= -->
    <div class="sol-resumen-grid">

        <div class="sol-resumen-card">
            <div class="sol-resumen-icono pendientes">
                <i data-lucide="clock"></i>
            </div>
            <div>
                <div class="sol-resumen-numero">0</div>
                <div class="sol-resumen-label">Pendientes</div>
            </div>
        </div>

        <div class="sol-resumen-card">
            <div class="sol-resumen-icono aprobadas">
                <i data-lucide="check-circle"></i>
            </div>
            <div>
                <div class="sol-resumen-numero">0</div>
                <div class="sol-resumen-label">Aprobadas</div>
            </div>
        </div>

        <div class="sol-resumen-card">
            <div class="sol-resumen-icono entregadas">
                <i data-lucide="package-check"></i>
            </div>
            <div>
                <div class="sol-resumen-numero">0</div>
                <div class="sol-resumen-label">Entregadas</div>
            </div>
        </div>

        <div class="sol-resumen-card">
            <div class="sol-resumen-icono rechazadas">
                <i data-lucide="x-circle"></i>
            </div>
            <div>
                <div class="sol-resumen-numero">0</div>
                <div class="sol-resumen-label">Rechazadas</div>
            </div>
        </div>

    </div>

    <!-- ============================================
         FILTROS
    ============================================= -->
    <div class="sol-filtros-wrapper">
        <button class="sol-filtro-btn sol-filtro-btn-activo" data-filtro="todas">
            Todas (0)
        </button>
        <button class="sol-filtro-btn" data-filtro="pendiente">
            Pendientes (0)
        </button>
        <button class="sol-filtro-btn" data-filtro="aprobada">
            Aprobadas (0)
        </button>
        <button class="sol-filtro-btn" data-filtro="entregada">
            Entregadas (0)
        </button>
        <button class="sol-filtro-btn" data-filtro="rechazada">
            Rechazadas (0)
        </button>
    </div>

    <!-- GRID -->
    <div id="sol-cards"
         class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mt-4">
    </div>

    <div id="sol-pagination" class="flex justify-center gap-2 mt-6"></div>

</main>

<!-- ============================================================
     MODAL – CREAR SOLICITUD (OBLIGATORIO)
============================================================ -->
<div id="sol-modal" class="sol-modal-overlay">
    <div class="sol-modal-box">

        <!-- HEADER -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-xl font-semibold">Nueva Solicitud</h2>
                <p class="text-sm text-muted-foreground">
                    Registre una nueva solicitud de materiales
                </p>
            </div>

            <button id="sol-modal-cerrar"
                    class="p-2 hover:bg-gray-100 rounded-xl">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- PASO 1 -->
        <div id="sol-paso-1" class="space-y-4">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Programa *</label>
                    <select class="input-siga w-full">
                        <option>ADSO</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">RAE *</label>
                    <select class="input-siga w-full">
                        <option>2950 Realizar muros de...</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-sm font-medium">Ficha *</label>
                <select class="input-siga w-full">
                    <option>2845765</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-medium">Observaciones</label>
                <textarea class="input-siga w-full" rows="3"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button class="sol-btn-secondary" id="sol-btn-cancelar">
                    Cancelar
                </button>
                <button id="sol-btn-ir-paso-2" class="sol-btn-primary">
                    Siguiente
                </button>
            </div>
        </div>

        <!-- PASO 2 -->
        <div id="sol-paso-2" class="hidden space-y-4">

            <div>
                <label class="text-sm font-medium">Materiales *</label>

                <div class="flex gap-3 mt-2">
                    <select class="input-siga flex-1">
                        <option>Seleccione el material</option>
                    </select>

                    <input type="number" class="input-siga w-20" value="1">

                    <button class="sol-btn-secondary">
                        Agregar
                    </button>
                </div>
            </div>

            <div class="border rounded-xl p-4 min-h-[120px] text-sm text-muted-foreground">
                Aquí se listarán los materiales agregados
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button class="sol-btn-secondary" id="sol-btn-volver">
                    Cancelar
                </button>
                <button id="sol-btn-guardar" class="sol-btn-primary">
                    Crear Solicitud
                </button>
            </div>
        </div>

    </div>
</div>

<!-- JS -->
<script src="src/assets/js/solicitudes/solicitudes.js"></script>
<script>
    lucide.createIcons();
</script>

</body>
</html>
