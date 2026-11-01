<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin · Estudyantipid</title>
    <link rel="stylesheet" href="{{ asset('css/estudyantipid.css') }}">
</head>
<body style="padding:48px">
    <h1 class="page-title">Admin overview</h1>
    <p class="page-sub">Placeholder. We'll build this screen from Figma next.</p>
    <form method="POST" action="{{ route('logout') }}" style="margin-top:24px; max-width:200px">
        @csrf
        <button class="btn-signout">Sign out</button>
    </form>
</body>
</html>
