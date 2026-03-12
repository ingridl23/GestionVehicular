// calculadora.js

document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('calc-litros')) return;

    console.log('js calculadora cargado');

    const PRECIO_ACTUAL = window.PRECIO_ACTUAL || null;

    // -------------------------------------------------------
    // Exponer en window para que los onclick del blade funcionen
    // -------------------------------------------------------

    window.usarPrecioActual = function() {
        if (PRECIO_ACTUAL) {
            document.getElementById('calc-precio').value = PRECIO_ACTUAL;
        }
    };

    window.calcularGasto = function() {
        const litros = parseFloat(document.getElementById('calc-litros').value);
        const precio = parseFloat(document.getElementById('calc-precio').value);
        const km = parseFloat(document.getElementById('calc-km').value);

        const vacio = document.getElementById('resultado-vacio');
        const datos = document.getElementById('resultado-datos');
        const error = document.getElementById('resultado-error');
        const errorMsg = document.getElementById('resultado-error-msg');

        // Reset estado
        vacio.classList.add('hidden');
        datos.classList.add('hidden');
        error.classList.add('hidden');

        // Validaciones
        if (isNaN(litros) || litros <= 0) {
            error.classList.remove('hidden');
            errorMsg.textContent = 'Ingresá una cantidad de litros válida.';
            return;
        }
        if (isNaN(precio) || precio <= 0) {
            error.classList.remove('hidden');
            errorMsg.textContent = 'Ingresá un precio por litro válido.';
            return;
        }

        // Cálculo — igual que CalculoGastoService::calcularMonto()
        const monto = Math.round(litros * precio * 100) / 100;

        // Mostrar resultado
        datos.classList.remove('hidden');
        document.getElementById('resultado-monto').textContent =
            '$' + monto.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('resultado-detalle').textContent =
            `${litros} L × $${precio.toLocaleString('es-AR', { minimumFractionDigits: 2 })} por litro`;

        // Info extra con kilómetros
        const kmInfo = document.getElementById('resultado-km-info');
        if (!isNaN(km) && km > 0) {
            kmInfo.classList.remove('hidden');
            const costoPorKm = monto / km;
            const consumo = (litros / km) * 100; // L/100km
            document.getElementById('resultado-costo-km').textContent =
                '$' + costoPorKm.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '/km';
            document.getElementById('resultado-consumo').textContent =
                consumo.toFixed(1) + ' L/100km';
        } else {
            kmInfo.classList.add('hidden');
        }
    };

    window.limpiarCalculadora = function() {
        ['calc-litros', 'calc-precio', 'calc-km'].forEach(id => {
            document.getElementById(id).value = '';
        });
        if (PRECIO_ACTUAL) {
            document.getElementById('calc-precio').value = PRECIO_ACTUAL;
        }
        document.getElementById('resultado-vacio').classList.remove('hidden');
        document.getElementById('resultado-datos').classList.add('hidden');
        document.getElementById('resultado-error').classList.add('hidden');
    };

    // -------------------------------------------------------
    // Event listeners para calcular con Enter
    // -------------------------------------------------------
    ['calc-litros', 'calc-precio', 'calc-km'].forEach(id => {
        var el = document.getElementById(id);
        if (el) {
            el.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') window.calcularGasto();
            });
        }
    });
});
