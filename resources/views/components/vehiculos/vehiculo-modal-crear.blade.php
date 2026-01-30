<!-- resources/views/components/vehiculos/vehiculo-modal.blade.php -->

<div id="vehiculo-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full ">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800 z-50">
        <!-- Header -->
        <div class="flex justify-between items-center pb-3 border-b dark:border-gray-700">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modal-title">
                Alta De Vehículo
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Formulario -->
        <form id="vehiculo-form" class="mt-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Dominio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Dominio <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="dominio"
                        id="dominio"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="ABC123"
                    />
                </div>

                <!-- Marca -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Marca <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="marca"
                        id="marca"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="Toyota"
                    />
                </div>

                <!-- Modelo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Modelo <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="modelo"
                        id="modelo"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="Corolla"
                    />
                </div>

                <!-- Año -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Año <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="anio"
                        id="anio"
                        required
                        min="1900"
                        max="2099"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="2024"
                    />
                </div>

                <!-- Kilómetros -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Kilómetros <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="kilometros"
                        id="kilometros"
                        required
                        min="0"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="0"
                    />
                </div>

                <!-- VTV -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        VTV <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="VTV"
                        id="VTV"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    />
                </div>

                <!-- Dependencia Dueña -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Dependencia Dueña <span class="text-red-500">*</span>
                    </label>
                   <select name="id_dependencia_duena" id="id_dependencia_duena" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    >

</select>

                </div>

                <!-- Dirección Actual -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Dirección Actual <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="id_direccion_actual"
                        id="id_direccion_actual"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    >

                        <!-- Se llenarán dinámicamente -->
                    </select>
                </div>

                <!-- Estado Vehículo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Estado Vehículo <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="id_estado_vehiculo"
                        id="id_estado_vehiculo"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                        <!-- Se llenarán dinámicamente -->
                    </select>
                </div>

                <!-- Estado Nafta -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Estado Nafta <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="id_estado_nafta"
                        id="id_estado_nafta"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                        <!-- Se llenarán dinámicamente -->
                    </select>
                </div>

                <!-- Control Satelital -->
                <div class="flex items-center mt-6">
                    <input
                        type="checkbox"
                        name="control_satelital"
                        id="control_satelital"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                    <label for="control_satelital" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Control Satelital
                    </label>
                </div>

                <!-- Habilitado para Préstamo -->
                <div class="flex items-center mt-6">
                    <input
                        type="checkbox"
                        name="habilitado_prestamo"
                        id="habilitado_prestamo"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                    <label for="habilitado_prestamo" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Habilitado para Préstamo
                    </label>
                </div>

                <!-- Condiciones de Préstamo -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Condiciones de Préstamo
                    </label>
                    <textarea
                        name="condiciones_prestamo"
                        id="condiciones_prestamo"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="Especifique las condiciones..."
                    ></textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t dark:border-gray-700">
                <button
                    type="button"
                    onclick="closeModal()"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-lg transition-colors">
                    Cancelar
                </button>
                <button
                    type="button"
                    id="btn-Alta"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>


    @push('scripts')
        @vite(['resources/js/vehiculo.js'])
    @endpush

