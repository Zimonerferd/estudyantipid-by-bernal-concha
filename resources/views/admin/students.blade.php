@extends('layouts.admin')

@section('title', 'Students · Estudyantipid Admin')

@php
    $peso = fn ($n) => '₱' . number_format($n);
@endphp

@section('content')
    <div class="page-head">
        <header>
            <h1 class="page-title">Students</h1>
            <p class="page-sub">Manage accounts and access.</p>
        </header>

        <form method="GET" action="{{ route('admin.students') }}">
            <input type="search" name="q" value="{{ $search }}" placeholder="Search students…"
                   aria-label="Search students" class="admin-search">
        </form>
    </div>

    <section class="card admin-card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>College</th>
                        <th>Spent (mo.)</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $s)
                        <tr>
                            <td>
                                <p class="table__name">{{ $s->name }}</p>
                                <p class="table__email">{{ $s->email }}</p>
                            </td>
                            <td class="table__mono">{{ $s->college ?? '—' }}</td>
                            <td class="table__mono">{{ $peso($spent[$s->id] ?? 0) }}</td>
                            <td>
                                @if ($s->suspended_at)
                                    <span class="badge badge--warn">Suspended</span>
                                @else
                                    <span class="badge badge--ok">Active</span>
                                @endif
                            </td>
                            <td class="table__action">
                                <form method="POST" action="{{ route('admin.students.toggle', $s) }}">
                                    @csrf
                                    <button type="submit" class="link-btn">{{ $s->suspended_at ? 'Reinstate' : 'Suspend' }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="table__empty">
                                {{ $search !== '' ? 'No students match “' . $search . '”.' : 'No students have registered yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($students->previousPageUrl() || $students->nextPageUrl())
            <div class="pager">
                @if ($students->previousPageUrl())
                    <a href="{{ $students->previousPageUrl() }}" class="link-btn">← Previous</a>
                @else
                    <span></span>
                @endif

                @if ($students->nextPageUrl())
                    <a href="{{ $students->nextPageUrl() }}" class="link-btn">Next →</a>
                @endif
            </div>
        @endif
    </section>
@endsection
