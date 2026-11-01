@extends('layouts.auth')

@section('title', 'Create account · Estudyantipid')

@section('content')
    <form method="POST" action="{{ route('register') }}" class="auth-form" novalidate>
        @csrf

        <h2 class="auth-form__title">Create account</h2>
        <p class="auth-form__sub">Student accounts only. Start tracking your allowance today.</p>

        <label class="field">
            <span class="field__label">Full name</span>
            <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus
                   placeholder="Juan Dela Cruz"
                   class="input @error('name') is-invalid @enderror">
            @error('name') <span class="field__error">{{ $message }}</span> @enderror
        </label>

        <label class="field">
            <span class="field__label">College</span>
            <select name="college" required class="input @error('college') is-invalid @enderror">
                @foreach ($colleges as $college)
                    <option value="{{ $college }}" @selected(old('college') === $college)>{{ $college }}</option>
                @endforeach
            </select>
            @error('college') <span class="field__error">{{ $message }}</span> @enderror
        </label>

        <label class="field">
            <span class="field__label">Email</span>
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required
                   placeholder="you@snsu.edu.ph"
                   class="input @error('email') is-invalid @enderror">
            @error('email') <span class="field__error">{{ $message }}</span> @enderror
        </label>

        <label class="field">
            <span class="field__label">Password</span>
            <input type="password" name="password" autocomplete="new-password" required
                   placeholder="••••••••"
                   class="input @error('password') is-invalid @enderror">
            @error('password') <span class="field__error">{{ $message }}</span> @enderror
        </label>

        <label class="field field--last">
            <span class="field__label">Confirm password</span>
            <input type="password" name="password_confirmation" autocomplete="new-password" required
                   placeholder="••••••••" class="input">
        </label>

        <button type="submit" class="btn-primary">Create student account</button>

        <p class="auth-form__foot">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
    </form>
@endsection
