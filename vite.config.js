import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/viaje-operativo.css',
                'resources/css/filtrosReservas.css',
                'resources/js/app.js',
                'resources/js/filtros/filtrosReservas.js',
                'resources/js/filtros/filtrosDependencias.js',
                'resources/js/reservas/accionesReserva.js',
                'resources/js/scriptsOperativo.js',
                'resources/js/reportes.js',
                'resources/js/vehiculo.js',
                'resources/js/vehiculo-detalle.js',
                'resources/js/accionesDependencias.js',
                'resources/js/reservas/accionesAutorizarPrestamo.js',
                'resources/js/loader.js',
                'resources/js/simulador.js',
                'resources/js/alerta.js',
                'resources/js/bootstrap.js',
                'resources/js/calculadora.js',
                'resources/js/Campana.js',
                'resources/js/perfilGestionVehicular.js',
                'resources/js/users-modal.js',
                'resources/js/vehiculo-debug.js',
                'resources/js/viajeModal.js',
                'resources/css/loader.css',
                'resources/css/simulador.css',
                'resources/css/viaje-admin.css',
                'resources/css/viaje-operativo.css',
                'resources/css/viaje-operativo-partial.css',
                'public/css/operador.css'


            ],
            refresh: true,
        }),
    ],
});
