@extends('layouts.app')

@section('title', 'Message us · Estudyantipid')

@php
    // "Today", "Yesterday", or "Oct 8"
    $when = function ($date) {
        if ($date->isToday()) return 'Today';
        if ($date->isYesterday()) return 'Yesterday';
        return $date->format('M j');
    };
@endphp

@section('content')
    <header>
        <h1 class="page-title">Message us</h1>
        <p class="page-sub">Report a problem, share a suggestion, or write to the admin.</p>
    </header>

    <section class="msg-grid">
        {{-- Compose --}}
        <div class="card msg-compose">
            <nav class="seg" aria-label="Message type">
                @foreach ($types as $key => $t)
                    <a href="{{ route('student.messages', ['type' => $key]) }}"
                       class="seg__item {{ $key === $type ? 'is-active' : '' }}">{{ $t['label'] }}</a>
                @endforeach
            </nav>

            <p class="msg-blurb">{{ $types[$type]['blurb'] }}</p>

            <form method="POST" action="{{ route('student.messages.store') }}" class="msg-form" novalidate>
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">

                <label class="msg-field">
                    <span class="msg-label">Subject</span>
                    <input type="text" name="subject" value="{{ old('subject') }}" maxlength="150" required
                           class="msg-input">
                    @error('subject') <span class="msg-error">{{ $message }}</span> @enderror
                </label>

                <label class="msg-field msg-field--last">
                    <span class="msg-label">Message</span>
                    <textarea name="body" required class="msg-input">{{ old('body') }}</textarea>
                    @error('body') <span class="msg-error">{{ $message }}</span> @enderror
                </label>

                <div>
                    <button type="submit" class="btn-lime">{{ $types[$type]['button'] }}</button>
                </div>
            </form>
        </div>

        {{-- Your messages --}}
        <div class="card msg-inbox">
            <h2 class="card__title">Your messages</h2>

            <div class="msg-list">
                @forelse ($messages as $m)
                    <div class="msg-item">
                        <div class="msg-item__top">
                            <span class="msg-item__title">{{ $m->subject }}</span>
                            <span class="msg-item__date">{{ $when($m->created_at) }}</span>
                        </div>

                        <span class="msg-item__type">{{ ucfirst($m->type) }}</span>

                        @if ($m->admin_reply)
                            <div class="msg-reply"><strong>Admin:</strong> {{ $m->admin_reply }}</div>
                        @else
                            <span class="msg-item__status">Awaiting reply</span>
                        @endif
                    </div>
                @empty
                    <p class="expense-list__empty">You haven't sent anything yet.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
