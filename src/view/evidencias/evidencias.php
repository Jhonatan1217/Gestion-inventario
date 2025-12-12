<?php

$collapsed = isset($_GET["coll"]) && $_GET["coll"] == "1";
$sidebarWidth = $collapsed ? "70px" : "260px";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evidencias de Formación - SIGA</title>
    <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?= BASE_URL ?>/src/assets/css/globals.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/src/assets/css/evidencias/evidencias.css">
</head>
<body class="bg-background text-foreground">
    
    <!-- Main Content -->
    <main class="p-6 transition-all duration-300"
      style="margin-left: <?= isset($_GET['coll']) && $_GET['coll'] == "1" ? '70px' : '260px' ?>;">        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-foreground">Evidencias de Formación</h1>
                <p class="text-sm text-muted-foreground mt-1">Registro de evidencias de uso de materiales de formación</p>
            </div>
            <button id="btnNuevaEvidencia" class="bg-primary text-primary-foreground px-4 py-2.5 rounded-lg font-medium hover:opacity-90 transition-all flex items-center gap-2">
                <span class="text-xl font-bold">+</span>
                Nueva Evidencia
            </button>
        </div>

        <!-- Evidence Grid -->
        <div id="evidenceGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Evidence Card 1 -->
            <div class="evidence-card bg-card rounded-xl border border-border overflow-hidden cursor-pointer hover:shadow-lg transition-all" onclick="openDetailModal(1)">
                <div class="relative">
                    <img src="placeholder-image.jpg" alt="Evidencia" class="w-full h-48 object-cover">
                    <div class="absolute top-3 right-3 bg-card/90 backdrop-blur-sm px-3 py-1.5 rounded-lg flex items-center gap-2">
                        <img src="calendar-icon.png" alt="calendar" class="w-4 h-4">
                        <span class="text-xs font-medium">Fecha 2896441</span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-muted-foreground">2024-04-06</span>
                    </div>
                    <p class="text-sm text-foreground line-clamp-2 mb-3">Trabajo de cimentación realizado por los aprendices de la ficha 2567890</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="badge-material">
                            <img src="tag-icon.png" alt="material" class="w-3 h-3">
                            Cemento Gris
                        </span>
                        <span class="badge-material">
                            <img src="tag-icon.png" alt="material" class="w-3 h-3">
                            Arena de Río
                        </span>
                    </div>
                </div>
            </div>

            <!-- Evidence Card 2 -->
            <div class="evidence-card bg-card rounded-xl border border-border overflow-hidden cursor-pointer hover:shadow-lg transition-all" onclick="openDetailModal(2)">
                <div class="relative">
                    <img src="placeholder-image.jpg" alt="Evidencia" class="w-full h-48 object-cover">
                    <div class="absolute top-3 right-3 bg-card/90 backdrop-blur-sm px-3 py-1.5 rounded-lg flex items-center gap-2">
                        <img src="calendar-icon.png" alt="calendar" class="w-4 h-4">
                        <span class="text-xs font-medium">Fecha 2896441</span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-muted-foreground">2024-11-24</span>
                    </div>
                    <p class="text-sm text-foreground line-clamp-2 mb-3">Instalación eléctrica residencial completada exitosamente</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="badge-material">
                            <img src="tag-icon.png" alt="material" class="w-3 h-3">
                            Cable Eléctrico #12
                        </span>
                    </div>
                </div>
            </div>

            <!-- Evidence Card 3 -->
            <div class="evidence-card bg-card rounded-xl border border-border overflow-hidden cursor-pointer hover:shadow-lg transition-all" onclick="openDetailModal(3)">
                <div class="relative">
                    <img src="placeholder-image.jpg" alt="Evidencia" class="w-full h-48 object-cover">
                    <div class="absolute top-3 right-3 bg-card/90 backdrop-blur-sm px-3 py-1.5 rounded-lg flex items-center gap-2">
                        <img src="calendar-icon.png" alt="calendar" class="w-4 h-4">
                        <span class="text-xs font-medium">2024-11-24</span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-muted-foreground">2024-11-24</span>
                    </div>
                    <p class="text-sm text-foreground line-clamp-2 mb-3">Instalación eléctrica residencial completada exitosamente</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="badge-material">
                            <img src="tag-icon.png" alt="material" class="w-3 h-3">
                            Cable Eléctrico #12
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Detail Modal -->
    <div id="detailModal" class="modal-overlay">
        <div class="bg-card rounded-xl shadow-2xl w-full max-w-2xl mx-4 animate-modal-slide-down">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-5 border-b border-border">
                <h2 class="text-xl font-semibold text-foreground">Detalle de Evidencia</h2>
                <button onclick="closeDetailModal()" class="text-muted-foreground hover:text-foreground transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <!-- Large Image -->
                <div class="mb-5">
                    <img id="detailImage" src="placeholder-image.jpg" alt="Evidencia detalle" class="w-full h-80 object-cover rounded-lg">
                </div>

                <!-- Info Section -->
                <div class="space-y-4">
                    <!-- Title -->
                    <div>
                        <h3 id="detailTitle" class="text-lg font-semibold text-foreground mb-1">Técnico en Instalaciones Eléctricas Residenciales</h3>
                        <div class="flex items-center gap-2 text-sm text-muted-foreground">
                            <img src="calendar-icon.png" alt="calendar" class="w-4 h-4">
                            <span id="detailDate">2024-04-06</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <h4 class="text-sm font-medium text-foreground mb-2">Descripción:</h4>
                        <p id="detailDescription" class="text-sm text-muted-foreground leading-relaxed">
                            Instalación eléctrica residencial completada exitosamente
                        </p>
                    </div>

                    <!-- Materials -->
                    <div>
                        <h4 class="text-sm font-medium text-foreground mb-3">Materiales consumidos:</h4>
                        <div id="detailMaterials" class="flex flex-wrap gap-2">
                            <span class="badge-material-detail">
                                <img src="tag-icon.png" alt="material" class="w-3.5 h-3.5">
                                Cemento Gris
                            </span>
                            <span class="badge-material-detail">
                                <img src="tag-icon.png" alt="material" class="w-3.5 h-3.5">
                                Arena de Río
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal-overlay">
        <div class="bg-card rounded-xl shadow-2xl w-full max-w-lg mx-4 animate-modal-slide-down">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-5 border-b border-border">
                <h2 class="text-xl font-semibold text-foreground">Registrar Evidencia</h2>
                <button onclick="closeRegisterModal()" class="text-muted-foreground hover:text-foreground transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="evidenceForm" class="p-6 space-y-5">
                <p class="text-sm text-muted-foreground">Cargue una evidencia del uso de materiales de formación</p>

                <!-- Date Field -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Fecha de capacitación<span class="text-destructive">*</span>
                    </label>
                    <select class="input-siga w-full" required>
                        <option value="">Seleccionar fecha...</option>
                        <option value="2024-04-06">2024-04-06</option>
                        <option value="2024-11-24">2024-11-24</option>
                    </select>
                </div>

                <!-- Photo Upload -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Fotografía de evidencia<span class="text-destructive">*</span>
                    </label>
                    <div class="upload-area">
                        <svg class="w-12 h-12 text-muted-foreground mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm text-foreground font-medium mb-1">Arrastra una imagen o haz clic para seleccionar</p>
                        <p class="text-xs text-muted-foreground">PNG, JPG hasta 5MB</p>
                        <input type="file" id="photoInput" class="hidden" accept="image/png,image/jpeg,image/jpg">
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Descripción de la obra realizada<span class="text-destructive">*</span>
                    </label>
                    <textarea class="input-siga w-full min-h-[100px] resize-none" placeholder="Formación técnica de procesos constructivos" required></textarea>
                </div>

                <!-- Materials Select -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Materiales consumidos<span class="text-destructive">*</span>
                    </label>
                    <select class="input-siga w-full" required>
                        <option value="">Agregar Material...</option>
                        <option value="cemento">Cemento Gris</option>
                        <option value="arena">Arena de Río</option>
                        <option value="cable">Cable Eléctrico #12</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeRegisterModal()" class="flex-1 px-4 py-2.5 rounded-lg border border-border text-foreground hover:bg-muted transition-all font-medium">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-lg bg-primary text-primary-foreground hover:opacity-90 transition-all font-medium">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/src/assets/js/evidencias/evidencias.js"></script>
</body>
</html>
