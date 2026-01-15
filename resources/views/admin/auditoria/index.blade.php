@extends('layout.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

    <!-- Tarjeta: Vehículos Totales -->
    <div class="p-4 bg-white dark:bg-slate-800 border rounded shadow">
        <h3 class="text-sm font-medium text-slate-600 dark:text-slate-300">Vehículos activos</h3>
        <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">42</p>
        <span class="text-xs text-green-600 dark:text-green-400">+3 este mes</span>
    </div>

    <!-- Tarjeta: Reservas activas -->
    <div class="p-4 bg-white dark:bg-slate-800 border rounded shadow">
        <h3 class="text-sm font-medium text-slate-600 dark:text-slate-300">Reservas activas</h3>
        <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">8</p>
    </div>

    <!-- Tarjeta: Reportes abiertos -->
    <div class="p-4 bg-white dark:bg-slate-800 border rounded shadow">
        <h3 class="text-sm font-medium text-slate-600 dark:text-slate-300">Reportes abiertos</h3>
        <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">5</p>
    </div>

    <!-- Tarjeta: Alertas -->
    <div class="p-4 bg-white dark:bg-slate-800 border rounded shadow">
        <h3 class="text-sm font-medium text-slate-600 dark:text-slate-300">Alertas activas</h3>
        <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">3</p>
    </div>

</div>

<!-- Placeholder gráfico -->
<div class="bg-white dark:bg-slate-800 border rounded shadow p-4">
    <h2 class="text-sm font-medium text-slate-600 dark:text-slate-300 mb-2">Uso mensual de vehículos</h2>
    <div class="h-64 flex items-center justify-center text-slate-400 dark:text-slate-500">
        Gráfico aquí
    </div>
</div>

@endsection
