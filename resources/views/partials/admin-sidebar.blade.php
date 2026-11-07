@php
    $nav = [
        ['route' => 'admin.overview',   'icon' => '◉', 'label' => 'Overview'],
        ['route' => 'admin.students',   'icon' => '☰', 'label' => 'Students'],
        ['route' => 'admin.categories', 'icon' => '◔', 'label' => 'Categories'],
        ['route' => 'admin.feedback',   'icon' => '✉', 'label' => 'Feedback'],
    ];
@endphp

<aside class="aside">
    <a href="{{ route('admin.overview') }}" class="brand">
        <span class="brand__mark">
            <span class="brand__logo">
                <img src="{{ asset('images/et-logo.png') }}" alt="">
            </span>
        </span>
        <span class="brand__name">Estudyantipid</span>
    </a>

    <span class="role-pill">Admin</span>

    <nav class="nav">
        @foreach ($nav as $item)
            <a href="{{ route($item['route']) }}"
               class="nav__item {{ request()->routeIs($item['route'], $item['route'] . '.*') ? 'is-active' : '' }}">
                <span class="nav__icon">{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="aside__spacer"></div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-signout">Sign out</button>
    </form>
</aside>
