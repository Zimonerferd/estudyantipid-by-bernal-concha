@extends('layouts.app')

@section('title', 'Monthly reports · Estudyantipid')

@php
    // ₱85, ₱1,400, ₱85.50 (decimals only when needed)
    $peso = fn ($n) => '₱' . (fmod((float) $n, 1) == 0 ? number_format($n) : number_format($n, 2));
    $maxWeek = max(1, collect($weeks)->max('amount'));
@endphp

@section('content')
    <div class="page-head">
        <header>
            <h1 class="page-title">Monthly reports</h1>
            <p class="page-sub">How your allowance went, month by month.</p>
        </header>

        <div class="month-nav">
            @if ($prevKey)
                <a href="{{ route('student.reports', ['month' => $prevKey]) }}" class="month-nav__btn" aria-label="Previous month">←</a>
            @else
                <span class="month-nav__btn is-disabled" aria-hidden="true">←</span>
            @endif

            <span class="month-nav__label">{{ $monthLabel }}</span>

            @if ($nextKey)
                <a href="{{ route('student.reports', ['month' => $nextKey]) }}" class="month-nav__btn" aria-label="Next month">→</a>
            @else
                <span class="month-nav__btn is-disabled" aria-hidden="true">→</span>
            @endif

            <a href="{{ route('student.reports.export', ['month' => $monthKey]) }}" class="btn-outline">Download CSV</a>
        </div>
    </div>

    {{-- Summary --}}
    <section class="stats">
        <div class="card stat stat--highlight">
            <span class="stat__label">Total spent</span>
            <span class="stat__value">{{ $peso($report->total()) }}</span>
            <span class="stat__note">{{ $report->count() }} {{ Str::plural('expense', $report->count()) }}</span>
        </div>

        <div class="card stat">
            <span class="stat__label">Daily average</span>
            <span class="stat__value">{{ $peso(round($report->dailyAverage())) }}</span>
            <span class="stat__note">over {{ $report->daysCounted() }} {{ Str::plural('day', $report->daysCounted()) }}</span>
        </div>

        <div class="card stat">
            <span class="stat__label">Saved</span>
            <span class="stat__value">{{ $peso($report->saved()) }}</span>
            <span class="stat__note">into savings goals</span>
        </div>

        <div class="card stat">
            <span class="stat__label">Vs. last month</span>
            @if (! $vsLastMonth['has_data'])
                <span class="stat__value">—</span>
                <span class="stat__note">No spending recorded last month</span>
            @elseif ($vsLastMonth['diff'] == 0)
                <span class="stat__value">0%</span>
                <span class="stat__note">Same as last month</span>
            @else
                <span class="stat__value">{{ $vsLastMonth['less'] ? '−' : '+' }}{{ abs($vsLastMonth['percent']) }}%</span>
                <span class="stat__note">You spent {{ $peso($vsLastMonth['diff']) }} {{ $vsLastMonth['less'] ? 'less' : 'more' }}</span>
            @endif
        </div>
    </section>

    <section class="grid-row">
        {{-- Spending by week --}}
        <div class="card grid-row__chart">
            <h2 class="card__title">Spending by week</h2>

            <div class="chart">
                @foreach ($weeks as $w)
                    <div class="chart__col" title="Days {{ $w['range'] }}">
                        <span class="chart__value">{{ number_format($w['amount']) }}</span>
                        <div class="chart__bar" style="height: {{ round($w['amount'] / $maxWeek * 100) }}px"></div>
                        <span class="chart__day">{{ $w['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Where it went --}}
        <div class="card grid-row__budgets">
            <h2 class="card__title">Where it went</h2>

            <div class="budgets">
                @forelse ($categories as $c)
                    <div class="budget">
                        <div class="budget__head">
                            <span class="budget__name">{{ $c['name'] }}</span>
                            <span class="budget__amt">{{ $peso($c['amount']) }} · {{ $c['percent'] }}%</span>
                        </div>
                        <div class="budget__track">
                            <div class="budget__fill" style="width: {{ $c['percent'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="expense-list__empty">No expenses recorded in {{ $monthLabel }}.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Biggest expenses --}}
    <section class="card">
        <h2 class="card__title">Biggest expenses</h2>

        <div class="expenses">
            @forelse ($top as $e)
                <div class="expense">
                    <div>
                        <p class="expense__title">{{ $e->title }}</p>
                        <p class="expense__meta">{{ $e->category }} · {{ $e->spent_on->format('M j') }}</p>
                    </div>
                    <span class="expense__amt">−{{ $peso($e->amount) }}</span>
                </div>
            @empty
                <p class="expense-list__empty">Nothing to show for {{ $monthLabel }}.</p>
            @endforelse
        </div>
    </section>
@endsection
