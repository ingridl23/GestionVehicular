<?php

namespace App\Providers;

use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

// CONTRATOS
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

// IMPLEMENTACIÓN
use App\Http\Responses\LoginResponse as CustomLoginResponse;
use Illuminate\Validation\ValidationException;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //  Binding correcto: CONTRATO → IMPLEMENTACIÓN
        $this->app->singleton(
            LoginResponseContract::class,
            CustomLoginResponse::class
        );
    }

    public function boot(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forgot-password');
        });

        Fortify::resetPasswordView(function ($request) {
            return view('auth.reset-password', [
                'request' => $request
            ]);
        });

        // Vista de login
        Fortify::loginView(function () {
            return view('welcome');
        });



        // Autenticación personalizada (honeypot)
        Fortify::authenticateUsing(function (Request $request) {

            
             if ($request->filled('oculto')) {
                throw ValidationException::withMessages([
                    'email' => ['Credenciales inválidas.'],
                ]);
            }

            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && \Hash::check($request->password, $user->password)) {
                return $user;
            }

            throw ValidationException::withMessages([
                'email' => ['Las credenciales no coinciden con nuestros registros.'],
            ]);

        });

        // Rate limit login
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(
                Str::lower($request->input(Fortify::username())) . '|' . $request->ip()
            );
        });
    }
}
