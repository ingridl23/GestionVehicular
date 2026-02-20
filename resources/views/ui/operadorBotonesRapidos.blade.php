{{-- ALERTAS --}}
@foreach ($alertas as $alerta)
    <x-alerta-card :alerta="$alerta" />
@endforeach

{{-- BOTONES RÁPIDOS --}}
<section class="flex flex-col gap-10 mb-10">
    <button class="btn-rapido" action="{{ route('admin.reservas.form.agregar') }}" >Iniciar reserva</button>

    <button class="btn-rapido"
        onclick="window.location='{{ route('operativo.reportes.create') }}'">
        Comenzar reporte
    </button>

    <button  action="{{ route('operativo.editar-conductor','$id') }}" class="btn-rapido">Asignar conductor</button>
</section>

{{-- BOTONES DE VIAJE --}}
<section class="flex gap-6">
    <button class="btn-iniciar flex-1">Iniciar viaje</button>

    <button
        class="btn-finalizar flex-1 {{ auth()->user()->viajeActivo ? '' : 'opacity-50 cursor-not-allowed' }}"
      {{ auth()->user()->viajeActivo ? '' : 'disabled' }}>
        Finalizar viaje
    </button>
</section>


