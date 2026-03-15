<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Redirect to their own dashboard instead of showing a 403
        if ($user->role === 'superadmin') {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. Redirected to your dashboard.');
        }

        if ($user->role === 'bookman') {
            return redirect()->route('bookman.dashboard')->with('error', 'Access denied. Redirected to your dashboard.');
        }

        // Regular users get sent to game
        return redirect()->route('game.index')->with('error', 'Access denied.');
    }
}
