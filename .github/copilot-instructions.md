# GitHub Copilot Instructions for Snaphubb

## 🏗️ Project Overview
A Laravel 11-based modular admin panel built with `nwidart/laravel-modules`. Core logic lives in `app/`, and feature modules reside under `Modules/` (e.g., `Modules/Genres`, `Modules/Page`). Frontend assets are managed via Laravel Mix and Vue 3.

## 🚀 Getting Started
1. Copy `.env.example` → `.env` and configure database, MeiliSearch, AWS, social logins.
2. Start containers: `docker-compose up -d` (MySQL, Redis, MeiliSearch).
3. Install PHP deps: `composer install`.
4. Install JS deps: `npm install`.
5. Run migrations & seeders: `php artisan migrate --seed`.
6. Compile assets: `npm run dev` (or `npm run production` for prod).

## 📦 Modular Structure
- Each module has its own `module.json`, `Providers/`, `Http/Controllers`, `Routes/web.php` & `api.php`.
- Routes and services auto-registered via `Modules/*/Providers/*ServiceProvider.php`.
- Scaffold a new module: `php artisan module:make Blog`.
- PSR-4 autoload configured in `composer.json` under `"Modules\\": "Modules/"`.

## 🎨 Frontend Stack
- Entry: `resources/js/app.js` imports Vue 3, Pinia store, and vue-router.
- Asset pipeline: `webpack.mix.js` with Vue loader, SCSS, and image handling.
- Common tasks: `npm run watch` (hot reload), `npm run production` (minify).

## 🔧 Common Artisan & NPM Tasks
- Clear caches & optimize: `composer clear-all` (defined in `composer.json`).
- Generate IDE helpers: `php artisan ide-helper:generate && php artisan ide-helper:meta`.
- Run backend tests: `php artisan test` or `phpunit`.
- Run Dusk browser tests: `php artisan dusk`.

## 📐 Conventions & Patterns
- Global helpers in `app/helpers.php`.
- Domain services under `app/Services`.
- Eloquent models in `app/Models` and `Modules/*/Models`.
- Events & listeners in `app/Events` & `app/Listeners`.
- Notifications under `app/Notifications`.

## 🔗 Integrations & Configuration
- MeiliSearch: `config/scout.php` reads `MEILISEARCH_HOST`.
- AWS S3: `FILESYSTEM_DISK=s3` + `aws.*` env vars in `config/filesystems.php`.
- Spatie packages: permissions, medialibrary, activity log auto-discovered via `config/permission.php` & `config/medialibrary.php`.

## 💻 Debugging & Code Style
- PHP CS Fixer: `composer fix-cs` / `composer fix-cs-dry` (see `composer.json`).
- IDE helper and debugging: Telescope disabled by default in `extra.laravel.dont-discover`.

---

*Please review and let me know if any section needs more detail or clarification.*
