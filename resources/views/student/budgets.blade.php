@extends('layouts.app')

@section('title', 'Budgets · Estudyantipid')

@php
    // ₱85, ₱1,400, ₱85.50 (decimals only when needed)
    $peso = fn ($n) => '₱' . (fmod((float) $n, 1) == 0 ? number_format($n) : number_format($n, 2));
@endphp

@section('content')
    <header>
        <h1 class="page-title">Budgets</h1>
        <p class="page-sub">Split your allowance so food money stays food money.</p>
    </header>

    <section class="budget-grid">
        @foreach ($budgets as $b)
            <article class="card budget-card">
                <div class="budget-card__head">
                    <h2 class="budget-card__name">{{ $b['category'] }}</h2>

                    @if ($b['status'] === 'near')
                        <span class="badge badge--warn">Near limit</span>
                    @elseif ($b['status'] === 'over')
                        <span class="badge badge--warn">Over budget</span>
                    @endif
                </div>

                <p class="budget-card__pct">{{ $b['percent'] }}%</p>
                <p class="budget-card__amt">{{ $peso($b['spent']) }} of {{ $peso($b['limit']) }}</p>

                <div class="budget__track">
                    <div class="budget__fill {{ $b['status'] !== 'ok' ? 'is-warn' : '' }}"
                         style="width: {{ min(100, $b['percent']) }}%"></div>
                </div>
            </article>
        @endforeach
    </section>
@endsection
