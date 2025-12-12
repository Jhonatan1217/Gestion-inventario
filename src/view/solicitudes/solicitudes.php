<?php
// ========================
//  SOLICITUDES – PHP VIEW
// ========================
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Solicitudes de Material</title>

<!-- Tailwind -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

<!-- Global Styles -->
<link rel="stylesheet" href="../../assets/css/globals.css">

<!-- Extra CSS (opcional) -->
<link rel="stylesheet" href="../../assets/css/solicitudes/solicitudes.css">

</head>

<body class="bg-background text-foreground">

<main class="px-6 lg:px-10 py-6 fade-in">

    <!-- ===============================
          TÍTULO
    ================================ -->
    <h1 class="text-2xl font-semibold tracking-tight">Solicitudes de Material</h1>
    <p class="text-sm text-muted-foreground">
        Gestiona las solicitudes de materiales de formación
    </p>

    <!-- ===============================
          TARJETAS DE ESTADO
    ================================ -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">

        <div class="card-solicitud-status">
            <p class="text-4xl font-bold" id="countPendientes">0</p>
            <p class="text-sm text-muted-foreground">Pendientes</p>
        </div>

        <div class="card-solicitud-status">
            <p class="text-4xl font-bold" id="countAprobadas">0</p>
            <p class="text-sm text-muted-foreground">Aprobadas</p>
        </div>

        <div class="card-solicitud-status">
            <p class="text-4xl font-bold" id="countRechazadas">0</p>
            <p class="text-sm text-muted-foreground">Rechazadas</p>
        </div>
    </div>

    <!-- ===============================
          FILTROS Y BOTÓN
    ================================ -->
    <div class="flex flex-wrap justify-between items-center mt-6 mb-4">

        <div class="flex gap-2">
            <button class="filtro-btn active" data-filter="todas">Todas</button>
            <button class="filtro-btn" data-filter="Pendiente">Pendientes</button>
            <button class="filtro-btn" data-filter="Aprobada">Aprobadas</button>
            <button class="filtro-btn" data-filter="Rechazada">Rechazadas</button>
        </div>

        <button id="btnNuevaSolicitud"
            class="inline-flex gap-2 items-center bg-primary text-white px-4 py-2 rounded-xl shadow">
            <i data-lucide="plus"></i> Nueva Solicitud
        </button>
    </div>

    <!-- ===============================
          CONTENEDOR DE SOLICITUDES
    ================================ -->
    <div id="contenedorSolicitudes" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3"></div>

</main>

<!-- ===============================
      MODAL DE CREACIÓN
=============================== -->
<div id="modalSolicitud" class="modal-overlay">

    <div class="modal-container max-w-xl">

        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-lg font-semibold">Nueva Solicitud</h2>
                <p class="text-sm text-muted-foreground">
                    Registre una nueva solicitud de materiales para una ficha de formación
                </p>
            </div>
            <button id="cerrarModal" class="modal-close-btn">
                <i data-lucide="x"></i>
            </button>
        </div>

        <form id="formSolicitud" class="space-y-4">

            <!-- Material -->
            <div>
                <label>Material solicitado *</label>
                <select id="material" class="input-siga w-full">
                    <option value="">Seleccione el material</option>
                </select>
            </div>

            <!-- Cantidad -->
            <div>
                <label>Cantidad *</label>
                <input type="number" id="cantidad" class="input-siga w-full" min="1" value="1">
            </div>

            <!-- Programa -->
            <div>
                <label>Programa de formación *</label>
                <select id="programa" class="input-siga w-full">
                    <option value="">Seleccione...</option>
                </select>
            </div>

            <!-- Instructor -->
            <div>
                <label>Instructor *</label>
                <select id="instructor" class="input-siga w-full">
                    <option value="">Seleccione...</option>
                </select>
            </div>

            <!-- Observaciones -->
            <div>
                <label>Observaciones</label>
                <textarea id="observaciones" class="input-siga w-full h-24"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" id="btnCancelarModal" class="btn-secondary">Cancelar</button>
                <button class="btn-primary">Crear Solicitud</button>
            </div>

        </form>
    </div>

</div>

<!-- ===============================
      JAVASCRIPT DEL MÓDULO
=============================== -->
<script src="../../assets/js/solicitudes/solicitudes.js"></script>

<script> lucide.createIcons(); </script>

</body>
</html>
