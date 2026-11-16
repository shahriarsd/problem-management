@extends('layouts.app')

@section('title', $problem->title)

@section('content')
    <div class="mx-auto" style="max-width: 780px;">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <a href="{{ route('problems.index') }}" class="btn btn-sm btn-light border">
                <i class="bi bi-arrow-left me-1"></i>Back to all problems
            </a>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('problems.edit', $problem) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil-square me-1"></i>Edit
                </a>

                <form action="{{ route('problems.destroy', $problem) }}" method="POST"
                      onsubmit="return confirm('Delete this problem and its solution? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash3 me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>

        <article class="card article-card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <header class="mb-4">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="section-label">Problem &amp; Solution</span>
                        <span class="badge rounded-pill text-bg-light border font-monospace fw-normal">
                            ID {{ $problem->id }}
                        </span>
                    </div>

                    <h1 class="article-title mb-3">{{ $problem->title }}</h1>

                    <div class="d-flex flex-wrap align-items-center gap-3 meta">
                        <span>
                            <i class="bi bi-calendar3 me-1"></i>
                            Published {{ $problem->created_at->format('M j, Y \a\t g:i A') }}
                        </span>

                        @if ($problem->updated_at->gt($problem->created_at))
                            <span>
                                <i class="bi bi-pencil me-1"></i>
                                Updated {{ $problem->updated_at->format('M j, Y \a\t g:i A') }}
                            </span>
                        @endif
                    </div>
                </header>

                @if ($problem->image_path)
                    <figure class="position-relative mb-4 mb-lg-5">
                        <img src="{{ asset('storage/' . $problem->image_path) }}"
                             alt="Problem image: {{ $problem->title }}"
                             class="article-image shadow-sm">

                        <button type="button"
                                class="btn btn-light btn-sm shadow-sm image-zoom-btn position-absolute bottom-0 end-0 m-3"
                                data-bs-toggle="modal"
                                data-bs-target="#problemImageModal"
                                aria-label="View the full size image">
                            <i class="bi bi-eye me-1"></i>View full size
                        </button>
                    </figure>
                @endif

                <section class="mb-4 mb-lg-5">
                    <h2 class="article-section-title">
                        <span class="section-accent"></span>Problem Description
                    </h2>
                    <div class="article-body">
                        {!! $problem->description !!}
                    </div>
                </section>

                <section>
                    <h2 class="article-section-title">
                        <span class="section-accent section-accent-success"></span>Solution
                    </h2>
                    <div class="article-body">
                        {!! $problem->solution !!}
                    </div>
                </section>
            </div>
        </article>

        @if ($problem->image_path)
            <div class="modal fade" id="problemImageModal" tabindex="-1"
                 aria-labelledby="problemImageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title fs-6" id="problemImageModalLabel">{{ $problem->title }}</h2>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body bg-dark p-2 p-lg-3">
                            <img src="{{ asset('storage/' . $problem->image_path) }}"
                                 alt="Full size image for {{ $problem->title }}"
                                 class="modal-full-image">
                        </div>
                        <div class="modal-footer justify-content-between">
                            <span class="text-muted small">Full size image</span>
                            <a href="{{ asset('storage/' . $problem->image_path) }}"
                               target="_blank" rel="noopener noreferrer"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Open original in new tab
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
