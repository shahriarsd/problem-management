# Project Overview — Problem & Solution Management System

A small, unauthenticated Laravel application for recording problems, the image that shows them, and the solution that resolved each one. This document is the working brief: read it before changing code, and update it when behavior changes.

- Repository root: this folder
- Git branch: `main`, no remote configured
- Status: feature complete (CRUD, search, sort, pagination, rich text, responsive UI)
- Snapshot date: 2026-11-16

## Stack and environment

| Item | Value |
| --- | --- |
| Framework | Laravel 13.42, PHP 8.3+ (this machine runs PHP 8.5.4) |
| Database | MySQL 8.4 on `127.0.0.1:3306`, database `psms` |
| UI | Blade, Bootstrap 5.3.3 + Bootstrap Icons 1.11.3 (CDN), Inter from Google Fonts |
| Rich text | Quill 2.0.3 (CDN) |
| Build step | None required for the UI; Vite/Tailwind scaffolding exists but is unused |
| Local server | `php artisan serve --host=127.0.0.1 --port=8000` |
| Timezone | `APP_TIMEZONE=Asia/Dhaka` (GMT+6), configured in `.env` |
| Secrets | `.env` holds the DB password; it is git-ignored. Never print, copy, or commit it |

Session, cache, and queue drivers are `database`; migrations create those tables. `php artisan storage:link` has been run and must exist for uploaded images to render.

### Environment gotchas

- The system MySQL service (port 3306) is the only usable server here. AppArmor blocks `mysqld` from using a data directory inside this project, so do not try to start a project-local MySQL instance; use the running service.
- All UI styling, fonts, icons, and the editor load from CDNs. Without internet the app still functions but loses styling and the rich text toolbar.
- Changing `.env` values requires `php artisan config:clear` only if config was cached; the dev server re-reads env per request.

## Project structure

```
app/
  Http/Controllers/ProblemController.php   # resource CRUD, search/sort, image lifecycle
  Http/Requests/ProblemRequest.php         # shared rules + sanitizing + messages
  Http/Requests/StoreProblemRequest.php    # image required
  Http/Requests/UpdateProblemRequest.php   # image optional
  Models/Problem.php                       # fillable: title, description, image_path, solution
  Providers/AppServiceProvider.php         # Paginator::useBootstrapFive()
  Support/HtmlSanitizer.php                # allowlist HTML cleaner + blank detector
database/migrations/2026_11_16_000001_create_problems_table.php
resources/views/layouts/app.blade.php      # navbar, search form, fonts, all custom CSS
resources/views/problems/index.blade.php   # list, mobile sort bar, mobile dates, pagination
resources/views/problems/show.blade.php    # article page + full-size image modal
resources/views/problems/create.blade.php
resources/views/problems/edit.blade.php
resources/views/problems/_form.blade.php   # shared form + Quill initialization
routes/web.php
```

Supporting docs: `README.md` (setup/features) and this file. `AGENTS.md`/`CLAUDE.md` are unchanged Laravel scaffold stubs.

## Routes

| Method | URI | Name | Action |
| --- | --- | --- | --- |
| GET | `/` | — | Redirects to `/problems` |
| GET | `/problems` | `problems.index` | List with search, sort, pagination |
| GET | `/problems/create` | `problems.create` | Create form |
| POST | `/problems` | `problems.store` | Store |
| GET | `/problems/{problem}` | `problems.show` | Article detail |
| GET | `/problems/{problem}/edit` | `problems.edit` | Edit form |
| PUT/PATCH | `/problems/{problem}` | `problems.update` | Update |
| DELETE | `/problems/{problem}` | `problems.destroy` | Delete |

No authentication middleware is applied anywhere.

## Data model and current data

`problems`: `id` (bigint unsigned, primary key, auto-increment — surfaced as "Problem ID"), `title` (string), `description` (text, sanitized HTML), `image_path` (string, nullable, public disk), `solution` (text, sanitized HTML), `created_at`, `updated_at`.

Current database state: three records — `1 First`, `2 second`, `3 third` — each with an uploaded image; `AUTO_INCREMENT` is 4. These are the user's records: do not delete, edit, or renumber them during testing. Use clearly named temporary rows and remove only those, or verify with HTTP GETs that do not write.

Timestamps are stored and rendered in Dhaka local time. They were migrated from UTC to local on 2026-11-16 by adding six hours to the two older rows; do not change `APP_TIMEZONE` without converting existing rows, or stored values will be misinterpreted.

## Behavior details

### List page (`/problems`)

- Default listing: `created_at` descending, 10 per page, `withQueryString()` so search/sort survive pagination.
- Sortable columns: `sort=id|created|edited` mapped to `id|created_at|updated_at`; `direction=asc|desc`; anything else falls back to created desc. Header links toggle direction; active header shows `bi-sort-up`/`bi-sort-down` and `aria-sort`.
- Search (`?search=`): title `LIKE %term%` (with `%`, `_`, `\` escaped) plus exact ID match when the term is all digits. A numeric search can therefore return the matching ID row *and* titles containing that number; this is intentional.
- Table columns: Problem ID, Problem (thumbnail, title, truncated description), Created, Edited, Actions. Created/Edited are hidden below 768px; the `#` counter no longer exists.
- Below 768px, each row shows "Created"/"Edited" (format `M j, y`, full local timestamp in `title`), and a compact sort bar above the table provides ID/Created/Edited plus a direction toggle.
- The table uses `table-layout: fixed` with widths in `.col-id`, `.col-date`, `.col-actions`; this is what keeps it inside 320px screens. Never switch back to auto layout without re-testing overflow.
- Empty state differs for no records vs no search matches; both offer a way out.
- Page header keeps the "Add problem" button; the navbar intentionally has no add button.

### Create / edit

- Fields in order: Problem title, Problem description, Problem image, Solution description.
- Validation (`ProblemRequest`): title required ≤255; description and solution required ≤20000 after sanitizing; image required on create, optional on update; image must be an image, mime jpg/jpeg/png/webp, ≤2 MB.
- Placeholders: "Enter problem name", "Enter problem description", "Enter solution to the problem".
- Description and solution use Quill. The real `<textarea>` is hidden by JS after init and re-synced on submit (`''` when the editor is visually empty), so server-side `required` still works. If JS/CDN fails, the plain textarea remains usable.
- On update, a new image replaces and deletes the old file; leaving the file input empty keeps the current image. The edit form shows the current image thumbnail.
- Image files are stored via `$file->store('problems', 'public')` with random names; path is saved in `image_path`.

### Detail page (`/problems/{id}`)

- Blog-style article card (max 780px) with a 4px gradient top accent, eyebrow ("Problem & Solution"), ID badge, title, and meta with icons: Published and (when edited) Updated, both absolute local times (`M j, Y \a\t g:i A`).
- Photo at article width with rounded corners and shadow; the eye button opens `#problemImageModal` (Bootstrap modal) showing the full image plus an "Open original in new tab" link.
- Sections: Problem Description, then Solution, each with a labelled heading and colored accent square (blue/green). Content is rendered raw (`{!! !!}`) because it is sanitized on save.
- Toolbar: "Back to all problems" button (top left), Edit and Delete (right); Delete asks for confirmation.
- Article image max-height is 460px, reduced to 260px below 576px.

### Delete

- Deletes the record and its stored image file, then redirects with a success flash. Requires the confirmation dialog.

## Rich text and security

- `HtmlSanitizer::clean()` strips `<script>`/`<style>` blocks, applies a tag allowlist (`p, br, strong, b, em, i, u, s, ul, ol, li, a, h1–h4, blockquote, code, pre, hr`), drops all attributes except safe `href` (http/https/mailto/relative/anchor) and Quill's `data-list`, and forces `target="_blank" rel="noopener noreferrer"` on links.
- `ProblemRequest::prepareForValidation()` sanitizes both fields and converts visually empty HTML to `''` so `required` rejects it.
- Rendering sanitized HTML with `{!! !!}` is intentional. Never render unsanitized input this way, and never bypass the sanitizer when adding new rich-text fields.
- CSRF protection applies to all mutations through Blade forms; do not disable it. Delete uses `@method('DELETE')`.

## UI system and responsive rules

- Layout: light gray page (`#f6f7f9`), white navbar, Bootstrap defaults, Inter everywhere (including Quill toolbar/editor via `--bs-body-font-family`).
- Navbar: brand, search form (full width second row below 576px, inline above), "All problems" button (icon-only below 576px).
- Custom classes live in `layouts/app.blade.php`: `.brand-mark`, `.brand-text`, `.thumb`, `.thumb-lg`, `.article-card`, `.article-title`, `.article-image`, `.article-section-title`, `.section-accent`, `.section-accent-success`, `.article-body`, `.meta`, `.problem-snippet`, `.problem-dates`, `.col-id`, `.col-date`, `.col-actions`, `.action-group`, `.nav-search`, `.rich-editor`, `.modal-full-image`.
- Breakpoints used: 576px (compact/expanded controls), 768px (table date columns and inline dates; mobile sort bar), 1280px (desktop sanity check).
- Verify at 320, 375, 768, and 1280 CSS px. The acceptance bar is `document.documentElement.scrollWidth <= innerWidth` and `table-responsive.scrollWidth <= clientWidth`. Keep tap targets intact and provide mobile alternatives instead of hiding critical data.

## Verification workflow

There are no custom automated tests. `tests/Feature/ExampleTest.php` is the stale Laravel scaffold test (expects `/` to return 200, but it redirects to `/problems`); do not report it as a regression, and replace it with real tests before relying on `php artisan test`.

Manual checks used so far:

```bash
php artisan serve --host=127.0.0.1 --port=8000
curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8000/problems
curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8000/problems/1
```

- CRUD can be exercised with `curl` using a cookie jar and `_token` from the create page; use `--form-string` for HTML values because `-F key=<p>...` is interpreted as a file upload.
- Check `storage/logs/laravel.log` after changes; it should stay empty of errors.
- For UI work, check the rendered browser at the widths above. On 2026-11-16 the pages had zero horizontal overflow at 320px, the table was 281px wide inside a 281px wrapper, and the mobile sort bar fit in one row.

## Do

- Keep controllers thin; validation and sanitizing stay in `ProblemRequest` and `HtmlSanitizer`.
- Sanitize new rich-text input before saving and render only sanitized fields with `{!! !!}`.
- Whitelist sortable columns and use bound/Eloquent queries; escape `LIKE` wildcards.
- Keep pagination at 10 per page with `withQueryString()`.
- Display times through Carbon using the app timezone; never hardcode `+06:00`.
- Validate uploads (image, mime, size) and delete orphaned files when replacing/deleting records.
- Use `route()`/`asset()` rather than hardcoded paths or ports.
- Provide a mobile equivalent whenever a table column is hidden.
- Re-check 320px overflow and the fixed column widths after table or content changes.
- Keep the design minimal and consistent with the existing Bootstrap + Inter system.
- Update this document and the README when behavior or setup changes.

## Don't

- Don't add authentication, users, roles, or admin panels unless explicitly requested.
- Don't run `php artisan migrate:fresh` (or truncate tables) on this database; it destroys the user's records. Use targeted, reversible checks and temporary rows instead.
- Don't delete, edit, renumber, or "clean up" existing records or their images during testing.
- Don't commit `.env`, credentials, `vendor/`, `node_modules/`, uploaded images, or SQLite files.
- Don't store or output unsanitized HTML, add raw `<script>`/inline event handlers, or weaken the sanitizer allowlist.
- Don't switch the table back to auto layout, remove the fixed column widths, or hide dates/sorting on mobile without replacing them.
- Don't hardcode URLs, ports, timezones, page sizes, or file paths in views.
- Don't introduce a JS framework or a required CSS build for the UI; Bootstrap, icons, Inter, and Quill load from CDNs.
- Don't remove the Quill hidden-textarea sync or the server-side empty check; both are needed for validation.
- Don't treat the stale scaffold test as coverage, and don't claim tests pass when they were not run.

## Common commands

```bash
php artisan serve            # http://127.0.0.1:8000
php artisan migrate          # apply schema changes
php artisan storage:link     # publish uploads (already done)
php artisan config:clear     # after changing env/config
php artisan test             # scaffold tests only (see note above)
php artisan route:list --path=problems
```

## Known limitations / possible next work

- No authentication or per-user ownership by design.
- No image optimization or thumbnails; the original upload is used in list and article views.
- Numeric search matches both the ID and titles containing the number.
- Rich text supports a limited tag set (no images, tables, or embedded media inside content).
- No dedicated automated feature tests yet; adding them for CRUD, validation, sanitizing, search, and sorting would be the highest-value reliability improvement.
