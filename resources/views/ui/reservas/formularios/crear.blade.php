@extends('layout.app')

@section('content')
<section class="py-10 lg:py-0 mr-6 ml-6">
    <div class="mx-auto px-0">
        <div class="-mx-4 flex flex-wrap">
            <div class="w-full">
                <div class="max-w-full overflow-x-auto">
                    <form action="{{ $formAction }}" method="post">
                        @csrf
                        
                        <x-reserva-form-fields :reserva="$reserva" :vehiculos="$vehiculos" :usuarios="$usuarios" :ubicacion="$ubicacion" :dependencias="$dependencias" />

                        <div class="mt-6 flex items-center justify-end gap-x-6">
                            @php
                                $configEditar = $ubicacion == 'externa'
                                    ? ['route' => route('reservas.prestamos')]
                                    : ['route' => route('reservas.internas')];
                            @endphp

                            <a href="{{ $configEditar['route'] }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg flex items-center gap-2 transition-colors">
                                Cancelar
                            </a>


                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2 transition-colors">
                                Crear
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
