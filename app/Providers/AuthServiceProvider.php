<?php

namespace App\Providers;
use App\Models\Dependencia;
use App\Models\Reserva;
use App\Models\Reportes;
use App\Models\Gasto;
use App\Models\User;
use App\Models\Vehiculo;
use App\Policies\ReservaPolicy;
use App\Policies\DependenciaPolicy;
use App\Policies\GastoPolicy;
use App\Policies\ReportePolicy;
use App\Policies\UserPolicy;
use App\Policies\VehiculoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{


protected $policies = [
    Reserva::class => ReservaPolicy::class,
      Dependencia::class => DependenciaPolicy::class,
        Gasto::class => GastoPolicy::class,
          Reportes::class => ReportePolicy::class,
            User::class => UserPolicy::class,
              Vehiculo::class => VehiculoPolicy::class,
];


    public function boot(): void
    {
        $this->registerPolicies();
    }
}
