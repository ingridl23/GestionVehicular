<?php

namespace App\Providers;
use App\Models\Reserva;
use App\Policies\ReservaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{


protected $policies = [
    Reserva::class => ReservaPolicy::class,
];


    public function boot(): void
    {
        $this->registerPolicies();
    }
}
