@extends('layout.app')

@section('content')
<section class="bg-gray-100 dark:bg-gray-900 py-10 lg:py-0 mr-6 ml-6">
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
                            class="inline-flex items-center justify-center
                                    rounded-md bg-gray-400 px-4 py-2
                                    text-sm font-medium text-white
                                    hover:bg-gray-700
                                    focus:outline-none focus:ring-2 focus:ring-blue-500
                                    dark:bg-gray-700 dark:hover:bg-gray-900
                                    text-gray-700 dark:text-white">
                                Cancelar
                            </a>


                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white
                                hover:bg-blue-700 focus-visible:outline focus-visible:outline-2
                                focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
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
