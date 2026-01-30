<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\JsonResponse;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
public function toResponse($request)
{
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    $user = auth()->user();

    if ($user->hasRole('Administrador General')) {
        return redirect()->route('dashboard');
    }

    if ($user->hasRole('Operativo')) {
        return redirect()->route('operativo.dashboard2');
    }

    abort(403);
}

}
