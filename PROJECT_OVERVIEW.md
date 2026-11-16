# Project Overview — Problem & Solution Management System

A small Laravel application for recording problems, the image that shows them, and the solution that resolved each one. It is intentionally unauthenticated and minimal in design.

## Stack

- Laravel 13 / PHP 8.3+
- MySQL 8
- Blade templates, Bootstrap 5 + Bootstrap Icons (CDN), Inter (Google Fonts)
- Quill 2 rich text editor (CDN)
- No front-end build step is required for the UI

## Key files

| Concern | Location |
| --- | --- |
| Routes | `routes/web.php` |
| Controller | `app/Http/Controllers/ProblemController.php` |
| Validation | `app/Http/Requests/ProblemRequest.php`, `StoreProblemRequest.php`, `UpdateProblemRequest.php` |
| Model | `app/Models/Problem.php` |
| Migration | `database/migrations/2026_11_16_000001_create_problems_table.php` |
| HTML sanitizing | `app/Support/HtmlSanitizer.php` |
| Layout | `resources/views/layouts/app.blade.php` |
| Pages | `resources/views/problems/{index,show,create,edit,_form}.blade.php` |

## Data model

`problems` table: `id` (primary key, auto-increment, shown as Problem ID), `title`, `description` (sanitized HTML), `image_path` (nullable, public disk), `solution` (sanitized HTML), `created_at`, `updated_at`.

Timestamps are stored and displayed in the application timezone (`APP_TIMEZONE`, currently `Asia/Dhaka`, GMT+6).

## Behavior summary

- **List (`/problems`):** search by problem ID or title from the navbar; sortable by Problem ID, Created, Edited; 10 rows per page; fixed table layout that fits 320px screens; on phones dates appear under each title and a compact sort bar sits above the table.
- **Create/Edit:** required title, description, and solution; image required on create and optional on edit (JPG/JPEG/PNG/WebP, max 2 MB); rich text via Quill.
- **Detail (`/problems/{id}`):** blog-style article (title, meta, image, Problem Description, Solution) with a full-size image modal.
- **Delete:** confirmation prompt; the stored image file is removed with the record.

## Do

- Keep the controller thin: validation belongs in form requests, HTML cleaning in `HtmlSanitizer`.
- Sanitize every rich-text field on save and only render sanitized fields with `{!! !!}`.
- Validate uploads by type and size; store on the `public` disk under `problems/`; delete old files when replacing or deleting records.
- Use Eloquent with bound values; whitelist sortable columns and use `ctype_digit()` when matching IDs.
- Keep pagination at 10 per page and use `withQueryString()` so search and sort survive page changes.
- Render all times through Carbon using `config('app.timezone')`; never hardcode an offset.
- Design mobile-first: verify 320px, 375px, 768px, and 1280px widths and check `scrollWidth` for horizontal overflow.
- When hiding a column on small screens, provide the same information and actions elsewhere (as done for dates and sorting).
- Use `route()` and `asset()` for URLs, and keep image uploads behind `storage:link`.
- Keep the UI minimal: Bootstrap utilities, Inter, subtle accents, no decorative noise.
- Update this document and the README when behavior changes.

## Don't

- Don't add authentication, registration, or user accounts unless explicitly requested.
- Don't commit `.env`, credentials, `vendor/`, `node_modules/`, uploaded images, or the SQLite test file.
- Don't store or render unsanitized user HTML; don't add raw `<script>`, event handlers, or unsafe URL schemes.
- Don't rely on `table-layout: auto` for the list; long content must not widen the page.
- Don't hide critical data on mobile without an alternative control.
- Don't skip image type/size validation or accept arbitrary file extensions.
- Don't add a build step requirement for styling; Bootstrap and the editor load from CDNs.
- Don't hardcode the app URL, port, timezone offset, or per-page size in views.
- Don't delete or modify real records during testing/debugging without explicit approval; use clearly named temporary rows and remove only those.
- Don't remove the global Inter font, Bootstrap includes, or the responsive breakpoints without a replacement plan.

## Common commands

```bash
php artisan serve            # local server (http://127.0.0.1:8000)
php artisan migrate          # apply schema changes
php artisan migrate:fresh    # DESTRUCTIVE: drops all tables and data
php artisan storage:link     # expose uploaded images
php artisan test             # run the test suite
```

Database settings live in `.env`. Keep this repository free of secrets, and treat `migrate:fresh` as a data-loss operation.
