@extends('layouts.app')

@section('title', 'Savings goals · Estudyantipid')

@php
    // ₱85, ₱1,400, ₱85.50 (decimals only when needed)
    $peso = fn ($n) => '₱' . (fmod((float) $n, 1) == 0 ? number_format($n) : number_format($n, 2));
@endphp

@section('content')
    <div class="page-head">
        <header>
            <h1 class="page-title">Savings goals</h1>
            <p class="page-sub">Set a slice of allowance aside for what matters.</p>
        </header>

        @if ($creating)
            <a href="{{ route('student.goals') }}" class="btn-lime">Cancel</a>
        @else
            <a href="{{ route('student.goals.create') }}" class="btn-lime">New goal</a>
        @endif
    </div>

    {{-- New goal form (only on /student/goals/new) --}}
    @if ($creating)
        <section class="card quick-add">
            <form method="POST" action="{{ route('student.goals.store') }}" class="quick-add__form" novalidate>
                @csrf

                <input type="text" name="name" value="{{ old('name') }}" maxlength="100" required autofocus
                       placeholder="Goal name" aria-label="Goal name"
                       class="quick-add__input">

                <input type="number" name="target_amount" value="{{ old('target_amount') }}" step="0.01" min="1" inputmode="decimal" required
                       placeholder="Target (₱)" aria-label="Target in pesos"
                       class="quick-add__input">

                <input type="number" name="saved_amount" value="{{ old('saved_amount') }}" step="0.01" min="0" inputmode="decimal"
                       placeholder="Saved so far (₱)" aria-label="Saved so far in pesos"
                       class="quick-add__input">

                <button type="submit" class="quick-add__btn">Save goal</button>
            </form>

            @if ($errors->any())
                <div class="quick-add__errors">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    <section class="goal-grid">
        @forelse ($goals as $goal)
            <article class="card goal-card">
                <h2 class="goal-card__name">{{ $goal->name }}</h2>
                <p class="goal-card__saved">{{ $peso($goal->saved_amount) }}</p>
                <p class="goal-card__target">of {{ $peso($goal->target_amount) }}</p>

                <div class="budget__track">
                    <div class="budget__fill" style="width: {{ $goal->percent() }}%"></div>
                </div>

                @if ($goal->isComplete())
                    <p class="goal-card__done">Goal reached</p>
                @else
                    <form method="POST" action="{{ route('student.goals.deposit', $goal) }}">
                        @csrf
                        <button type="submit" class="goal-card__add">+ Add ₱100</button>
                    </form>
                @endif
            </article>
        @empty
            <div class="card goal-empty">
                No savings goals yet. Press <strong>New goal</strong> to start one.
            </div>
        @endforelse
    </section>
@endsection
