<!-- resources/views/components/vehiculos/vehiculo-modal.blade.php -->

<div id="vehiculo-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
        <!-- Header -->
        <div class="flex justify-between items-center pb-3 border-b dark:border-gray-700">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modal-title">
                Agregar Vehículo
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
                    <select
                        name="id_dependencia_duena"
                        id="id_dependencia_duena"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Seleccione...</option>
                        <!-- Se llenarán dinámicamente -->
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
                        <option value="">Seleccione...</option>
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
                        <option value="">Seleccione...</option>
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
                        <option value="">Seleccione...</option>
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
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-lg transition-colors"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                >
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(vehiculo = null) {
    const modal = document.getElementById('vehiculo-modal');
    const form = document.getElementById('vehiculo-form');
    const title = document.getElementById('modal-title');

    if (vehiculo) {
        title.textContent = 'Editar Vehículo';
        // Llenar el formulario con los datos del vehículo
        Object.keys(vehiculo).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = vehiculo[key];
                } else {
                    input.value = vehiculo[key];
                }
            }
        });
    } else {
        title.textContent = 'Agregar Vehículo';
        form.reset();
    }

    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('vehiculo-modal');
    modal.classList.add('hidden');
}
</script>
