@extends('layout.app')

@section('page-title', 'Reportes')
@section('page-description', 'Gestión y seguimiento de reportes')

@section('content')

<div class="flex h-[calc(100vh-140px)] bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

    <!-- ============================================================
         PANEL IZQUIERDO — Lista de conversaciones
         ============================================================ -->
    <aside class="w-80 flex flex-col border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">

        <!-- Searchbar + filtros -->
        <div class="p-3 border-b border-gray-200 dark:border-gray-700 space-y-2">
            <!-- Buscar por título o usuario -->
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Buscar reporte o usuario..."
                    class="w-full pl-9 pr-4 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white"
                >
            </div>

            <!-- Filtro por estado -->
            <select id="filterEstado"
                class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="en_revision">En revisión</option>
                <option value="atendido">Atendido</option>
                <option value="cerrado">Cerrado</option>
            </select>

            <!-- Filtros exclusivos para Admin General -->
            @if(auth()->user()->can('ver_reportes_general'))
            <div class="flex gap-2">
                <select id="filterDependencia"
                    class="flex-1 px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white">
                    <option value="">Todas las dependencias</option>
                    @foreach($reportes->pluck('entidad_id')->unique() as $entidadId)
                        <option value="{{ $entidadId }}">Dependencia {{ $entidadId }}</option>
                    @endforeach
                </select>
{{--
                <input
                    type="text"
                    id="filterUsuario"
                    placeholder="Usuario..."
                    class="flex-1 px-3 py-2 text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white"
                >
--}}
            </div>
            @endif
        </div>

        <!-- Lista de reportes -->
        <div id="listaReportes" class="flex-1 overflow-y-auto">
            @forelse($reportes as $reporte)
            <button
                type="button"
                class="reporte-item w-full text-left px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-white dark:hover:bg-gray-800 transition-colors"
                data-id="{{ $reporte->id }}"
                data-titulo="{{ $reporte->titulo }}"
                data-descripcion="{{ $reporte->descripcion }}"
                data-estado="{{ $reporte->estado }}"
                data-entidad-tipo="{{ $reporte->entidad_tipo }}"
                data-entidad-id="{{ $reporte->entidad_id }}"
                data-usuario-id="{{ $reporte->usuario->id }}"
                data-usuario-nombre="{{ $reporte->usuario->name }}"
                data-fecha="{{ $reporte->created_at->format('d/m H:i') }}"
             "
            >
                <div class="flex items-start gap-3">
                    <!-- Avatar usuario -->
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-semibold flex-shrink-0 mt-0.5">
                        {{ strtoupper(substr($reporte->usuario->name, 0, 2)) }}
                    </div>

                    <!-- Info reporte -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                {{ $reporte->usuario->name }}
                            </p>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $reporte->created_at->format('d/m H:i') }}</span>
                        </div>
                        <p class="text-sm text-gray-700 dark:text-gray-300 truncate font-medium">
                            {{ $reporte->titulo }}
                        </p>
                        <div class="flex items-center justify-between gap-2 mt-0.5">
                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate">
                                @if($reporte->comentarios->isNotEmpty())
                                    {{ $reporte->comentarios->last()->comentario }}
                                @else
                                    {{ $reporte->descripcion }}
                                @endif
                            </p>
                            <!-- Badge estado -->
                            <span class="flex-shrink-0 px-2 py-0.5 rounded-full text-xs font-medium
                                @if($reporte->estado === 'pendiente') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                @elseif($reporte->estado === 'en_revision') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif($reporte->estado === 'atendido') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @elseif($reporte->estado === 'cerrado') bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400
                                @endif">
                                @if($reporte->estado === 'pendiente') Pendiente
                                @elseif($reporte->estado === 'en_revision') En revisión
                                @elseif($reporte->estado === 'atendido') Atendido
                                @elseif($reporte->estado === 'cerrado') Cerrado
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </button>
            @empty
                <div class="px-4 py-8 text-center">
                    <i class="fas fa-file-alt text-gray-300 dark:text-gray-600 text-3xl mb-3"></i>
                    <p class="text-sm text-gray-400 dark:text-gray-500">No hay reportes disponibles</p>
                </div>
            @endforelse
        </div>
    </aside>

    <!-- ============================================================
         PANEL DERECHO — Conversación del reporte seleccionado
         ============================================================ -->
    <div class="flex-1 flex flex-col">

        <!-- Estado: sin reporte seleccionado -->
        <div id="sinSeleccion" class="flex-1 flex flex-col items-center justify-center text-center px-8">
            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-comments text-gray-400 dark:text-gray-500 text-3xl"></i>
            </div>
            <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Seleccioná un reporte</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Elegí un reporte de la lista para ver la conversación</p>
        </div>

        <!-- Estado: reporte seleccionado -->
        <div id="reporteSeleccionado" class="flex-1 flex flex-col hidden">

            <!-- Header del reporte -->
            <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div id="chatAvatar" class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-semibold">
                        --
                    </div>
                    <div>
                        <p id="chatUsuario" class="text-sm font-semibold text-gray-900 dark:text-white">--</p>
                        <p id="chatTitulo" class="text-xs text-gray-500 dark:text-gray-400">--</p>
                    </div>
                </div>

                <!-- Cambiar estado -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Estado:</span>
                        <select id="selectEstado" name="estado"
                            class="text-xs px-2 py-1 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                            onchange="cambiarEstado(this)">

                            <option value="pendiente">Pendiente</option>
                            <option value="en_revision">En revisión</option>
                            <option value="atendido">Atendido</option>
                            <option value="cerrado">Cerrado</option>
                        </select>
                    </div>
                    <!-- Info entidad -->
                    <span id="chatEntidad" class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg">
                        --
                    </span>
                </div>
            </div>

            <!-- Body: mensajes -->
            <div id="chatBody" class="flex-1 overflow-y-auto px-5 py-4 space-y-4 bg-gray-50 dark:bg-gray-900">
                <!-- Se renderiza con JS -->
            </div>

            <!-- Footer: placeholder para tu blade de mensajería -->
            <div class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 flex-shrink-0">
                @include('components.mensajeriaInterna', ['reporte_id' => null])
                {{-- Cuando integres tu blade, pasale el reporte_id activo.
                     Desde JS actualizás el atributo data-reporte-id del contenedor
                     o reemplazás el contenido según tu implementación. --}}
            </div>
        </div>
    </div>
</div>
<script>

    window.REPORTES_DATA = @json($reportesData);
    window.USUARIO_ACTUAL_ID = {{ auth()->id() }};




</script>

@vite(['resources/js/reportes.js'])

@endsection

