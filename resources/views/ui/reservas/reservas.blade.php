@extends('layout.app')

@section('content')
<section class="bg-white py-20 lg:py-[120px] dark:bg-dark">
    <div class="mx-auto px-0">
      <div class="-mx-4 flex flex-wrap">
        <div class="w-full">
          <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
              <thead>
                <tr class="bg-primary text-center">

                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white lg:px-4 lg:py-7">
                    Inicio de uso
                  </th>
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white lg:px-4 lg:py-7">
                    Fin de uso
                  </th>
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white lg:px-4 lg:py-7">
                    Estado
                  </th>
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white lg:px-4 lg:py-7">
                    Oficina solicitante
                  </th>
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white lg:px-4 lg:py-7">
                    Conductor
                  </th>
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white lg:px-4 lg:py-7">
                    Vehiculo
                  </th>
                  @canany(['ver_reservas_internas' , 'actualizar_reserva_interna' , 'cancelar_reserva_interna'])
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white lg:px-4 lg:py-7">
                    Acciones
                    @endcanany
                  </th>
                </tr>
              </thead>
              <tbody>
                @foreach ($reservas as $reserva)
                <tr>
                  <td
                    class="border-b border-[#E8E8E8] bg-white px-2 py-5 text-center text-base font-medium text-dark dark:border-dark dark:bg-dark-2 dark:text-dark-7">
                    {{$reserva -> fecha_inicio_reserva}}
                  </td>
                  <td
                    class="border-b border-[#E8E8E8] bg-white px-2 py-5 text-center text-base font-medium text-dark dark:border-dark dark:bg-dark-2 dark:text-dark-7">
                    {{$reserva -> fecha_fin_reserva}}
                  </td>
                  <td
                    class="border-b border-[#E8E8E8] bg-[#F3F6FF] px-2 py-5 text-center text-base font-medium text-dark dark:border-dark dark:bg-dark-3 dark:text-dark-7">
                    {{$reserva ->estado_reserva -> estado}}
                  </td>
                  <td
                    class="border-b border-[#E8E8E8] bg-white px-2 py-5 text-center text-base font-medium text-dark dark:border-dark dark:bg-dark-2 dark:text-dark-7">
                    {{$reserva -> dependencia_solicitante -> nombre}}
                  </td>
                  <td
                    class="border-b border-[#E8E8E8] bg-[#F3F6FF] px-2 py-5 text-center text-base font-medium text-dark dark:border-dark dark:bg-dark-3 dark:text-dark-7">
                    {{$reserva -> usuario -> name}} - {{$reserva -> usuario -> lastname}}
                  </td>
                  <td
                    class="border-b border-[#E8E8E8] bg-[#F3F6FF] px-2 py-5 text-center text-base font-medium text-dark dark:border-dark dark:bg-dark-3 dark:text-dark-7">
                    {{$reserva -> vehiculo -> dominio}} - {{$reserva -> vehiculo -> marca}} - {{$reserva -> vehiculo -> anio}}
                  </td>
                  <td
                    class="flex justify-center border-b border-r border-[#E8E8E8] bg-white px-2 py-5 text-center text-base font-medium text-dark dark:border-dark dark:bg-dark-2 dark:text-dark-7">
                    @can('ver_reservas_internas')
                    <a href="{{ route('reservas.reserva', $reserva->id) }}"
                      class="inline-block rounded-md border border-primary px-2 py-2 m-1 font-medium text-primary hover:bg-primary hover:text-white" title="Ver detalles de la reserva">
                      <i class="fa-solid fa-eye text-blue-500"></i>
                    </a>
                    @endcan
                    @can('actualizar_reserva_interna')
                    <a href="{{ route('reservas.editar', $reserva->id) }}"
                      class="inline-block rounded-md border border-primary px-2 py-2 m-1 font-medium text-primary hover:bg-primary hover:text-white" title="Editar reserva">
                      <i class="fa-solid fa-pen-to-square text-yellow-500"></i>
                    </a>
                    @endcan
                    @can('cancelar_reserva_interna')
                    <a href="{{ route('reservas.cancelar', $reserva->id) }}"
                      class="inline-block rounded-md border border-primary px-2 py-2 m-1 font-medium text-primary hover:bg-primary hover:text-white" title="Cancelar reserva">
                      <i class="fa fa-times text-red-500"></i>
                    </a>
                    @endcan
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
    <div class="contenedor-servidor">
        @if($reservas->isEmpty())
            <p>No hay reservas</p>
        @else
    
            
        @endif
         
    </div>



    <div class="contenedor-servidor">
        {{ $reservas->links() }}
    </div>

    
@endsection

</body>



</html>