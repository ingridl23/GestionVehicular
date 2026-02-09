@extends('layout.app')

@section('page-title', 'Alertas y Notificaciones')
@section('page-description', 'Gestión de alertas del sistema')

@section('content')


<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Alertas Activas</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ $stats['total'] }}

                </p>
            </div>
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Vencimientos</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
            {{ $stats['licencias'] }}
                </p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-id-card text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Mantenimiento</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                   {{ $stats['mantenimiento'] }}
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-wrench text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Vehículos</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ $stats['vehiculos'] }}
                </p>
            </div>
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-car-crash text-orange-600 dark:text-orange-400 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Alertas -->
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            Todas las Alertas
        </h2>

        <button
            onclick="resolverTodas()"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors"
        >
            <i class="fas fa-check-double mr-2"></i>
            Marcar todas como leídas
        </button>
    </div>

    <div class="space-y-3">
        @forelse($alertas as $alerta)
            <div class="flex items-start gap-4 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">

                <!-- Icono -->
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                    {{ $alerta->tipo === 'vehiculo_fuera_servicio' ? 'bg-red-100 dark:bg-red-900/20' : '' }}
                    {{ $alerta->tipo === 'licencia_vencimiento' ? 'bg-yellow-100 dark:bg-yellow-900/20' : '' }}
                    {{ $alerta->tipo === 'mantenimiento_pendiente' ? 'bg-blue-100 dark:bg-blue-900/20' : '' }}
                    {{ $alerta->tipo === 'reserva_rechazada' ? 'bg-orange-100 dark:bg-orange-900/20' : '' }}
                ">
                    <i class="fas
                        {{ $alerta->tipo === 'vehiculo_fuera_servicio' ? 'fa-car-crash text-red-600 dark:text-red-400' : '' }}
                        {{ $alerta->tipo === 'licencia_vencimiento' ? 'fa-id-card text-yellow-600 dark:text-yellow-400' : '' }}
                        {{ $alerta->tipo === 'mantenimiento_pendiente' ? 'fa-wrench text-blue-600 dark:text-blue-400' : '' }}
                        {{ $alerta->tipo === 'reserva_rechazada' ? 'fa-calendar-times text-orange-600 dark:text-orange-400' : '' }}
                    "></i>
                </div>

                <!-- Contenido -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $alerta->titulo }}

                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $alerta->mensaje }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                <i class="far fa-clock mr-1"></i>
                                {{ $alerta->fecha_generada->diffForHumans() }}
                            </p>
                        </div>

                        <!-- Botón Resolver -->
                        <button
                            onclick="resolver({{ $alerta->id }})"
                            class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-medium transition-colors flex-shrink-0">
                            <i class="fas fa-check mr-1"></i>
                            Resolver
                        </button>
                    </div>
                </div>

            </div>
        @empty
            <div class="text-center py-12">
                <i class="fas fa-bell-slash text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                <p class="text-gray-500 dark:text-gray-400">No hay alertas activas</p>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    <div class="mt-6">
        {{ $alertas->links() }}
    </div>

</div>

@endsection

@push('scripts')
<script>
async function resolver(id) {
    if (!confirm('¿Marcar esta alerta como resuelta?')) return;

    try {
        const response = await fetch(`/alertas/${id}/resolver`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            // Recargar página
            window.location.reload();
        } else {
            alert('Error al resolver la alerta');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al resolver la alerta');
    }
}

async function resolverTodas() {
    if (!confirm('¿Marcar todas las alertas como resueltas?')) return;

    // Aquí puedes implementar la lógica para resolver todas
    alert('Funcionalidad en desarrollo');
}
</script>
@endpush
