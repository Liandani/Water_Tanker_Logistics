<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();

        if (!in_array($user->role, $roles)) {
            // Redirect to their respective dashboard instead of showing raw error
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard')->with('error', 'Akses tidak diizinkan untuk peran Anda.'),
                'driver' => redirect()->route('driver.dashboard')->with('error', 'Akses tidak diizinkan untuk peran Anda.'),
                'customer' => redirect()->route('customer.dashboard')->with('error', 'Akses tidak diizinkan untuk peran Anda.'),
                default => abort(403, 'Unauthorized action.'),
            };
        }

        // Driver verification check
        if ($user->role === 'driver' && $user->driver) {
            if ($user->driver->verification_status === 'pending' && !$request->routeIs('driver.pending') && !$request->routeIs('logout')) {
                return redirect()->route('driver.pending');
            }
        }

        return $next($request);
    }
}

