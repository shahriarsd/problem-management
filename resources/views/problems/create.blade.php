@extends('layouts.app')

@section('title', 'Add Problem')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold mb-1">Add a problem</h1>
            <p class="text-muted mb-0">Record the problem, its image, and the solution that resolved it.</p>
        </div>

        <a href="{{ route('problems.index') }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-1"></i>Back to list
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-lg-5">
            <form action="{{ route('problems.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @include('problems._form')
            </form>
        </div>
    </div>
@endsection
