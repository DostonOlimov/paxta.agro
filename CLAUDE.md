# CLAUDE.md

This file guides Claude Code (claude.ai/code) when working in this repository.

## What this is

A Laravel 8 (PHP 8.1) web app for certifying cotton ("paxta") crops in Uzbekistan. It covers the whole path from an application to laboratory tests and then to certificates and conclusions. The UI and most identifiers are in Uzbek (Latin), e.g. *dalolatnoma* = act/report, *chigit* = cotton seed, *tola* = fiber, *namlik* = humidity, *zavod* = factory, *sertifikat/sifat* = certificate/quality. The README is the unedited GitLab template, so ignore it.

## Environment & commands

- Runs locally under **OSPanel** (Windows). `.osp/project.ini` pins PHP-8.1 and serves `public/`. Database: MySQL `paxta` (see `.env`).
- `composer install`, `npm install`
- `npm run dev` / `npm run watch` / `npm run prod`: Laravel Mix builds `resources/js/app.js` (Vue 3 + vue-router) and `resources/css/app.css` (Tailwind) into `public/`. Most pages use prebuilt Bootstrap/jQuery assets in `public/assets`, `public/vendors` and `public/build`, which Mix does not build.
- `php artisan test` or `vendor/bin/phpunit --filter Name`. There are only example tests, and SQLite in-memory is commented out in `phpunit.xml`, so tests hit the configured MySQL DB.
- Custom artisan commands: `sertificates:update-values`, `app:process-file-command`.
- `QUEUE_CONNECTION=sync` locally, so dispatched jobs (`app/Jobs`: HVI/humidity file processing, `ExportReportJob`, `SertificatePdfSave`) run inline.

## Database schema caveat

`database/migrations` has only about 14 migrations. **Most tables (applications, crop_data, dalolatnoma, test_programs, tbl_* legacy tables, etc.) are not created by migrations.** They exist in the live DB. Don't assume `migrate:fresh` gives a working schema. When adding tables or columns, write a new migration, and check the model's `$table`/`$fillable` for the real column names (table names are often singular or legacy, e.g. `crops_name`).

## Architecture

### Session-scoped "year" and "crop" context (important)
The user picks a year and a crop type in the navbar (`POST /change-year`, `/change-crop` → `LanguageController`), and both are stored in the session. Helpers in [app/Http/helpers.php](app/Http/helpers.php) read them:
- `getCurrentYear()`: `session('year', 2026)`
- `getApplicationType()`: `session('crop', 3)`, one of `CropsName::CROP_TYPE_1..5`. `LoginController@authenticated` seeds the session value on every login, so change it there too.
- `isSifatSertificate()` (types 3, 4) and `isProductConclusion()` (type 5)

`Application::booted()` adds **global scopes** from these values: it excludes `STATUS_DELETED`, filters `app_type = crop` and the crop year, and for `BRANCH_STATE` users limits results to their region. A "missing" record is usually filtered out by this scope, so use `withoutGlobalScope(...)` when you need to bypass it. Controllers and views pick different templates or flows per crop type (e.g. `DalolatnomaController@add` → `dalolatnoma/chigit/*` for type 2, `dalolatnoma/conclusion/*` for type 5).

[app/Services/MenuService.php](app/Services/MenuService.php) (a singleton) maps crop types to the features that are enabled:
- `laboratory`: type 1
- `hvi_enabled`: types 1, 3, 4
- `sertificate_protocol`: types 3, 4
- `lclass_enabled`: type 4
- `product_conclusion`: type 5
- `quality_certificates_only`: type 2 (chigit)

Use these groups instead of hard-coding type checks.

### Workflow / domain flow
`Application` (status: NEW/REJECTED/ACCEPTED/FINISHED/DELETED; `PROGRESS_*` steps initial → decision → example → laboratory → conclusion → finished) → `Decision` → `TestPrograms` → `Dalolatnoma` (sampling act, bales/`GinBalles`) → measurements (`AktAmount`, `Humidity`/`HumidityResult`, `MeasurementMistake`, HVI data/`ClampData`, `InXaus`) → `LaboratoryResult`/`FinalResult` → outputs: `LaboratoryProtocol`, `SertificateProtocol` (`Sertificate`), `ProductConclusion` (`FinalConclusionResult`), `SifatSertificates`. Many later-stage routes are keyed by `{dalolatnoma}`.

`ChigitQualityEvaluator` (`app/HelperClasses`) holds the seed-quality rules. PDFs are generated with barryvdh/laravel-dompdf (layout `resources/views/layouts/pdf.blade.php`) and include QR codes from simple-qrcode.

### Code layout conventions
- **Web routes** ([routes/web.php](routes/web.php)) mostly use string `'\App\Http\Controllers\X@method'` syntax in prefix groups with the pattern `/add`, `/list`, `/store`, `/list/edit/{id}`, `/list/edit/update/{id}` (POST) and `/list/delete/{id}` (GET). The method is spelled `destory` in many controllers; keep existing names when wiring routes. Views live in `resources/views/<prefix>/`.
- **List/search pages** go through `SearchService::search()` with a filter class from `app/Filters/V1` (`getFilters()` / `apply()`). Follow this pattern for new searchable lists.
- **Lookups** (regions, crop names, years, statuses, settings) come from cached global helpers in `helpers.php` (1h cache). Clear the cache after changing reference data.
- **Region IDs** are hard-coded constants in `helpers.php` (`TASHKENT_ID`, `NAVOI_ID`, …).
- **Roles**: integer constants on `App\Models\User` (`LABORATORY_DIRECTOR=90`, `ROLE_STATE_CHIGIT_BOSHLIQ=100`, …). Other scoping fields are `branch_id` (main/state/area), `crop_branch` (tola/chigit/both), `state_id` and `zavod_id`. Policies exist for User, Application and OrganizationCompanies. `getAccessStatusUser()` always returns `'yes'`.
- **Repositories** (`app/Repositories` + `Contracts`) and `app/Services/ModelServices` are used for some models, not all.
- **Legacy models**: `app/Models/DefaultModels/tbl_*` (settings, access rights, activities, cities, states).
- **Activity logging**: spatie/laravel-activitylog plus the `LogsActivity` trait; the `tbl_activities` model is also used directly.
- **JSON responses**: the `response()->successJson()` / `errorJson()` macros (`ResponseMixin`).
- **API** ([routes/api.php](routes/api.php)): `/api/v1/*` apiResources behind `auth:sanctum` (`Api\V1` controllers, `Resources\V1`), plus unauthenticated Vue report endpoints (`get-state-report`, `get-factory-report`). Session-authenticated inline-edit endpoints live under `/api/tests/...` in `web.php`.
- **Vue**: only the report SPA under `/vue/*` (`resources/js/components/StateReport.vue`, `FactoryReport.vue`). Everything else is Blade + jQuery.
- **Excel**: maatwebsite/excel exports in `app/Exports`. Legacy readers live in `app/Http/Controllers/excelImporter`, and hisamu/php-xbase reads DBF files.
- **Telegram**: irazasyed/telegram-bot-sdk, `app/Services/Telegram`, config in `config/telegram.php` and `services.telegram`.

### Localization
The default locale is `uz`; `lang/` has `uz`, `ru`, `en` and `krill` (Cyrillic Uzbek). `SetLocale` middleware reads `session('language')`. Views use `trans('app.Key')` with keys that are often Uzbek phrases (e.g. `app.Ro'yxat`), so add new keys to `resources/lang/*/app.php`. The timezone is `Asia/Tashkent`. Use `formatUzbekDate()` / `formatUzbekDateInLatin()` for dates shown to users.
