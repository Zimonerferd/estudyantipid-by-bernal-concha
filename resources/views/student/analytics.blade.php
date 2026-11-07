@extends('layouts.app')

@section('title', 'Analytics · Estudyantipid')

@php
    // ₱85, ₱1,400, ₱85.50 (decimals only when needed)
    $peso = fn ($n) => '₱' . (fmod((float) $n, 1) == 0 ? number_format($n) : number_format($n, 2));
    $max = max(1, collect($daily)->max('amount'));
@endphp

@section('content')
    <div class="page-head">
        <header>
            <h1 class="page-title">Analytics</h1>
            <p class="page-sub">Your weekly spending habits.</p>
        </header>

        <a href="{{ route('student.reports') }}" class="btn-outline">Monthly reports</a>
    </div>

    {{-- Daily spend chart --}}
    <section class="card analytics-chart">
        <h2 class="card__title">Daily spend · this week</h2>

        <div class="chart">
            @foreach ($daily as $day)
                <div class="chart__col">
                    <span class="chart__value">{{ $day['amount'] }}</span>
                    <div class="chart__bar" style="height: {{ round($day['amount'] / $max * 100) }}px"></div>
                    <span class="chart__day">{{ $day['day'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Insights --}}
    <section class="insights">
        <div class="card stat">
            <span class="stat__label">Biggest day</span>
            <span class="stat__value">{{ $biggest['name'] ?? '—' }}</span>
            <span class="stat__note">{{ $biggest ? $peso($biggest['amount']) . ' spent' : 'No spending yet this week' }}</span>
        </div>

        <div class="card stat">
            <span class="stat__label">Top category</span>
            <span class="stat__value">{{ $topCategory['name'] ?? '—' }}</span>
            <span class="stat__note">{{ $topCategory ? $topCategory['percent'] . '% of spending' : 'No spending yet this week' }}</span>
        </div>

        <div class="card stat stat--highlight">
            <span class="stat__label">Vs. last week</span>
            @if (! $vsLastWeek['has_data'])
                <span class="stat__value">—</span>
                <span class="stat__note">No spending recorded last week</span>
            @elseif ($vsLastWeek['diff'] == 0)
                <span class="stat__value">0%</span>
                <span class="stat__note">Same as last week</span>
            @else
                <span class="stat__value">{{ $vsLastWeek['less'] ? '−' : '+' }}{{ abs($vsLastWeek['percent']) }}%</span>
                <span class="stat__note">You spent {{ $peso($vsLastWeek['diff']) }} {{ $vsLastWeek['less'] ? 'less' : 'more' }}</span>
            @endif
        </div>
    </section>
@endsection
