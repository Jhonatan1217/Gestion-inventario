<?php

$collapsed = isset($_GET["coll"]) && $_GET["coll"] == "1";
$sidebarWidth = $collapsed ? "70px" : "260px";

$stats = [
  'total' => 5,
  'unread' => 1,
  'critical' => 1,
  'low' => 3
];

$alerts = [
  [
    'name' => 'Arena de río',
    'status' => 'Stock Crítico',
    'value' => '8/10 m³',
    'code' => 'MC-002',
    'type' => 'warning',
    'time' => '18 de noviembre, 07:00 p.m.'
  ],
  [
    'name' => 'Guantes industriales',
    'status' => 'Agotado',
    'value' => '0/60 pares',
    'code' => 'EP-120',
    'type' => 'critical',
    'time' => '16 de noviembre, 15:20 p.m.'
  ],
  [
    'name' => 'Pintura acrílica blanca',
    'status' => 'Bajo',
    'value' => '7/12 galones',
    'code' => 'PT-009',
    'type' => 'low',
    'time' => '23 de noviembre, 10:45 a.m.'
  ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Alertas Inventario</title>

  <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- Tus variables -->
    <link rel="stylesheet" href="../../assets/css/globals.css">
</head>
<body class="bg-background p-6">
    <main class="p-6 transition-all duration-300"
    style="margin-left: <?= isset($_GET['coll']) && $_GET['coll'] == "1" ? '70px' : '260px' ?>;">
  <!-- KPIs -->
  <div class="grid grid-cols-4 gap-4 mb-6">
    <?php
    $cards = [
      ['icon' => 'bell', 'label' => 'Total Notificaciones', 'value' => $stats['total'], 'bg' => 'bg-success'],
      ['icon' => 'alert-triangle', 'label' => 'Sin Leer', 'value' => $stats['unread'], 'bg' => 'bg-warning'],
      ['icon' => 'alert-octagon', 'label' => 'Críticas', 'value' => $stats['critical'], 'bg' => 'bg-destructive'],
      ['icon' => 'box', 'label' => 'Stock Bajo', 'value' => $stats['low'], 'bg' => 'bg-info'],
    ];

    foreach ($cards as $c): ?>
      <div class="bg-card rounded-xl p-4 shadow flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center <?= $c['bg'] ?>">
          <i data-lucide="<?= $c['icon'] ?>" class="text-white w-5 h-5"></i>
        </div>

        <div>
          <p class="text-xs text-muted-foreground"><?= $c['label'] ?></p>
          <p class="text-xl font-semibold"><?= $c['value'] ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- ALERTAS -->
  <div class="bg-card rounded-xl p-6 shadow">

    <h2 class="text-lg font-semibold mb-1">Todas las alertas</h2>
    <p class="text-sm text-muted-foreground mb-4">
      Notificaciones sobre el estado del inventario
    </p>

    <div class="space-y-4">

      <?php foreach ($alerts as $a): 
        $colors = match($a['type']) {
          'warning' => 'bg-warning bg-opacity-30',
          'critical' => 'bg-destructive bg-opacity-30',
          'low' => 'bg-info bg-opacity-25'
        };

        $badge = match($a['type']) {
          'warning' => 'bg-warning text-warning-foreground',
          'critical' => 'bg-destructive text-destructive-foreground',
          'low' => 'bg-info text-info-foreground'
        };

        $icon = match($a['type']) {
          'warning' => 'alert-triangle',
          'critical' => 'alert-octagon',
          'low' => 'box'
        };
      ?>

      <div class="flex items-start justify-between p-4 rounded-lg <?= $colors ?>">
        <div class="flex gap-3">

          <div class="mt-1">
            <i data-lucide="<?= $icon ?>" class="w-5 h-5"></i>
          </div>

          <div>
            <p class="font-semibold">
              <?= $a['name'] ?> - <?= $a['status'] ?> (<?= $a['value'] ?>)
            </p>

            <p class="text-xs text-muted-foreground">
              <?= $a['code'] ?> - <?= $a['name'] ?>
            </p>

            <div class="flex items-center gap-3 mt-1">
              <span class="text-xs text-muted-foreground"><?= $a['time'] ?></span>

              <span class="text-xs px-2 py-0.5 rounded <?= $badge ?>">
                <?= ucfirst($a['type']) ?>
              </span>
            </div>
          </div>
        </div>

        <!-- Botón eliminar -->
        <button class="text-red-500 hover:text-red-700">
          <i data-lucide="trash-2" class="w-5 h-5"></i>
        </button>

      </div>

      <?php endforeach; ?>

    </div>
  </div>

  <!-- Inicializar iconos -->
  <script>
    lucide.createIcons();
  </script>

</body>
</html>
