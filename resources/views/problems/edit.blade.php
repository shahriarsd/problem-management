@extends('layouts.app')

@section('title', 'Edit Problem')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold mb-1">Edit problem</h1>
            <p class="text-muted mb-0">Update the details or replace the image of this record.</p>
        </div>

        <a href="{{ route('problems.index') }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-1"></i>Back to list
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-lg-5">
            <form action="{{ route('problems.update', $problem) }}" method="POST" enctype="multipart/form-data" novalidate>
                @include('problems._form', ['problem' => $problem])
            </form>
        </div>
    </div>
@endsection
