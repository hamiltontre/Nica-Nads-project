<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Solo permitir estos emails (configurar en .env)
        $allowedEmails = explode(',', env('ADMIN_EMAILS'));
        
        if (!in_array(Auth::user()->email, $allowedEmails)) {
            abort(403, 'Acceso no autorizado');
        }
        
        return $next($request);
    }
}