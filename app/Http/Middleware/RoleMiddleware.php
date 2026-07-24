<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Jika user belum login, atau role-nya tidak sesuai dengan yang diminta (admin)
        if (!Auth::check() || Auth::user()->role !== $role) {
            abort(403, 'Akses Ditolak: Anda bukan Admin.');
        }

        return $next($request);
    }
}
