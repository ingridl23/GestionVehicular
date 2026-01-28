@extends('layout.app')

@section('content')
<section class="bg-gray-100 dark:bg-gray-900 py-10 lg:py-0">
    <div class="mx-auto px-0">
        <div class="-mx-4 flex flex-wrap">
            <div class="w-full">
                <div class="max-w-full overflow-x-auto">
                    <form action="{{ $formAction }}" method="post">
                        @csrf

                        <x-reserva-form-fields :reserva="$reserva" :vehiculos="$vehiculos" :usuarios="$usuarios" />

                        <div class="mt-6 flex items-center justify-end gap-x-6">
                            <button type="button" class="text-sm font-semibold text-gray-700 dark:text-white">
                                <a href="{{ route('reservas.internas') }}">Cancelar</a>
                            </button>

                            <button type="submit" class="rounded-md bg-indigo-500 px-4 py-2 text-sm font-semibold text-white
                                hover:bg-indigo-600 focus-visible:outline focus-visible:outline-2
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
