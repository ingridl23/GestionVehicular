<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Alerta;
use Illuminate\Support\Facades\View;

use Illuminate\Support\Facades\Auth;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         View::composer('*', function ($view) {

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $alertas = Alerta::where('activa', true)
            ->where(function ($query) use ($user) {

                // Alertas generales
                $query->whereNull('entidad_tipo');

                // Alertas por dependencia
                if ($user->dependencia_id) {
                    $query->orWhere(function ($q) use ($user) {
                        $q->where('entidad_tipo', 'dependencia')
                          ->where('entidad_id', $user->dependencia_id);
                    });
                }

                // Alertas por usuario (si más adelante las agregás)
                // $query->orWhere('entidad_tipo', 'usuario')
                //       ->where('entidad_id', $user->id);

            })
            ->orderByDesc('fecha_generada')
            ->limit(5)
            ->get();

        $view->with('alertas', $alertas);
    });
    }
}
