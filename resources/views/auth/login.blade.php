@extends('layouts.auth')

@section('title', 'Sign in · Estudyantipid')

@php
    $isAdmin = $role === 'admin';
@endphp

@section('content')
    <form method="POST" action="{{ $isAdmin ? route('admin.login') : route('login') }}" class="auth-form" novalidate>
        @csrf

        <h2 class="auth-form__title">Sign in</h2>
        <p class="auth-form__sub">Welcome back. Pick your account type to continue.</p>

        <div class="role-tabs">
            <a href="{{ route('login') }}" class="role-tabs__item {{ $isAdmin ? '' : 'is-active' }}">Student</a>
            <a href="{{ route('admin.login') }}" class="role-tabs__item {{ $isAdmin ? 'is-active' : '' }}">Admin</a>
        </div>

        <label class="field">
            <span class="field__label">Email</span>
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus
                   placeholder="{{ $isAdmin ? 'admin@snsu.edu.ph' : 'you@snsu.edu.ph' }}"
                   class="input @error('email') is-invalid @enderror">
            @error('email') <span class="field__error">{{ $message }}</span> @enderror
        </label>

        <label class="field field--last">
            <span class="field__label">Password</span>
            <input type="password" name="password" autocomplete="current-password" required
                   placeholder="••••••••"
                   class="input @error('password') is-invalid @enderror">
            @error('password') <span class="field__error">{{ $message }}</span> @enderror
        </label>

        <button type="submit" class="btn-primary">Sign in as {{ $isAdmin ? 'admin' : 'student' }}</button>

        <p class="auth-form__foot">
            @if ($isAdmin)
                Admin access is restricted to authorized staff.
            @else
                New student? <a href="{{ route('register') }}">Create an account</a>
            @endif
        </p>
    </form>
@endsection
