import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/filtros/filtrosReservas.js',
                'resources/js/filtros/filtrosDependencias.js',
                'resources/css/filtrosReservas.css',
                'resources/js/reservas/accionesReserva.js',
                'resources/js/scriptsOperativo.js',
                'resources/js/reportes.js',
                'resources/js/vehiculo.js',
                'resources/js/vehiculo-detalle.js',
                'resources/js/accionesDependencias.js',
                'resources/js/reservas/accionesAutorizarPrestamo.js',
            ],
            refresh: true,
        }),
    ],
});