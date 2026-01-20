<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\LoginViewResponse;

use App\Http\Responses\LoginResponse as CustomLoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar respuestas personalizadas
          $this->app->singleton(
        LoginViewResponse::class,

    );

    $this->app->singleton(
        LoginResponse::class,
        CustomLoginResponse::class
    );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Vista personalizada de login
        Fortify::loginView(function () {
            return view('welcome');
        });

        // Vista de recuperación de contraseña (opcional)
        // Fortify::requestPasswordResetLinkView(function () {
        //     return view('auth.forgot-password');
        // });

        // Fortify::resetPasswordView(function (Request $request) {
        //     return view('auth.reset-password', ['request' => $request]);
        // });

        // Personalizar autenticación (honeypot)
        Fortify::authenticateUsing(function (Request $request) {
            // Verificar honeypot (anti-bot)
            if ($request->filled('oculto')) {
                return null;
            }

            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && \Hash::check($request->password, $user->password)) {
                return $user;
            }

            return null;
        });

        // Rate limiting para login
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        // Rate limiting para two-factor
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
