<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Models\Alerta;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Integrations\Gps\Contracts\GpsProviderInterface;
use App\Integrations\Gps\GestyaProvider;
use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
        GpsProviderInterface::class,
        GestyaProvider::class
    );
    }

    public function boot(): void
    {
          URL::forceScheme('https');  //Nos aseguramos de que el protocolo sea https
        // Para el navbar y layout PRINCIPAL (admin)
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

        //  AGREGAR ESTO: Para el navbar y layout OPERATIVO
        View::composer([
            'layout.navbarOperativo',
            'layout.appOperativo',
            'operativo.*'
        ], function ($view) {

            $user = Auth::user();

            if (!$user) {
                $view->with('alertas', collect([]));
                return;
            }

            // Alertas para operativos (más simple, solo las últimas)
            $alertas = Alerta::where('activa', true)
                ->orderByDesc('fecha_generada')
                ->limit(10)
                ->get();

            $view->with('alertas', $alertas);
        });
    }
}
