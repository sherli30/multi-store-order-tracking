<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (empty($roles)) {
            $roles = ['administrator']; // Default fallback
        }

        $userRole = trim(strtolower(auth()->user()->role ?? ''));

        if (!in_array($userRole, $roles)) {
            return response()->view('errors.403', [
                'message' => 'Akses Ditolak: Halaman ini hanya untuk Administrator atau peran yang diizinkan.'
            ], 403);
        }

        return $next($request);
    }
}
