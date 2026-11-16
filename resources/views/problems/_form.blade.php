@php
    $editing = isset($problem) && $problem->exists;
@endphp

@csrf
@if ($editing)
    @method('PUT')
@endif

<div class="mb-3">
    <label for="title" class="form-label">Problem title <span class="text-danger">*</span></label>
    <input type="text"
           id="title"
           name="title"
           value="{{ old('title', $problem->title ?? '') }}"
           class="form-control @error('title') is-invalid @enderror"
           placeholder="Enter problem name"
           maxlength="255"
           required>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Problem description <span class="text-danger">*</span></label>
    <textarea id="description"
              name="description"
              rows="8"
              class="form-control rich-input @error('description') is-invalid @enderror"
              placeholder="Enter problem description">{{ old('description', $problem->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="image" class="form-label">
        Problem image
        @unless ($editing)
            <span class="text-danger">*</span>
        @endunless
    </label>

    @if ($editing && $problem->image_path)
        <div class="d-flex align-items-center gap-3 mb-2">
            <img src="{{ asset('storage/' . $problem->image_path) }}"
                 alt="Current image for {{ $problem->title }}"
                 class="thumb-lg">
            <span class="text-muted small">Current image. Upload a new file to replace it.</span>
        </div>
    @endif

    <input type="file"
           id="image"
           name="image"
           accept=".jpg,.jpeg,.png,.webp"
           class="form-control @error('image') is-invalid @enderror"
           @unless ($editing) required @endunless>
    <div class="form-text">JPG, JPEG, PNG or WebP. Maximum file size 2 MB.</div>
    @error('image')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label for="solution" class="form-label">Solution description <span class="text-danger">*</span></label>
    <textarea id="solution"
              name="solution"
              rows="8"
              class="form-control rich-input @error('solution') is-invalid @enderror"
              placeholder="Enter solution to the problem">{{ old('solution', $problem->solution ?? '') }}</textarea>
    @error('solution')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check2 me-1"></i>{{ $editing ? 'Save changes' : 'Create record' }}
    </button>
    <a href="{{ route('problems.index') }}" class="btn btn-light border">Cancel</a>
</div>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('textarea.rich-input').forEach(function (textarea) {
                var editorElement = document.createElement('div');
                editorElement.className = 'rich-editor';
                textarea.insertAdjacentElement('afterend', editorElement);

                var quill = new Quill(editorElement, {
                    theme: 'snow',
                    placeholder: textarea.getAttribute('placeholder') || '',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{ header: [2, 3, false] }],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            ['blockquote', 'link'],
                            ['clean']
                        ]
                    }
                });

                if (textarea.value.trim() !== '') {
                    quill.clipboard.dangerouslyPasteHTML(textarea.value);
                }

                textarea.classList.add('d-none');

                var container = editorElement.querySelector('.ql-container');
                if (container && textarea.classList.contains('is-invalid')) {
                    container.classList.add('border', 'border-danger');
                }

                var form = textarea.closest('form');
                if (form) {
                    form.addEventListener('submit', function () {
                        textarea.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
                    });
                }
            });
        });
    </script>
@endpush
