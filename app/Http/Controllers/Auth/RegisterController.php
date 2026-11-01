<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register', ['colleges' => config('estudyantipid.colleges')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'college'  => ['required', Rule::in(config('estudyantipid.colleges'))],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Role is set here, never read from the request, so nobody can sign up as admin.
        $user = User::create([
            'name'     => $data['name'],
            'college'  => $data['college'],
            'email'    => $data['email'],
            'password' => $data['password'], // hashed by the User model cast
            'role'     => 'student',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard');
    }
}
