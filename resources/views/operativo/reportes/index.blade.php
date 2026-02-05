@extends('layout.appOperativo')

@section('title', 'Mis reportes')

@section('content')
<div class="space-y-3">

    <a href="{{ route('operativo.reportes.create') }}"
       class="block text-center bg-blue-600 text-white py-2 rounded-lg">
        + Nuevo reporte
    </a>

    @forelse ($reportes as $reporte)
        <a href="{{ route('operativo.reportes.index', $reporte) }}"
           class="block bg-white rounded-lg p-4 shadow">
            <div class="font-semibold">{{ $reporte->titulo }}</div>
            <div class="text-xs text-gray-500">
                Estado: {{ ucfirst($reporte->estado) }}
            </div>
        </a>
    @empty
        <p class="text-center text-gray-400">
            No tenés reportes todavía
        </p>
    @endforelse

</div>
@endsection
