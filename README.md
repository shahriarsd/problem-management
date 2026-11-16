# Problem & Solution Management System

A Laravel application for recording problems, the image that shows them, and the solution that resolved each one.

See [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md) for the architecture, key files, conventions, and do/don't guidelines.

## Features

- Create, view, edit, and delete problem records with confirmation before deletion.
- Secure image uploads (JPG, JPEG, PNG, WebP; max 2 MB) with old files removed on replace/delete.
- Rich text descriptions and solutions (Quill editor), sanitized server-side before saving.
- Search by problem ID or title from the navbar.
- Sortable table by problem ID, created date, and edited date, with 10 rows per page.
- Blog-style problem detail page with a full-size image viewer.
- Responsive Bootstrap 5 interface; authentication is intentionally not included.

## Tech stack

- Laravel 13 / PHP 8.3+
- MySQL 8
- Bootstrap 5 and Bootstrap Icons (CDN)
- Quill 2 rich text editor (CDN)

## Getting started

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure the database in `.env` (`DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), then:

```bash
php artisan migrate
php artisan storage:link
php artisan serve
```

The app is served at <http://127.0.0.1:8000>. Uploaded images live in `storage/app/public/problems`.

## Timezone

Times are rendered in the application timezone, configured with `APP_TIMEZONE` in `.env` (defaults to `Asia/Dhaka`, GMT+6 in this project).
