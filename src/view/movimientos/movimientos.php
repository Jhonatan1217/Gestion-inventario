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
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Movimientos</h1>
        <p class="text-muted-foreground">Historial de entradas, salidas y devoluciones de materiales</p>
    </div>
    <div class="flex gap-2">
        <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md bg-primary px-4 py-2 text-white font-medium shadow hover:bg-primary/90 gap-2">
        <i data-lucide="package" class="h-4 w-4"></i>
        Agregar Movimiento
        </button>
    </div>
</div>
    <!-- targets -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-6">
        <div class="rounded-x1 border border-border bg-card p-8 flex flex-col items-center">
            <div class="flex flex-col justify-center">
                <p class="text-2x1 font-medium text-muted-foreground">2</p>
                <span class="text-xs text-muted-foreground">Entrada</span>
            </div>
        </div>
        <div class="rounded-x1 border border-border bg-card p-8 flex flex-col items-center">
            <div class="flex flex-col justify-center">
                <p class="text-2x1 font-medium text-muted-foreground">2</p>
                <span class="text-xs text-muted-foreground">Salida</span>
            </div>
        </div>
        <div class="rounded-x1 border border-border bg-card p-8 flex flex-col items-center">
            <div class="flex flex-col justify-center">
                <p class="text-2x1 font-medium text-muted-foreground">2</p>
                <span class="text-xs text-muted-foreground">Devolver</span>
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

        <input type="text" name="buscar_material" placeholder="Buscar por material..." class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"/>
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
    <div class="mt-6 rounded-2xl border border-border bg-card overflow-hidden">

    <table class="min-w-full text-sm">

        <!-- CABECERA DE LA TABLA -->
        <thead>
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
                    <span class="inline-flex items-center rounded-md border border-border bg-background px-2 py-1 text-xs font-medium">
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
                    <span class="inline-flex items-center rounded-full px-3 py-0.5 text-xs font-medium bg-slate-100 text-slate-700">
                        Bueno
                    </span>
                </td>

                <!-- Acciones -->
                <td class="px-4 py-3 align-top text-right">
                    <button class="inline-flex h-8 w-8 items-center justify-center rounded-full hover:bg-muted">
                        <i data-lucide="more-horizontal" class="h-4 w-4"></i>
                    </button>
                </td>

            </tr>

        </tbody>
    </table>
</div>



</body>