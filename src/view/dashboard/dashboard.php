<?php

$collapsed = isset($_GET["coll"]) && $_GET["coll"] == "1";
$sidebarWidth = $collapsed ? "70px" : "260px";
// ===============================
//  Mock data equivalente a mock-data.ts
//  (puedes luego conectarlo a tu DB)
// ===============================

$mockMateriales = [
    ["id" => 1, "nombre" => "Cemento gris", "estado" => "disponible"],
    ["id" => 2, "nombre" => "Cable eléctrico", "estado" => "disponible"],
    ["id" => 3, "nombre" => "Pintura blanca", "estado" => "agotado"],
];

$mockBodegas = [
    ["id" => 1, "nombre" => "Bodega Principal", "estado" => true],
    ["id" => 2, "nombre" => "Bodega Secundaria", "estado" => true],
    ["id" => 3, "nombre" => "Bodega Antigua", "estado" => false],
];

$mockMovimientos = [
    ["id" => 1, "tipo" => "entrada", "material_nombre" => "Cemento gris", "cantidad" => 20, "fecha" => "2024-11-27", "hora" => "08:15"],
    ["id" => 2, "tipo" => "salida",  "material_nombre" => "Cable eléctrico", "cantidad" => 10, "fecha" => "2024-11-27", "hora" => "10:30"],
    ["id" => 3, "tipo" => "entrada", "material_nombre" => "Pintura blanca", "cantidad" => 5, "fecha" => "2024-11-26", "hora" => "16:10"],
    ["id" => 4, "tipo" => "salida",  "material_nombre" => "Cemento gris", "cantidad" => 8, "fecha" => "2024-11-27", "hora" => "17:45"],
];

$mockSolicitudes = [
    ["id" => 1, "instructor_nombre" => "Juan Pérez",  "ficha_numero" => "2567890", "estado" => "pendiente"],
    ["id" => 2, "instructor_nombre" => "Ana Gómez",   "ficha_numero" => "2456789", "estado" => "aprobada"],
    ["id" => 3, "instructor_nombre" => "Carlos Ruiz", "ficha_numero" => "2345678", "estado" => "rechazada"],
    ["id" => 4, "instructor_nombre" => "Laura Díaz",  "ficha_numero" => "2678901", "estado" => "pendiente"],
];

$mockAlerts = [
    ["material_id" => 1, "material_nombre" => "Cemento gris",   "stock_actual" => 5,  "stock_minimo" => 20],
    ["material_id" => 2, "material_nombre" => "Cable eléctrico","stock_actual" => 3,  "stock_minimo" => 15],
];

$consumoData = [
    [ "name" => "Ene", "consumo" => 120 ],
    [ "name" => "Feb", "consumo" => 98 ],
    [ "name" => "Mar", "consumo" => 145 ],
    [ "name" => "Abr", "consumo" => 87 ],
    [ "name" => "May", "consumo" => 156 ],
    [ "name" => "Jun", "consumo" => 134 ],
];

$categoriaData = [
    [ "name" => "Construcción", "value" => 45, "color" => "var(--chart-1)" ],
    [ "name" => "Eléctrico",    "value" => 25, "color" => "var(--chart-2)" ],
    [ "name" => "Herramientas", "value" => 20, "color" => "var(--chart-3)" ],
    [ "name" => "Pinturas",     "value" => 10, "color" => "var(--chart-4)" ],
];

// CATEGORÍAS (sin color aún, solo nombre + valor)
$categoriaDataRaw = [
    [ "name" => "Construcción", "value" => 45 ],
    [ "name" => "Eléctrico",    "value" => 25 ],
    [ "name" => "Herramientas", "value" => 20 ],
    [ "name" => "Pinturas",     "value" => 10 ],
    // Si mañana agregas más categorías, se añaden aquí o vienen desde BD
];

// Paleta de colores reutilizable (puedes agregar más)
$palette = [
    "#39A900", // Verde principal
    "#007832", // Verde oscuro
    "#00304D", // Azul oscuro
    "#71277A", // Morado
    "#50E5F9", // Cian
    "#FDC300", // Amarillo
    "#F6F6F6", // Gris claro
    "#FFFFFF", // Blanco
    "#000000", // Negro
];


// Construimos $categoriaData con color dinámico
$categoriaData = [];
foreach ($categoriaDataRaw as $index => $cat) {
    $color = $palette[$index % count($palette)]; // va ciclando la paleta
    $categoriaData[] = [
        "name"  => $cat["name"],
        "value" => $cat["value"],
        "color" => $color,
    ];
}


// ===============================
//  Cálculos equivalentes a los de React
// ===============================
$solicitudesPendientes = count(array_filter($mockSolicitudes, fn($s) => $s["estado"] === "pendiente"));
$materialesActivos     = count(array_filter($mockMateriales,  fn($m) => $m["estado"] === "disponible"));
$bodegasActivas        = count(array_filter($mockBodegas,     fn($b) => !empty($b["estado"])));
$movimientosHoy        = count(array_filter($mockMovimientos, fn($m) => $m["fecha"] === "2024-11-27"));

// Para la “gráfica” de barras simple
$maxConsumo = max(array_column($consumoData, "consumo"));

// Para la “gráfica” de pastel con conic-gradient
$totalCategorias = array_sum(array_column($categoriaData, "value"));
$currentAngle = 0;
$gradientParts = [];
foreach ($categoriaData as $cat) {
    $angle = ($cat["value"] / $totalCategorias) * 360;
    $start = $currentAngle;
    $end   = $currentAngle + $angle;
    $gradientParts[] = $cat["color"] . " " . $start . "deg " . $end . "deg";
    $currentAngle = $end;
}
$pieGradient = implode(", ", $gradientParts);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../assets/css/globals.css">
</head>
<body>
    <main class="p-6 transition-all duration-300"
      style="margin-left: <?= isset($_GET['coll']) && $_GET['coll'] == "1" ? '70px' : '260px' ?>;">

<div class="space-y-6 animate-fade-in-up">
<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
        <p class="text-muted-foreground">Resumen general del inventario y actividad reciente</p>
    </div>
    <div class="flex gap-2">
        <a href="?page=solicitudes">
        <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md border border-border bg-transparent px-4 py-2 text-sm font-medium text-foreground shadow-sm hover:bg-muted gap-2">
        <i data-lucide="clock" class="h-5 w-5 "></i>
        Pendientes
        </button>
    </a>
    <a href="?page=materiales">
        <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90 gap-2">
        <i data-lucide="package" class="h-4 w-4"></i>
        Nuevo Material
        </button>
    </a>
    </div>
</div>

<!-- targets -->
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class=" rounded-xl border border-border bg-card p-8 flex flex-col gap-2 ">
    <div class="flex items-center justify-between">
        <p class="text-2x1 font-medium text-muted-foreground">Total Materiales</p>
        <div class="rounded-full p-2 text-primary bg-[#39A90020]">
        <i data-lucide="box" class="h-5 w-5 text-[#39A900]"></i>
    </div>
    </div>
    <div class="flex items-center gap-2 mt-2">
        <p class="mt-2 text-2xl font-bold"><?php echo count($mockMateriales); ?></p>
        <span class="text-xs text-success flex items-center">+12%<i data-lucide="trending-up" class="ml-2 h-4 w-4 text-[#39A900]"></i></span>
    </div>

    <p class="text-xs text-success flex items-center"><?php echo $materialesActivos; ?> disponibles</p>
    <div class="flex items-center gap-1 text-xs text-success">
        
    </div>
    </div>

    <!-- StatCard: Bodegas Activas -->
    <div class="rounded-xl border border-border bg-card p-8 flex flex-col gap-2">
    <div class="flex items-center justify-between">
        <p class="text-2x1 font-medium text-muted-foreground">Bodegas Activas</p>
        <div class="rounded-full p-2 text-primary bg-[#39A90020]">
        <i data-lucide="warehouse" class="h-5 w-5 text-[#39A900]"></i>
        </div>
    </div>
    <div class="flex items-center gap-2 mt-2">
        <p class="mt-2 text-2xl font-bold"><?php echo $bodegasActivas; ?></p>
        <span class="text-xs text-success flex items-center">Sin cambios</span>
    </div>
    
    <p class="text-xs text-muted-foreground">de <?php echo count($mockBodegas); ?> registradas</p>
    </div>

    <!-- StatCard: Movimientos Hoy -->
    <div class="rounded-xl border border-border bg-card p-8 flex flex-col gap-2">
    <div class="flex items-center justify-between">
        <p class="text-2x1 font-medium text-muted-foreground">Movimientos Hoy</p>
        <div class="rounded-full p-2 text-primary bg-[#39A90020]">
        <i data-lucide="arrow-down-up" class="h-5 w-5 text-[#39A900]"></i>
        </div>
    </div>
    <div class="flex items-center gap-2 mt-2">
        <p class="mt-2 text-2xl font-bold"><?php echo $movimientosHoy; ?></p>
        <span class="text-xs text-success flex items-center">+8%<i data-lucide="trending-up" class="ml-2 h-4 w-4 text-[#39A900]"></i></span>
    </div>
    <p class="text-xs text-muted-foreground">Entradas y salidas</p>
    </div>

    <!-- StatCard: Alertas Stock -->
    <div class="rounded-xl border border-border bg-card p-8 flex flex-col gap-2">
    <div class="flex items-center justify-between">
        <p class="mg-7px text-2x1 font-medium text-muted-foreground">Alertas Stock</p>
        <div class="rounded-full bg-primary/10 p-2 text-primary bg-[#39A90020] <?php echo count($mockAlerts) > 0 ? 'bg-warning/10 text-warning-foreground' : 'bg-muted text-muted-foreground'; ?> p-2">
            <i data-lucide="alert-triangle" class="h-5 w-5 text-[#EF4444]"></i>
        </div>
        </div>
    </div>
    <div class="flex items-center gap-2 mt-2">
        <p class="mt-2 text-2xl font-bold"><?php echo count($mockAlerts); ?></p>
        <span class="text-xs text-success flex items-center">+8%<i data-lucide="trending-down" class="ml-2 h-4 w-4 text-[#EF4444]"></i></span>
    </div>
    <p class="text-xs text-muted-foreground">Materiales en riesgo</p>
    </div>
</div>
<!-- Charts Row -->
<div class="grid gap-6 lg:grid-cols-2">
    <!-- Consumo Chart -->
    <div class="rounded-xl border border-border bg-card">
        <div class="flex items-center justify-between px-6 pt-4 pb-2">
            <div>
                <h2 class="text-base font-semibold">Consumo de Materiales</h2>
                <p class="text-sm text-muted-foreground">Últimos 12 meses</p>
            </div>
            <i data-lucide="trending-up" class="h-5 w-5 text-muted-foreground"></i>
        </div>

        <div class="px-6 pb-6">
            <div class="border-t border-border pt-4 h-44">
                <canvas id="consumoChart" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Categorías Chart -->
    <div class="rounded-xl border border-border bg-card">
        <div class="flex items-center justify-between px-6 pt-4 pb-2">
            <div>
                <h2 class="text-base font-semibold">Distribución por Categoría</h2>
                <p class="text-sm text-muted-foreground">Materiales activos</p>
            </div>
        </div>
        <div class="px-6 pb-6">
            <div class="border-t border-border pt-4">
                <div class="flex items-center justify-center gap-6">
                    <!-- Gráfica de pastel -->
                    <div class="h-40 w-40">
                        <canvas id="categoriaChart" class="w-full h-full"></canvas>
                    </div>

                    <!-- Leyenda a la derecha -->
                    <div class="space-y-2 text-sm">
                        <?php foreach ($categoriaData as $item): ?>
                            <div class="flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full"
                                    style="background-color: <?php echo $item['color']; ?>;"></span>
                                <span><?php echo htmlspecialchars($item['name']); ?>:</span>
                                <span class="font-medium text-muted-foreground">
                                    <?php echo $item['value']; ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Bottom Row -->
<div class="grid gap-6 lg:grid-cols-3">
    <!-- Stock Alerts -->
    <div class="rounded-xl border border-border bg-card">
    <div class="flex items-center justify-between px-6 pt-4 pb-3">
        <h2 class="text-base font-semibold">Alertas de Stock</h2>
        <a href="?page=materiales">
        <button class="inline-flex items-center justify-center rounded-md px-2 py-1 text-xs text-muted-foreground hover:bg-muted gap-1 h-8">
            Ver todo
            <i data-lucide="arrow-right" class="h-3 w-3"></i>
        </button>
        </a>
    </div>
    <div class="px-6 pb-4 space-y-4">
        <?php if (count($mockAlerts) === 0): ?>
        <p class="text-center text-sm text-muted-foreground py-4">No hay alertas de stock</p>
        <?php else: ?>
        <?php foreach ($mockAlerts as $alert): 
            $percent = ($alert["stock_actual"] / $alert["stock_minimo"]) * 100;
            if ($percent > 100) $percent = 100;
        ?>
            <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium"><?php echo htmlspecialchars($alert["material_nombre"]); ?></span>
                <span class="px-3 py-1 rounded-full bg-[#FDC30050] font-medium text-sm text-[#FDC300]">
                Bajo
                </span>

            </div>
            <div class="flex items-center gap-2">
                <div class="h-2 w-full rounded-full bg-muted overflow-hidden">
                <div class="h-2 rounded-full bg-[#39A900]" style="width: <?php echo $percent; ?>%;"></div>
                </div>
                <span class="text-xs text-muted-foreground whitespace-nowrap">
                <?php echo $alert["stock_actual"]; ?>/<?php echo $alert["stock_minimo"]; ?>
                </span>
            </div>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    </div>

    <!-- Solicitudes Recientes -->
    <div class="rounded-xl border border-border bg-card">
    <div class="flex items-center justify-between px-6 pt-4 pb-3">
        <h2 class="text-base font-semibold">Solicitudes Recientes</h2>
        <a href="?page=solicitudes">
            <button class="inline-flex items-center justify-center rounded-md px-2 py-1 text-xs text-muted-foreground hover:bg-muted gap-1 h-8">
                Ver todo
                <i data-lucide="arrow-right" class="h-3 w-3"></i>
            </button>
        </a>
    </div>
    <div class="px-6 pb-4 space-y-4">
        <?php foreach (array_slice($mockSolicitudes, 0, 3) as $solicitud): 
        $estado = $solicitud["estado"];
        if ($estado === "pendiente") {
            $badgeClasses = "bg-[#FDC30040] text-[#FDC300]";
            $icon = "clock";
        } elseif ($estado === "aprobada") {
            $badgeClasses = "bg-[#39A90040] text-[#39A900]";
            $icon = "check-circle-2";
        } else {
            $badgeClasses = "bg-[#EF444440] text-[#EF4444]";
            $icon = "x-circle";
        }
        ?>
        <div class="flex items-start gap-3 pb-3 border-b border-border last:border-0 last:pb-0">
            <div class="mt-0.5 rounded-full p-1.5 <?php echo $badgeClasses; ?>">
            <i data-lucide="<?php echo $icon; ?>" class="h-3 w-3"></i>
            </div>
            <div class="flex-1 min-w-0">
            <p class="text-sm font-medium truncate"><?php echo htmlspecialchars($solicitud["instructor_nombre"]); ?></p>
            <p class="text-xs text-muted-foreground">Ficha <?php echo htmlspecialchars($solicitud["ficha_numero"]); ?></p>
            </div>
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs border <?php echo $badgeClasses; ?>">
            <?php echo htmlspecialchars($estado); ?>
            </span>
        </div>
        <?php endforeach; ?>
    </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="rounded-xl border border-border bg-card">
    <div class="flex items-center justify-between px-6 pt-4 pb-3">
        <h2 class="text-base font-semibold">Actividad Reciente</h2>
        <a href="?page=movimientos">
        <button class="inline-flex items-center justify-center rounded-md px-2 py-1 text-xs text-muted-foreground hover:bg-muted gap-1 h-8">
            Ver todo
            <i data-lucide="arrow-right" class="h-3 w-3"></i>
        </button>
        </a>
    </div>
    <div class="px-6 pb-4 space-y-4">
        <?php foreach (array_slice($mockMovimientos, 0, 4) as $mov): 
        if ($mov["tipo"] === "entrada") {
            $movClasses = "bg-success/10 text-success";
        } elseif ($mov["tipo"] === "salida") {
            $movClasses = "bg-primary/10 text-primary";
        } else {
            $movClasses = "bg-accent/10 text-accent-foreground";
        }
        ?>
        <div class="flex items-start gap-3 pb-3 border-b border-border last:border-0 last:pb-0">
            <div class="mt-0.5 rounded-full p-1.5 <?php echo $movClasses; ?>">
            <i data-lucide="arrow-down-up" class="h-3 w-3"></i>
            </div>
            <div class="flex-1 min-w-0">
            <p class="text-sm font-medium capitalize"><?php echo htmlspecialchars($mov["tipo"]); ?></p>
            <p class="text-xs text-muted-foreground truncate">
                <?php echo htmlspecialchars($mov["material_nombre"]); ?> (<?php echo $mov["cantidad"]; ?>)
            </p>
            </div>
            <span class="text-xs text-muted-foreground"><?php echo htmlspecialchars($mov["hora"]); ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    </div>
</div>
</div>
</main>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
<script>
// ========= CONSUMO MENSUAL (BARRAS) =========
const labelsConsumo = <?php echo json_encode(array_column($consumoData, 'name')); ?>;
const valoresConsumo = <?php echo json_encode(array_map('intval', array_column($consumoData, 'consumo'))); ?>;

const totalMateriales = valoresConsumo.reduce((acc, val) => acc + val, 0);
const maxY = totalMateriales > 0 ? totalMateriales : 10;

const consumoCtx = document.getElementById('consumoChart').getContext('2d');

const consumoChart = new Chart(consumoCtx, {
    type: 'bar',
    data: {
    labels: labelsConsumo,
    datasets: [{
        label: 'Consumo de materiales',
        data: valoresConsumo,
        backgroundColor: 'rgba(148, 163, 184, 0.75)', // gris suave
        borderRadius: 8,
        borderSkipped: false
    }]
    },
    options: {
    responsive: true,
    maintainAspectRatio: false, // usa h-56 del contenedor
    plugins: {
        legend: { display: false },
        tooltip: {
        enabled: true,
        callbacks: {
            label: function(context) {
            const valor = context.parsed.y || 0;
            return valor + ' materiales';
            }
        }
        }
    },
    scales: {
        x: {
        grid: { display: false },
        ticks: { font: { size: 10 } }
        },
        y: {
        beginAtZero: true,
        suggestedMax: maxY,
        ticks: {
            stepSize: Math.max(1, Math.round(maxY / 5)),
            font: { size: 10 }
        },
        grid: { color: 'rgba(229, 231, 235, 0.8)' }
        }
    }
    }
});

    // ========= DISTRIBUCIÓN POR CATEGORÍA (DOUGHNUT) =========
const categoriaLabels = <?php echo json_encode(array_column($categoriaData, 'name')); ?>;
const categoriaValoresRaw = <?php echo json_encode(array_map('intval', array_column($categoriaData, 'value'))); ?>;
const categoriaColoresRaw = <?php echo json_encode(array_column($categoriaData, 'color')); ?>;

const totalCategoriasValor = categoriaValoresRaw.reduce((acc, val) => acc + val, 0);

let categoriaLabelsFinal = categoriaLabels;
let categoriaValoresFinal = categoriaValoresRaw;
let categoriaColoresFinal = categoriaColoresRaw;

// Si no hay datos (todo 0), mostramos un solo slice "Sin datos"
if (totalCategoriasValor === 0) {
    categoriaLabelsFinal = ['Sin datos'];
    categoriaValoresFinal = [1];
    categoriaColoresFinal = ['rgba(148, 163, 184, 0.4)']; // gris suave
}

const categoriaCtx = document.getElementById('categoriaChart').getContext('2d');

const categoriaChart = new Chart(categoriaCtx, {
    type: 'doughnut',
    data: {
    labels: categoriaLabelsFinal,
    datasets: [{
        data: categoriaValoresFinal,
        backgroundColor: categoriaColoresFinal, // usa tus var(--chart-x) sin problema
        borderWidth: 0
    }]
    },
    options: {
    responsive: true,
    maintainAspectRatio: false, // usa h-56 del contenedor
    cutout: '65%', // agujero central (tipo donut)
    plugins: {
        legend: {
        display: false // ya tienes leyenda a la derecha si la quieres dejar en HTML
        },
        tooltip: {
        callbacks: {
            label: function(context) {
            if (totalCategoriasValor === 0) {
                return 'Sin datos';
            }
            const value = context.parsed;
            const percent = ((value / totalCategoriasValor) * 100).toFixed(1);
            return `${context.label}: ${value}% (${percent}%)`;
            }
        }
        }
    }
    }
});
</script>
