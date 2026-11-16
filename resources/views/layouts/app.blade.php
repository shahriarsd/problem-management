<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Problems') &middot; {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @stack('styles')

    <style>
        :root {
            --bs-body-font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background-color: #f6f7f9;
            color: #212529;
            font-family: var(--bs-body-font-family);
        }

        .brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background-color: var(--bs-primary);
            color: #fff;
            font-size: 1rem;
        }

        .card {
            --bs-card-border-radius: .75rem;
        }

        .thumb {
            width: 52px;
            height: 52px;
            object-fit: cover;
            border-radius: .5rem;
            border: 1px solid var(--bs-border-color);
            background-color: #eef1f4;
        }

        .thumb-lg {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: .5rem;
            border: 1px solid var(--bs-border-color);
            background-color: #eef1f4;
        }

        .thumb-empty {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 1.15rem;
        }

        .section-label {
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #6c757d;
        }

        .meta {
            font-size: .85rem;
            color: #6c757d;
        }

        .navbar-brand {
            min-width: 0;
        }

        .brand-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .problem-snippet {
            max-width: clamp(100px, 36vw, 340px);
        }

        .problem-dates {
            font-size: .78rem;
            line-height: 1.5;
        }

        .action-group {
            gap: .25rem;
        }

        .col-id {
            width: 6.5rem;
        }

        .col-date {
            width: 6.5rem;
        }

        .col-actions {
            width: 7rem;
        }

        .problems-table {
            table-layout: fixed;
        }

        .nav-search {
            order: 3;
            width: 100%;
        }

        .nav-search .form-control {
            min-width: 0;
        }

        .nav-link-all {
            order: 2;
        }

        @media (min-width: 576px) {
            .nav-search {
                order: 2;
                width: auto;
                flex: 1 1 240px;
                max-width: 360px;
                margin-left: auto;
            }

            .nav-link-all {
                order: 3;
                margin-left: .25rem;
            }
        }

        .table thead th {
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #6c757d;
            white-space: nowrap;
        }

        /* Article (problem detail) styles */
        .article-title {
            font-size: clamp(1.75rem, 3.4vw, 2.6rem);
            font-weight: 600;
            line-height: 1.2;
            letter-spacing: -.02em;
        }

        .article-card {
            position: relative;
            overflow: hidden;
        }

        .article-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--bs-primary), #74b9ff);
        }

        .article-image {
            width: 100%;
            max-height: 460px;
            object-fit: cover;
            border-radius: .75rem;
            border: 1px solid var(--bs-border-color);
        }

        .article-section-title {
            display: flex;
            align-items: center;
            gap: .55rem;
            font-size: .78rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #495057;
            margin-bottom: 1rem;
            padding-bottom: .65rem;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .section-accent {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 3px;
            background-color: var(--bs-primary);
        }

        .section-accent-success {
            background-color: #198754;
        }

        .meta i {
            color: #adb5bd;
        }

        .image-zoom-btn {
            backdrop-filter: blur(6px);
        }

        .article-body {
            font-size: 1.03rem;
            line-height: 1.75;
            color: #2b3035;
        }

        .article-body > :last-child {
            margin-bottom: 0;
        }

        .article-body h1,
        .article-body h2,
        .article-body h3,
        .article-body h4 {
            font-size: 1.15rem;
            font-weight: 600;
            margin: 1.5rem 0 .75rem;
        }

        .article-body ul,
        .article-body ol {
            padding-left: 1.35rem;
        }

        .article-body li {
            margin-bottom: .35rem;
        }

        .article-body li[data-list="bullet"] {
            list-style-type: disc;
        }

        .article-body blockquote {
            border-left: 3px solid var(--bs-border-color);
            padding-left: 1rem;
            color: #6c757d;
            margin: 1rem 0;
        }

        .article-body pre {
            background: #f1f3f5;
            padding: .85rem 1rem;
            border-radius: .5rem;
            font-size: .9rem;
        }

        .article-body a {
            color: var(--bs-primary);
        }

        .modal-full-image {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            max-height: 78vh;
            width: auto;
        }

        /* Rich text editor */
        .ql-toolbar.ql-snow {
            border-color: var(--bs-border-color);
            border-radius: .375rem .375rem 0 0;
            background-color: #fff;
            font-family: var(--bs-body-font-family);
        }

        .rich-editor.ql-container.ql-snow {
            border-color: var(--bs-border-color);
            border-radius: 0 0 .375rem .375rem;
            font-family: var(--bs-body-font-family);
            font-size: 1rem;
        }

        .rich-editor .ql-editor {
            min-height: 220px;
            line-height: 1.7;
            font-family: var(--bs-body-font-family);
        }

        @media (max-width: 575.98px) {
            .thumb {
                width: 42px;
                height: 42px;
            }

            .article-image {
                max-height: 260px;
            }

            .table > :not(caption) > * > * {
                padding-left: .5rem;
                padding-right: .5rem;
            }

            .table .btn-sm {
                padding-left: .45rem;
                padding-right: .45rem;
            }

            .ql-toolbar.ql-snow {
                padding: 6px;
            }

            .ql-toolbar.ql-snow .ql-formats {
                margin-right: 6px;
            }

            .rich-editor .ql-editor {
                min-height: 170px;
            }

            .modal-footer {
                flex-wrap: wrap;
                gap: .5rem;
            }
        }

        @media (max-width: 359.98px) {
            .thumb {
                width: 34px;
                height: 34px;
            }

            .table .btn-sm {
                padding-left: .3rem;
                padding-right: .3rem;
            }

            .action-group {
                gap: .125rem;
            }

            .col-id {
                width: 3.25rem;
            }

            .col-actions {
                width: 6rem;
            }

            .problem-snippet {
                max-width: 66px;
            }
        }

        .rich-editor .ql-editor.ql-blank::before {
            color: #adb5bd;
            font-style: normal;
        }
    </style>
</head>
<body>
    <nav class="navbar bg-white border-bottom">
        <div class="container flex-wrap gap-2" style="max-width: 1100px;">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold text-body me-2" href="{{ route('problems.index') }}">
                <span class="brand-mark flex-shrink-0"><i class="bi bi-kanban"></i></span>
                <span class="fs-6 brand-text">Problem &amp; Solution Manager</span>
            </a>

            <form class="nav-search" role="search" method="GET" action="{{ route('problems.index') }}">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                    <input type="search" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Search ID or title"
                           aria-label="Search problems by problem ID or title">
                    <button type="submit" class="btn btn-outline-secondary">Search</button>
                </div>
            </form>

            <a href="{{ route('problems.index') }}" class="nav-link-all btn btn-sm btn-light border flex-shrink-0" aria-label="All problems">
                <i class="bi bi-list-ul"></i>
                <span class="d-none d-sm-inline ms-1">All problems</span>
            </a>
        </div>
    </nav>

    <main class="container py-4 py-lg-5" style="max-width: 1100px;">
        @yield('content')
    </main>

    <footer class="container pb-4 text-center text-muted small" style="max-width: 1100px;">
        Problem &amp; Solution Management System
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
