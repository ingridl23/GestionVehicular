@extends('layout.appOperativo')

@section('page-title', 'Reportes')
@section('page-description', 'Seguimiento de mis reportes')

@section('content')

<div class="h-[calc(100vh-150px)] flex flex-col bg-white dark:bg-gray-800 rounded-2xl shadow overflow-hidden">

    <!-- Header -->
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">

    <a href="{{ route('operativo.reportes.index') }}"> <span  class="text-xs px-4 py-2 bg-gray-100 dark:bg-blue-700 text-gray-600 dark:text-gray-400 rounded-lg"> <i class="fas fa-arrow-left"></i></span> </a>
        <span id="chatEntidad"
              class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg">
        </span>
    </div>

    <!-- Body -->
    <div id="chatBody"
         class="flex-1 overflow-y-auto px-5 py-4 space-y-4 bg-gray-50 dark:bg-gray-900">
    </div>

    <!-- Footer -->
    <div class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
        @include('components.mensajeriaInterna', ['reporte_id' => $reporte->id])
    </div>

</div>

<script>
    window.BASE_REPORTES_URL = "{{ request()->segment(1) }}";
</script>


<script>
    window.REPORTES_DATA = @json($reportesData);
    window.USUARIO_ACTUAL_ID = {{ auth()->id() }};
</script>


@endsection
