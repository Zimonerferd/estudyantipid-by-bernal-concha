<?php
// In bootstrap/app.php, replace the existing ->withMiddleware(...) call with this:

use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Http\Request;

->withMiddleware(function (Middleware $middleware) {
    $middleware->alias(['role' => EnsureUserHasRole::class]);

    // Not logged in -> go to the matching login page
    $middleware->redirectGuestsTo(fn (Request $request) =>
        $request->is('admin*') ? route('admin.login') : route('login')
    );

    // Already logged in and visiting /login -> go to your own home
    $middleware->redirectUsersTo(fn () =>
        auth()->user()?->role === 'admin' ? route('admin.overview') : route('student.dashboard')
    );
})
