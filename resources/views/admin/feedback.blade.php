@extends('layouts.admin')

@section('title', 'Feedback · Estudyantipid Admin')

@php
    $totalUnread = (int) $unread->sum();
    $firstName = fn ($name) => explode(' ', trim($name))[0];
@endphp

@section('content')
    <header>
        <h1 class="page-title">Feedback</h1>
        <p class="page-sub">Reports, suggestions and mail from students. Open a message to reply.</p>
    </header>

    {{-- Filter tabs (the number is how many are unread) --}}
    <nav class="fb-tabs" aria-label="Filter feedback">
        <a href="{{ route('admin.feedback') }}" class="fb-tab {{ $filter === null ? 'is-active' : '' }}">
            All
            @if ($totalUnread > 0)<span class="fb-tab__badge">{{ $totalUnread }}</span>@endif
        </a>

        @foreach ($types as $key => $t)
            @php $count = (int) $unread->get($key, 0); @endphp
            <a href="{{ route('admin.feedback', ['type' => $key]) }}" class="fb-tab {{ $filter === $key ? 'is-active' : '' }}">
                {{ Str::plural($t['label']) }}
                @if ($count > 0)<span class="fb-tab__badge">{{ $count }}</span>@endif
            </a>
        @endforeach
    </nav>

    @if ($messages->isEmpty())
        <section class="card fb-empty">Nothing here yet. Messages students send will show up in this list.</section>
    @else
        <section class="fb-grid">
            {{-- Message list --}}
            <div class="card fb-list">
                @foreach ($messages as $m)
                    @php $isUnread = $m->read_at === null; @endphp

                    <a href="{{ route('admin.feedback', array_filter(['type' => $filter, 'message' => $m->id])) }}"
                       class="fb-item {{ $selected && $selected->id === $m->id ? 'is-selected' : '' }} {{ $isUnread ? 'is-unread' : '' }}">
                        <span class="fb-item__top">
                            <span class="fb-item__who">
                                @if ($isUnread)<i class="fb-dot"></i>@endif
                                {{ $m->user->name }}
                            </span>
                            <span class="fb-item__time">{{ \App\Support\TimeAgo::format($m->created_at) }}</span>
                        </span>

                        <span class="fb-item__subject">{{ $m->subject }}</span>

                        <span class="fb-item__meta">
                            <span class="pill pill--{{ $m->type }}">{{ ucfirst($m->type) }}</span>
                            @if ($m->admin_reply)<span class="fb-item__flag">↩ Replied</span>@endif
                            @if ($m->resolved_at)<span class="fb-item__flag fb-item__flag--ok">✓ Resolved</span>@endif
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- Open message --}}
            @if ($selected)
                <div class="card fb-detail">
                    <div class="fb-detail__head">
                        <div>
                            <span class="pill pill--{{ $selected->type }}">{{ ucfirst($selected->type) }}</span>
                            <h2 class="fb-detail__title">{{ $selected->subject }}</h2>
                            <p class="fb-detail__meta">
                                {{ $selected->user->name }}
                                @if ($selected->user->college) · {{ $selected->user->college }} @endif
                                · {{ \App\Support\TimeAgo::format($selected->created_at) }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('admin.feedback.resolve', $selected) }}">
                            @csrf
                            @if ($filter)<input type="hidden" name="type" value="{{ $filter }}">@endif
                            <button type="submit" class="btn-outline">{{ $selected->resolved_at ? 'Reopen' : 'Mark resolved' }}</button>
                        </form>
                    </div>

                    <div class="fb-bubble">{{ $selected->body }}</div>

                    @if ($selected->admin_reply)
                        <div class="fb-reply">
                            <span class="fb-reply__label">Your reply · {{ \App\Support\TimeAgo::format($selected->replied_at) }}</span>
                            <div class="fb-reply__bubble">{{ $selected->admin_reply }}</div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.feedback.reply', $selected) }}" class="fb-form" novalidate>
                        @csrf
                        @if ($filter)<input type="hidden" name="type" value="{{ $filter }}">@endif

                        <label class="msg-field msg-field--last">
                            <span class="msg-label">{{ $selected->admin_reply ? 'Update your reply to' : 'Reply to' }} {{ $firstName($selected->user->name) }}</span>
                            <textarea name="reply" required placeholder="Write your reply…" class="msg-input fb-textarea">{{ old('reply', $selected->admin_reply) }}</textarea>
                            @error('reply') <span class="msg-error">{{ $message }}</span> @enderror
                        </label>

                        <div>
                            <button type="submit" class="btn-lime">{{ $selected->admin_reply ? 'Update reply' : 'Send reply' }}</button>
                        </div>
                    </form>
                </div>
            @endif
        </section>
    @endif
@endsection
