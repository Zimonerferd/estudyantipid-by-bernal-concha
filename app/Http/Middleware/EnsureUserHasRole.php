<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /** Usage: ->middleware('role:admin') */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        // A suspended account is signed out on its very next click.
        if ($user?->suspended_at) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'This account has been suspended. Please contact the admin.']);
        }

        if ($user?->role !== $role) {
            abort(403, 'You do not have access to this page.');
        }

        return $next($request);
    }
}
