<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestauranteAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('restaurante_logado')) {
            return redirect()->route('restaurante.login');
        }

        return $next($request);
    }
}
