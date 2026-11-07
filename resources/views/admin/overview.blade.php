@extends('layouts.admin')

@section('title', 'Overview · Estudyantipid Admin')

@php
    // 1,284 stays 1,284; 48,200 becomes 48.2k
    $abbr = fn ($n) => $n >= 1000
        ? rtrim(rtrim(number_format($n / 1000, 1), '0'), '.') . 'k'
        : number_format($n);

    $peso = fn ($n) => '₱' . number_format($n);
@endphp

@section('content')
    <header>
        <h1 class="page-title">System overview</h1>
        <p class="page-sub">Estudyantipid · Surigao del Norte State University</p>
    </header>

    {{-- Top cards --}}
    <section class="stats">
        <div class="card stat stat--highlight">
            <span class="stat__label">Registered students</span>
            <span class="stat__value">{{ number_format($students) }}</span>
            <span class="stat__note">+{{ number_format($newThisMonth) }} this month</span>
        </div>
        <div class="card stat">
            <span class="stat__label">Expenses logged</span>
            <span class="stat__value">{{ $abbr($expenseCount) }}</span>
            <span class="stat__note">all time</span>
        </div>
        <div class="card stat">
            <span class="stat__label">Unread feedback</span>
            <span class="stat__value">{{ number_format($unread) }}</span>
            <span class="stat__note">waiting for you</span>
        </div>
        <div class="card stat">
            <span class="stat__label">Avg. savings rate</span>
            <span class="stat__value">{{ $savingsRate }}%</span>
            <span class="stat__note">of allowance, this week</span>
        </div>
    </section>

    <section class="grid-row grid-row--admin">
        {{-- Campus spend by day --}}
        <div class="card grid-row__chart">
            <h2 class="card__title">Campus spend by day</h2>

            <div class="acs">
                <div class="acs__axis">
                    <span>{{ $peso($top) }}</span>
                    <span>{{ $peso($top / 2) }}</span>
                    <span>₱0</span>
                </div>

                <div class="acs__plot">
                    <div class="acs__grid"><i></i><i></i><i></i></div>

                    <div class="acs__cols">
                        @foreach ($days as $d)
                            <div class="acs__col">
                                <div class="acs__bar-wrap">
                                    <span class="acs__val">{{ number_format($d['amount']) }}</span>
                                    <div class="acs__bar {{ $max > 0 && $d['amount'] == $max ? 'is-peak' : '' }}"
                                         style="height: {{ round($d['amount'] / $top * 196) }}px"></div>
                                </div>
                                <span class="acs__day">{{ $d['day'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent activity --}}
        <div class="card activity">
            <h2 class="card__title">Recent activity</h2>

            <div class="activity__list">
                @forelse ($activity as $a)
                    <div class="activity__item">
                        <span class="activity__text">{{ $a->description }}</span>
                        <span class="activity__time">{{ \App\Support\TimeAgo::format($a->created_at, compact: true) }}</span>
                    </div>
                @empty
                    <p class="expense-list__empty">Nothing yet. Suspending a student or replying to feedback shows up here.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
