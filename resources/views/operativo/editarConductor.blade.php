@extends('layout.appOperativo')

@section('content')

<div>
    
    <form action="{{ route('operativo.update.conductor', $id) }}" method="post">
        @method('PATCH')
        @csrf

        <div class="border-b border-gray-200 dark:border-white/10 pb-12 mt-3">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                Asignación de la reserva
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Seleccione el usuario responsable durante el período de la reserva.
            </p>

            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                <!-- Usuario -->
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">
                        Usuario responsable
                    </label>
                    <div class="mt-2 relative">
                        <select id="usuario" name="id_usuario"
                            class="w-full appearance-none rounded-md
                                                bg-white dark:bg-white/5 text-gray-900 dark:text-white
                                                border border-gray-300 dark:border-white/10 py-2 pl-3 pr-8
                                                focus:ring-2 focus:ring-indigo-500 sm:text-sm
                                                *:bg-white dark:*:bg-gray-800">

                            <option value=""
                                @selected(old('id_usuario', $id_usuario_reserva) === null)>
                                Seleccionar
                            </option>

                            @foreach ($usuarios as $usuario)
                            @if(!$usuario->carnet_vencido)
                            <option value="{{ $usuario->id }}"
                            @selected(old('id_usuario', $id_usuario_reserva) == $usuario->id)>
                                {{ $usuario->name }} {{ $usuario->lastname }} - Oficina: {{$usuario->nombre}}
                            </option>
                            @else
                            <option disabled class="text-gray-400">
                                {{ $usuario->name }} {{ $usuario->lastname }} - Oficina: {{$usuario->nombre}}
                                (Carnet vencido / Sin licencia)
                            </option>
                            @endif
                            @endforeach
                        </select>

                        <svg viewBox="0 0 16 16" fill="currentColor"
                            class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2
                                                size-4 text-gray-400">
                            <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" />
                        </svg>
                    </div>
                    @error('id_usuario')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

            <a href="{{route('reservas.internas')}}" class="inline-flex items-center justify-center
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
                                Editar
                            </button>
    </form>
</div>

@endsection