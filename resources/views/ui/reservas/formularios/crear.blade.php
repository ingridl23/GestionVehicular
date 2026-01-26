@extends('layout.app')

@section('content')
<section class="bg-gray-100 dark:bg-gray-900 py-10 lg:py-0">
    <div class="mx-auto px-0">
        <div class="-mx-4 flex flex-wrap">
            <div class="w-full">
                <div class="max-w-full overflow-x-auto">
                    <form action="{{ route('reservas.crear') }}" method="post">
                        @csrf

                        <div class="space-y-12">
                            <div class="border-b border-gray-200 dark:border-white/10 pb-12">
                                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                                    Período de la reserva
                                </h2>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Defina el rango de fechas y horarios durante los cuales se utilizará el vehículo.
                                </p>

                                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                    <!-- Fecha inicio -->
                                    <div class="sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                            Inicio de la reserva
                                        </label>
                                        <div class="mt-2">
                                            <div class="flex items-center rounded-md bg-white dark:bg-white/5
                                                border border-gray-300 dark:border-white/10 focus-within:ring-2 focus-within:ring-indigo-500">
                                                <input type="datetime-local" name="fecha_inicio" id="fecha_inicio"
                                                    class="block w-full bg-transparent py-2 px-3
                                                    text-gray-900 dark:text-white
                                                    placeholder:text-gray-400
                                                    focus:outline-none sm:text-sm" value="{{ old('fecha_inicio')}}">
                                            </div>
                                        </div>
                                        @error('fecha_inicio')
                                            <p class="text-red-600 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Fecha fin -->
                                    <div class="sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                            Fin de la reserva
                                        </label>
                                        <div class="mt-2">
                                            <div class="flex items-center rounded-md bg-white dark:bg-white/5
                                                border border-gray-300 dark:border-white/10 focus-within:ring-2 focus-within:ring-indigo-500">
                                                <input type="datetime-local" name="fecha_fin" id="fecha_fin"
                                                    class="block w-full bg-transparent py-2 px-3 text-gray-900 dark:text-white
                                                    placeholder:text-gray-400 focus:outline-none sm:text-sm" value="{{ old('fecha_fin')}}">
                                            </div>
                                        </div>
                                        @error('fecha_fin')
                                            <p class="text-red-600 text-sm">{{ $message }}</p>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="border-b border-gray-200 dark:border-white/10 pb-12 mt-3">
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                                Asignación de la reserva
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Seleccione el vehículo y el usuario responsable durante el período de la reserva.
                            </p>

                            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                
                                <!-- Vehículo -->
                                <div class="sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                        Vehículo asignado
                                    </label>
                                    <div class="mt-2 relative">
                                        <select id="vehiculo" name="id_vehiculo" class="w-full appearance-none rounded-md
                                            bg-white dark:bg-white/5 text-gray-900 dark:text-white
                                            border border-gray-300 dark:border-white/10 py-2 pl-3 pr-8
                                            focus:ring-2 focus:ring-indigo-500 sm:text-sm *:bg-white dark:*:bg-gray-800">
                                            <option {{ old('id_vehiculo') === null || old('id_vehiculo') === 'default' ? 'selected' : '' }}>Seleccionar</option>
                                            @foreach ($vehiculos as $vehiculo)
                                                @if($vehiculo->estado_vehiculo->estado == 'DISPONIBLE')
                                                    <option value="{{ $vehiculo->id }}" {{ old('id_vehiculo') == $vehiculo->id ? 'selected' : '' }}>
                                                        {{ $vehiculo->dominio }} {{ $vehiculo->marca }}
                                                        {{ $vehiculo->modelo }} {{ $vehiculo->anio }}
                                                    </option>
                                                @else
                                                    <option value="" disabled class="text-gray-400">
                                                        {{ $vehiculo->dominio }} {{ $vehiculo->marca }}
                                                        {{ $vehiculo->modelo }} {{ $vehiculo->anio }} ({{$vehiculo->estado_vehiculo->estado}})
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <svg viewBox="0 0 16 16" fill="currentColor"
                                            class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2
                                            size-4 text-gray-400">
                                            <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z"/>
                                        </svg>
                                    </div>
                                    @error('id_vehiculo')
                                        <p class="text-red-600 text-sm">{{ $message }}</p>
                                    @enderror

                                </div>


                                <!-- Usuario -->
                                <div class="sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-900 dark:text-white">
                                        Usuario responsable
                                    </label>
                                    <div class="mt-2 relative">
                                        <select id="usuario" name="id_usuario" class="w-full appearance-none rounded-md
                                            bg-white dark:bg-white/5 text-gray-900 dark:text-white
                                            border border-gray-300 dark:border-white/10 py-2 pl-3 pr-8
                                            focus:ring-2 focus:ring-indigo-500 sm:text-sm
                                            *:bg-white dark:*:bg-gray-800">
                                            <option value="default" {{ old('id_usuario') === null || old('id_usuario') === 'default' ? 'selected' : '' }}>Seleccionar</option>
                                            @foreach ($usuarios as $usuario)
                                            <p>{{$usuario}}</p>
                                                @if(!$usuario->carnet_vencido)
                                                     <option value="{{ $usuario->id }}" {{ old('id_usuario') == $usuario->id ? 'selected' : '' }}>
                                                    {{ $usuario->name }} {{ $usuario->lastname }}
                                                </option>
                                                @else
                                                <option value="" disabled class="text-gray-400">
                                                    {{ $usuario->name }} {{ $usuario->lastname }} (Carnet de conducir vencido/No tiene licencia)
                                                </option>
                                                @endif
                                               
                                            @endforeach
                                        </select>

                                        <svg viewBox="0 0 16 16" fill="currentColor" class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2
                                            size-4 text-gray-400">
                                            <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z"/>
                                        </svg>
                                        
                                    </div>
                                    @error('id_usuario')
                                        <p class="text-red-600 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-x-6">
                            <button type="button" class="text-sm font-semibold text-gray-700 dark:text-white">
                                Cancelar
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
