<div>
    @props([
        'direcciones',
        'dependencia' => null,
        'dependencias_arbol',
    ])

    <!-- Nombre de la dependencia -->
    <div class="space-y-12">
        <div class="border-b border-gray-200 dark:border-white/10 pb-12">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Datos de la dependencia
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Ingrese el nombre identificatorio de la dependencia.
            </p>

            <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white">
                        Nombre
                    </label>
                    <div class="mt-2">
                        <div class="flex items-center rounded-md bg-white dark:bg-white/5
                                    border border-gray-300 dark:border-white/10
                                    focus-within:ring-2 focus-within:ring-indigo-500">
                            <input type="text" name="nombre" id="nombre"
                                class="block w-full bg-transparent py-2 px-3 text-gray-900 dark:text-white
                                       placeholder:text-gray-400 focus:outline-none sm:text-sm"
                                value="{{ old('nombre', $dependencia?->nombre) }}" required>
                        </div>
                    </div>
                    @error('nombre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Estado -->
    <div class="space-y-12">
        <div class="border-b border-gray-200 dark:border-white/10 pb-12">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Estado de la dependencia
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Indique si la dependencia se encuentra actualmente activa.
            </p>

            <div class="mt-8 grid grid-cols-1 sm:grid-cols-6">
                <div class="sm:col-span-4">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Estado
                    </label>

                    <div class="flex items-center gap-6 rounded-md bg-white dark:bg-white/5 border border-gray-300 dark:border-white/10 px-4 py-3"
                        style="width: max-content;">
                        <label class="flex items-center gap-2 text-sm text-gray-900 dark:text-white">
                            <input type="radio" name="activa" value="true"
                                @checked(old('activa', $dependencia?->activa) == 1)>
                            Activa
                        </label>

                        <label class="flex items-center gap-2 text-sm text-gray-900 dark:text-white">
                            <input type="radio" name="activa" value="false"
                                @checked(old('activa', $dependencia?->activa) == 0)>
                            Inactiva
                        </label>
                    </div>

                    @error('activa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Dependencia padre -->
    <div class="border-b border-gray-200 dark:border-white/10 pb-12 mt-3">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            Relación jerárquica
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Seleccione la dependencia padre si corresponde.
        </p>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-6">
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-900 dark:text-white">
                    Dependencia padre
                </label>
                <div class="mt-2 relative">
                    <select id="id_dependencia_padre" name="id_dependencia_padre"
                        class="w-full appearance-none rounded-md
                               bg-white dark:bg-white/5 text-gray-900 dark:text-white
                               border border-gray-300 dark:border-white/10
                               py-2 pl-3 pr-8 focus:ring-2 focus:ring-indigo-500 sm:text-sm
                               *:bg-white dark:*:bg-gray-800">
                            <option value=""
                                @selected(old('id_dependencia_padre', $dependencia?->id_dependencia_padre) === null)>
                                No posee dependencia padre
                            </option>
                        @foreach ($dependencias_arbol as $dependencia_arbol)
                            <option value="{{ $dependencia_arbol->id }}"
                                @selected(old('id_dependencia_padre', $dependencia?->id_dependencia_padre) == $dependencia_arbol->id)>
                                {{ $dependencia_arbol->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <svg viewBox="0 0 16 16" fill="currentColor"
                        class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 size-4 text-gray-400">
                        <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" />
                    </svg>
                </div>

                @error('id_dependencia_padre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Dirección -->
    <div class="border-b border-gray-200 dark:border-white/10 pb-12 mt-3">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            Dirección
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Seleccione la dirección donde se encuentra ubicada la dependencia.
        </p>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-6">
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-900 dark:text-white">
                    Dirección asignada
                </label>

                <div class="mt-2 relative">
                    <select id="id_direccion" name="id_direccion"
                        class="w-full appearance-none rounded-md
                               bg-white dark:bg-white/5 text-gray-900 dark:text-white
                               border border-gray-300 dark:border-white/10
                               py-2 pl-3 pr-8 focus:ring-2 focus:ring-indigo-500 sm:text-sm
                               *:bg-white dark:*:bg-gray-800"
                        onchange="toggleNuevaDireccion()">

                        <option value=""
                                @selected(old('id_direccion', $dependencia?->id_direccion) === null)>
                                    Seleccionar
                            </option>
                        @foreach ($direcciones as $direccion)
                            <option value="{{ $direccion->id }}"
                                @selected(old('id_direccion', $dependencia?->id_direccion) == $direccion->id)>
                                {{ $direccion->calle }} {{ $direccion->altura }} - {{ $direccion->ciudad }}
                            </option>
                        @endforeach

                        <option value="nueva" @selected(old('id_direccion') === 'nueva')>
                            Agregar nueva dirección
                        </option>
                    </select>

                    <svg viewBox="0 0 16 16" fill="currentColor"
                        class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 size-4 text-gray-400">
                        <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" />
                    </svg>

                    <div class="mt-4 flex flex-col gap-3" style="display: none;" id="nueva-direccion">
                        <div>
                            <label class="text-sm font-medium">Calle</label>
                            <input type="text" name="calle" class="mt-1 w-full rounded-md border-gray-300" value="{{ old('calle') }}">
                            @error('calle')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium">Altura</label>
                            <input type="number" name="altura" class="mt-1 w-full rounded-md border-gray-300" value="{{ old('altura') }}">
                            @error('altura')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium">Ciudad</label>
                            <input type="text" name="ciudad" class="mt-1 w-full rounded-md border-gray-300" value="{{ old('ciudad') }}">
                            @error('ciudad')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function toggleNuevaDireccion() {
        const select = document.getElementById('id_direccion');
        const bloque = document.getElementById('nueva-direccion');

        if (select.value === 'nueva') {
            bloque.style.display = "flex";
        } else {
             bloque.style.display = "none";
        }
    }
    document.addEventListener('DOMContentLoaded', toggleNuevaDireccion);
</script>