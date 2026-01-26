<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Alerta;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Solo compartir con navbar y layout principal
        View::composer(['layout.navbar', 'layout.app'], function ($view) {

            $user = Auth::user();

            if (!$user) {
                $view->with('alertas', collect([]));
                $view->with('alertasCount', 0);
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
                })
                ->orderByDesc('fecha_generada')
                ->limit(5)
                ->get();

            $alertasCount = $alertas->count();

            $view->with('alertas', $alertas);
            $view->with('alertasCount', $alertasCount);
        });
    }
}
