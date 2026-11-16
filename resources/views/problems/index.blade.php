@extends('layouts.app')

@section('title', 'Problems')

@section('content')
    @php
        $sortLink = function (string $column) use ($sort, $direction, $search) {
            return route('problems.index', array_filter([
                'search' => $search,
                'sort' => $column,
                'direction' => $sort === $column && $direction === 'asc' ? 'desc' : 'asc',
            ]));
        };

        $sortIcon = function (string $column) use ($sort, $direction) {
            if ($sort !== $column) {
                return 'bi-arrow-down-up opacity-50';
            }

            return $direction === 'asc' ? 'bi-sort-up' : 'bi-sort-down';
        };
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold mb-1">Problems &amp; Solutions</h1>
            <p class="text-muted mb-0">Every problem recorded along with the solution that resolved it.</p>
        </div>

        <a href="{{ route('problems.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Add problem
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($search !== '')
        <div class="alert alert-light border d-flex flex-wrap justify-content-between align-items-center gap-2 py-2" role="status">
            <div class="small">
                Showing results for <strong>{{ $search }}</strong>
                <span class="text-muted">&middot; {{ $problems->total() }} {{ Str::plural('result', $problems->total()) }}</span>
            </div>
            <a href="{{ route('problems.index') }}" class="btn btn-sm btn-light border">Clear search</a>
        </div>
    @endif

    <div class="d-md-none d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
        <span class="text-muted small">Sort by</span>

        <div class="d-flex align-items-center gap-1">
            <div class="btn-group btn-group-sm" role="group" aria-label="Sort problems by">
                @foreach (['id' => 'ID', 'created' => 'Created', 'edited' => 'Edited'] as $field => $label)
                    <a href="{{ route('problems.index', array_filter(['search' => $search, 'sort' => $field, 'direction' => $sort === $field ? $direction : 'asc'])) }}"
                       class="btn {{ $sort === $field ? 'btn-secondary' : 'btn-outline-secondary' }}"
                       @if ($sort === $field) aria-current="true" @endif>
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <a href="{{ route('problems.index', array_filter(['search' => $search, 'sort' => $sort, 'direction' => $direction === 'asc' ? 'desc' : 'asc'])) }}"
               class="btn btn-sm btn-outline-secondary"
               aria-label="{{ $direction === 'asc' ? 'Sorted ascending. Switch to descending.' : 'Sorted descending. Switch to ascending.' }}"
               title="{{ $direction === 'asc' ? 'Ascending' : 'Descending' }}">
                <i class="bi bi-sort-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 problems-table">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="ps-2 col-id"
                            aria-sort="{{ $sort === 'id' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <a href="{{ $sortLink('id') }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                <span class="d-none d-sm-inline">Problem ID</span>
                                <span class="d-sm-none">ID</span>
                                <i class="bi {{ $sortIcon('id') }} small"></i>
                            </a>
                        </th>
                        <th scope="col">Problem</th>
                        <th scope="col" class="d-none d-md-table-cell col-date"
                            aria-sort="{{ $sort === 'created' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <a href="{{ $sortLink('created') }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                Created <i class="bi {{ $sortIcon('created') }} small"></i>
                            </a>
                        </th>
                        <th scope="col" class="d-none d-md-table-cell col-date"
                            aria-sort="{{ $sort === 'edited' ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <a href="{{ $sortLink('edited') }}" class="text-reset text-decoration-none d-inline-flex align-items-center gap-1">
                                Edited <i class="bi {{ $sortIcon('edited') }} small"></i>
                            </a>
                        </th>
                        <th scope="col" class="text-end pe-2 col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($problems as $problem)
                        <tr>
                            <td class="ps-2">
                                <span class="font-monospace small text-muted">{{ $problem->id }}</span>
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if ($problem->image_path)
                                        <a href="{{ route('problems.show', $problem) }}" class="flex-shrink-0">
                                            <img src="{{ asset('storage/' . $problem->image_path) }}"
                                                 alt="Image for {{ $problem->title }}"
                                                 class="thumb">
                                        </a>
                                    @else
                                        <span class="thumb thumb-empty flex-shrink-0" aria-hidden="true">
                                            <i class="bi bi-image"></i>
                                        </span>
                                    @endif

                                    <div class="min-w-0">
                                        <a href="{{ route('problems.show', $problem) }}"
                                           class="fw-semibold text-body text-decoration-none">
                                            {{ $problem->title }}
                                        </a>
                                        <div class="text-muted small text-truncate problem-snippet">
                                            {{ Str::limit(strip_tags($problem->description), 90) }}
                                        </div>

                                        <div class="d-md-none text-muted problem-dates mt-1">
                                            <div title="{{ $problem->created_at->format('M j, Y g:i A') }}">
                                                <span class="d-block">Created</span>
                                                {{ $problem->created_at->format('M j, y') }}
                                            </div>
                                            <div title="{{ $problem->updated_at->format('M j, Y g:i A') }}">
                                                <span class="d-block">Edited</span>
                                                {{ $problem->updated_at->format('M j, y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="d-none d-md-table-cell text-muted small text-nowrap"
                                title="{{ $problem->created_at->format('M j, Y g:i A') }}">
                                {{ $problem->created_at->format('M j, Y') }}
                            </td>

                            <td class="d-none d-md-table-cell text-muted small text-nowrap"
                                title="{{ $problem->updated_at->format('M j, Y g:i A') }}">
                                {{ $problem->updated_at->format('M j, Y') }}
                            </td>

                            <td class="text-end pe-2">
                                <div class="d-inline-flex align-items-center action-group">
                                    <a href="{{ route('problems.show', $problem) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="View" aria-label="View {{ $problem->title }}">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('problems.edit', $problem) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Edit" aria-label="Edit {{ $problem->title }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form action="{{ route('problems.destroy', $problem) }}" method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this problem and its solution? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Delete" aria-label="Delete {{ $problem->title }}">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted mb-3">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    @if ($search !== '')
                                        No problems match &ldquo;{{ $search }}&rdquo;.
                                    @else
                                        No problems have been recorded yet.
                                    @endif
                                </div>

                                @if ($search !== '')
                                    <a href="{{ route('problems.index') }}" class="btn btn-sm btn-light border">
                                        Clear search
                                    </a>
                                @else
                                    <a href="{{ route('problems.create') }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-plus-lg me-1"></i>Add the first problem
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
        <div class="text-muted small">
            @if ($problems->total() > 0)
                Showing {{ $problems->firstItem() }}&ndash;{{ $problems->lastItem() }} of {{ $problems->total() }}
            @else
                No records
            @endif
        </div>

        @if ($problems->hasPages())
            <div>{{ $problems->links() }}</div>
        @endif
    </div>
@endsection
