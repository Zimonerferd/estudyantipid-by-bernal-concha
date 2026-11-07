<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showStudent()
    {
        return view('auth.login', ['role' => 'student']);
    }

    public function showAdmin()
    {
        return view('auth.login', ['role' => 'admin']);
    }

    public function student(Request $request)
    {
        return $this->attempt($request, 'student');
    }

    public function admin(Request $request)
    {
        return $this->attempt($request, 'admin');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Passing 'role' to Auth::attempt() means a student's credentials
     * will NOT work on the admin form (and the other way around).
     */
    private function attempt(Request $request, string $role)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt([...$credentials, 'role' => $role])) {
            if (Auth::user()->suspended_at) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withErrors(['email' => 'This account has been suspended. Please contact the admin.'])
                    ->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(
                route($role === 'admin' ? 'admin.overview' : 'student.dashboard')
            );
        }

        return back()
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->onlyInput('email');
    }
}
