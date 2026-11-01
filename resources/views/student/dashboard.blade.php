@extends('layouts.app')

@section('title', 'Dashboard · Estudyantipid')

@php
    // ₱85, ₱1,400, ₱85.50 (decimals only when needed)
    $peso = fn ($n) => '₱' . (fmod((float) $n, 1) == 0 ? number_format($n) : number_format($n, 2));
@endphp

@section('content')
    <header>
        <h1 class="page-title">Kumusta, {{ $firstName }}!</h1>
        <p class="page-sub">Week of {{ $weekStart }} · allowance resets Sunday</p>
    </header>

    {{-- Summary stats --}}
    <section class="stats">
        <div class="card stat stat--highlight">
            <span class="stat__label">Left this week</span>
            <span class="stat__value">{{ $peso($stats['left']) }}</span>
            <span class="stat__note">of {{ $peso($stats['allowance']) }} allowance</span>
        </div>
        <div class="card stat">
            <span class="stat__label">Spent today</span>
            <span class="stat__value">{{ $peso($stats['spent_today']) }}</span>
            <span class="stat__note">{{ $stats['expenses_today'] }} {{ Str::plural('expense', $stats['expenses_today']) }}</span>
        </div>
        <div class="card stat">
            <span class="stat__label">Daily safe spend</span>
            <span class="stat__value">{{ $peso($stats['safe_spend']) }}</span>
            <span class="stat__note">to last until Sunday</span>
        </div>
        <div class="card stat">
            <span class="stat__label">Saved</span>
            <span class="stat__value">{{ $peso($stats['saved']) }}</span>
            <span class="stat__note">
                @if ($stats['goal_name'])
                    {{ $stats['goal_progress'] }}% to {{ $stats['goal_name'] }}
                @else
                    No savings goals yet
                @endif
            </span>
        </div>
    </section>

    <section class="grid-row">
        {{-- Weekly spending chart --}}
        <div class="card grid-row__chart">
            <h2 class="card__title">Spending this week</h2>
            @php $max = max(1, collect($weekly)->max('amount')); @endphp
            <div class="chart">
                @foreach ($weekly as $day)
                    <div class="chart__col">
                        <span class="chart__value">{{ $day['amount'] }}</span>
                        <div class="chart__bar" style="height: {{ round($day['amount'] / $max * 100) }}px"></div>
                        <span class="chart__day">{{ $day['day'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Budget categories --}}
        <div class="card grid-row__budgets">
            <h2 class="card__title">Budget categories</h2>
            <div class="budgets">
                @foreach ($budgets as $b)
                    <div class="budget">
                        <div class="budget__head">
                            <span class="budget__name">{{ $b['category'] }}</span>
                            <span class="budget__amt">{{ $peso($b['spent']) }} / {{ $peso($b['limit']) }}</span>
                        </div>
                        <div class="budget__track">
                            <div class="budget__fill {{ $b['status'] !== 'ok' ? 'is-warn' : '' }}"
                                 style="width: {{ min(100, $b['percent']) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Recent expenses --}}
    <section class="card">
        <h2 class="card__title">Recent expenses</h2>
        <div class="expenses">
            @forelse ($recent as $e)
                <div class="expense">
                    <div>
                        <p class="expense__title">{{ $e['title'] }}</p>
                        <p class="expense__meta">{{ $e['category'] }} · {{ $e['date'] }}</p>
                    </div>
                    <span class="expense__amt">−{{ $peso($e['amount']) }}</span>
                </div>
            @empty
                <p class="expense-list__empty">No expenses yet. Add your first one on the Expenses page.</p>
            @endforelse
        </div>
    </section>
@endsection
