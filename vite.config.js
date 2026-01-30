import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/filtros/filtrosReservas.js',
                'resources/css/filtrosReservas.css',
                'resources/js/reservas/accionesReserva.js',
            ],
            refresh: true,
        }),
    ],
});
