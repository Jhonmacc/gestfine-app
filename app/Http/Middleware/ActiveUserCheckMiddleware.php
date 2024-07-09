<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ActiveUserCheckMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (!$user->ativo) {
                Auth::guard('web')->logout(); // Logout usando o guard 'web'
                $request->session()->invalidate(); // Invalida a sessão
                $request->session()->regenerateToken(); // Regenera o token de sessão

                return redirect()->route('login')->withErrors(['email' => 'Seu usuário foi desativado no sistema.']);
            }
        }

        return $next($request);
    }
}
