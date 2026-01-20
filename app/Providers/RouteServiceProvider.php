<?php

namespace App\Providers;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            //archivos de rutas por rol:
            if (file_exists(base_path('routes/adminGeneral.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/adminGeneral.php'));
            }

            if (file_exists(base_path('routes/duenioDependencia.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/duenioDependencia.php'));
            }

            if (file_exists(base_path('routes/operativoyJefeArea.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/operativoyJefeArea.php'));
            }
        });
    }
}
