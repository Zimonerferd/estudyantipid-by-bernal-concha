<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Estudyantipid')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&family=DM+Mono:wght@400&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/estudyantipid.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="auth">
        <section class="auth__hero">
            <div class="auth__ring"></div>

            <div class="auth__lockup">
                <span class="auth__logo"><img src="{{ asset('images/et-logo-1.png') }}" alt=""></span>
                <span class="auth__wordmark">Estudyantipid</span>
            </div>

            <div class="auth__copy">
                <h1 class="auth__headline">Smarter<br>allowance,<br>smarter students.</h1>
                <p class="auth__tagline">Track every expense, split your allowance into categories, and stop running out of budget before the week ends.</p>
            </div>
        </section>

        <section class="auth__panel">
            @yield('content')
        </section>
    </div>
</body>
</html>
