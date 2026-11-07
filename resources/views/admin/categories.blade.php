@extends('layouts.admin')

@section('title', 'Categories · Estudyantipid Admin')

@section('content')
    <header>
        <h1 class="page-title">Categories</h1>
        <p class="page-sub">Default budget categories offered to every student.</p>
    </header>

    <section class="card cat-card">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="cat-add" novalidate>
            @csrf
            <input type="text" name="name" value="{{ old('name') }}" maxlength="50" required
                   placeholder="New category" aria-label="New category"
                   class="quick-add__input">
            <button type="submit" class="quick-add__btn">Add</button>
        </form>

        @if ($errors->any())
            <div class="quick-add__errors">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="cat-list">
            @forelse ($categories as $category)
                <div class="cat-item">
                    <span class="cat-item__name">{{ $category->name }}</span>

                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                          onsubmit="return confirm('Remove “{{ addslashes($category->name) }}”? Students will no longer see it.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="link-btn link-btn--danger">Remove</button>
                    </form>
                </div>
            @empty
                <p class="expense-list__empty">No categories yet. Add the first one above.</p>
            @endforelse
        </div>
    </section>
@endsection
