@php
    // Each item links to its named route once that page exists; until then it falls back to "#".
    $link = fn (string $name) => Route::has($name) ? route($name) : '#';

    $nav = [
        ['route' => 'student.dashboard', 'icon' => '◉', 'label' => 'Dashboard'],
        ['route' => 'student.expenses',  'icon' => '↗', 'label' => 'Expenses'],
        ['route' => 'student.budgets',   'icon' => '◔', 'label' => 'Budgets'],
        ['route' => 'student.goals',     'icon' => '✦', 'label' => 'Savings goals'],
        ['route' => 'student.analytics', 'icon' => '▥', 'label' => 'Analytics'],
    ];
@endphp

<aside class="aside">
    <a href="{{ $link('student.dashboard') }}" class="brand">
        <span class="brand__mark">
            <span class="brand__logo">
                <img src="{{ asset('images/et-logo.png') }}" alt="">
            </span>
        </span>
        <span class="brand__name">Estudyantipid</span>
    </a>

    <span class="role-pill">Student</span>

    <nav class="nav">
        @foreach ($nav as $item)
            <a href="{{ $link($item['route']) }}"
               class="nav__item {{ request()->routeIs($item['route'], $item['route'] . '.*') ? 'is-active' : '' }}">
                <span class="nav__icon">{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <a href="{{ $link('student.messages') }}" class="btn-message">Message us / Report</a>

    <div class="aside__spacer"></div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-signout">Sign out</button>
    </form>
</aside>
