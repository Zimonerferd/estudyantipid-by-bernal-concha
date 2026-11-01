@extends('layouts.app')

@section('title', 'Expenses · Estudyantipid')

@php
    // ₱85, ₱1,400, ₱85.50 (decimals only when needed)
    $peso = fn ($n) => '₱' . (fmod((float) $n, 1) == 0 ? number_format($n) : number_format($n, 2));
@endphp

@section('content')
    <header>
        <h1 class="page-title">Expenses</h1>
        <p class="page-sub">Log every peso the moment it leaves your wallet.</p>
    </header>

    {{-- Add expense --}}
    <section class="card quick-add">
        <form method="POST" action="{{ route('student.expenses.store') }}" class="quick-add__form" novalidate>
            @csrf

            <input type="text" name="title" value="{{ old('title') }}" maxlength="100" required
                   placeholder="What did you buy?" aria-label="What did you buy?"
                   class="quick-add__input">

            <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0.01" inputmode="decimal" required
                   placeholder="Amount (₱)" aria-label="Amount in pesos"
                   class="quick-add__input">

            <select name="category" required aria-label="Category" class="quick-add__input quick-add__select">
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>

            <button type="submit" class="quick-add__btn">Add expense</button>
        </form>

        @if ($errors->any())
            <div class="quick-add__errors">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
    </section>

    {{-- This week's expenses --}}
    <section class="card expense-list">
        <p class="expense-list__total">Total {{ $peso($total) }}</p>

        <div>
            @forelse ($expenses as $e)
                <div class="expense">
                    <div>
                        <p class="expense__title">{{ $e->title }}</p>
                        <p class="expense__meta">{{ $e->category }} · {{ $e->spent_on->format('M j') }}</p>
                    </div>
                    <span class="expense__amt">−{{ $peso($e->amount) }}</span>
                </div>
            @empty
                <p class="expense-list__empty">No expenses yet this week. Add your first one above.</p>
            @endforelse
        </div>
    </section>
@endsection
