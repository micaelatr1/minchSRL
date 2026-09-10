# AGENTS.md — Minch / SIC-MINCH

## Dual context

This repository contains **both** the source code of SIC-MINCH (a Laravel Livewire financial management app for Bolivian accounting) **and** the academic documentation produced across 10 project activities.

- **Codebase**: Laravel 13 + Livewire 4.1 app for retentions, bank books, cash, account statements.
- **Academic docs**: 8 LaTeX files in `docs/` covering the full software engineering lifecycle.

---

## Quick start

```bash
composer setup     # full first-time: install, .env, key:generate, migrate, npm i, npm build
composer dev       # serve + queue:listen --tries=1 + vite concurrently
composer test      # config:clear → lint:check → php artisan test
```

## Commands

| Command | What it runs |
|---|---|
| `composer setup` | install → .env → key:generate → migrate → npm i → build |
| `composer dev` | `php artisan serve` + `queue:listen --tries=1` + `vite` (concurrently) |
| `composer lint` | `pint --parallel` |
| `composer lint:check` | `pint --parallel --test` |
| `composer test` | `config:clear` → `@lint:check` → `php artisan test` |
| `php artisan test --coverage-html=public/coverage` | Code coverage report (requires pcov or xdebug) |
| `./vendor/bin/pest tests/Feature/ExampleTest.php` | Run a single test |

## Stack

**Laravel 13**, **Livewire 4.1**, **TallStackUI 3.1**, **Fortify** (auth), **Spatie Permission** (RBAC), **Tailwind CSS v4**, **mPDF** (PDF), **Maatwebsite/Excel** (exports).

- Auth middleware aliases in `bootstrap/app.php`: `role`, `permission`, `role_or_permission`.
- Routes: `routes/web.php` (main UI with `permission` middleware guards), `routes/settings.php` (required from web.php), `routes/api.php` (Sanctum — supplier search), `routes/console.php`.
- 28+ Livewire components in `app/Livewire/` organized by feature subdirectory; views in `resources/views/livewire/`.
- Key services: `AccountStatementService`, `CashBalanceService`, `MovementBalanceService`, `PersonSupplierService`.

## Config & env quirks

- `.env.example` defaults to **SQLite**; actual `.env` uses **MySQL** (`DB_CONNECTION=mysql`). Check which DB is active.
- `.env.example` has `SESSION_DRIVER=database`, actual `.env` may use `file`. Queue and cache use `database` driver.
- `.npmrc` has `ignore-scripts=true` — lifecycle hooks won't run on `npm install`.
- App locale defaults to **`es`** (`config/app.php:81`). Validation strings in `lang/es/`.
- `Carbon::setLocale('es')` is called in several models/controllers for Spanish date formatting.
- **PHP needs `pdo_sqlite` extension enabled** for tests to run (SQLite :memory: in phpunit.xml).

## Testing

- **Pest PHP**: SQLite `:memory:`, array cache, sync queue (see `phpunit.xml`).
- `RefreshDatabase` is enabled in `tests/Pest.php` for Feature tests.
- Tests that use Eloquent models **must** be in `tests/Feature/` — Unit tests don't bootstrap Laravel.
- Auth tests (Registration, Profile, Security) have pre-existing failures unrelated to business logic.
- Factory files for all business models are in `database/factories/`. They use `HasFactory` trait added to all models.
- **Coverage**: Run `php artisan test --coverage-html=public/coverage` (requires pcov or xdebug).
- Livewire tests that need permissions: use `$this->seed(\Database\Seeders\PermissionSeeder::class)` + `$user->givePermissionTo(Permission::all())`.

### Test structure

| Directory | Contents |
|---|---|
| `tests/Feature/Models/` | Unit tests for all 12 Eloquent models (accounts, boxes, movements, transactions, retentions, taxes, etc.) |
| `tests/Feature/Services/` | Tests for 4 services (AccountStatement, CashBalance, MovementBalance, PersonSupplier) |
| `tests/Feature/Http/` | Livewire feature tests (dashboard, taxes, accounts, suppliers, customers, retentions, account statements) |

## Database

Migrations in `database/migrations/`. Seed order (`DatabaseSeeder`):
1. `TaxeSeeder` — RC-IVA (13%, S), IUE (5%, G), IT (3%, A)
2. `PermissionSeeder` — 50+ permissions, syncs to Admin role (does NOT create the role)
3. Creates Admin role + 4 users + 8 bank accounts
4. `DemoDataSeeder` + `MoreDataSeeder` — demo data

```bash
php artisan db:seed
```

## Key domain notes

| Concept | Details |
|---|---|
| **Movement** types | `D` = Débito, `C` = Crédito, `B` = Balance carry |
| **Transaction** `payment_type` | `T` = Transferencia, `CH` = Cheque |
| **Transaction** numbering | Per type per **fiscal year (Oct–Sep)**, `lockForUpdate()` in `creating` boot event |
| **Box** numbering | Per type per **calendar month**, `lockForUpdate()` in `creating` boot event |
| **NumberHelper** | `require_once app_path('Helpers/NumberHelper.php')` in `AppServiceProvider::boot()`. Converts numbers to Spanish literal. |
| **Person** | Shared parent for `Supplier`, `Customer`, and `Employee` (one-to-one). `Movement` has `person_id`. |
| **Retention** `Taxe` type | `S` = Servicios, `G` = Bienes, `A` = Todos |
| **Account** | `HasManyThrough` to `Movement` via `Transaction` |
| **Account statement** route validation | Uses `whereIn('type', AccountStatementService::TYPES)` — accepts `'supplier'` or `'customer'` |
| **Retention calculation** | Service: RC-IVA 13% + IT 3%. Goods: IUE 5% + IT 3%. Formula: `total = amount / (1 - sum(rates)/100)`. |
| **RRHH bank account** | `Departament::where('area', 'Recursos Humanos')->first()?->account` — NOT from auth user's department |

## Schema quirks (mismatches between models and migrations)

These relationships exist in models but are **not backed by database columns**:
- `Transaction::supplier()` — `suppliers` table has no FK in `transactions`
- `Supplier::transactions()` — querying `transactions.supplier_id` which doesn't exist
- `Supplier::cooperative()` — `suppliers` table has no `cooperative_id`
- `Taxe::retention()` — `taxes` table has no `retention_id`
- `Transaction` model accessors (`calculate_label`, `date_label`) reference `amount`/`date` columns not present in the `transactions` table

## Miscellaneous

- `Date::use(CarbonImmutable::class)` and password defaults (12+ chars in production) configured in `AppServiceProvider::boot()`.
- TallStackUi heavily customized in `AppServiceProvider::boot()` — table, modal, button, inputs, selects, date picker, sidebar.
- No `.github/` CI workflows present.
- Spatie Permission `guard_name` defaults to `web`. Admin role is created in `DatabaseSeeder`, not `PermissionSeeder`.

---

### Academic documentation (docs/)

All files use `\documentclass[stu]{apa7}` (APA 7th ed. LaTeX). See `docs/RESUMEN-ACTIVIDADES.md` for a 192-line summary.

## Session context (Jul 2026)

### Check overlays (PDF receipts)

`resources/views/PDF/receipt.blade.php` — check overlay templates per bank by `account->initials`:

| Bank | Initials | Approach |
|---|---|---|
| BNB | `BNB.SA` | Block divs with `margin-left` + `margin-top` (negative for same-line fields) |
| BCP | `BCP.SA` | Block divs with negative `margin-top` for same-Y fields |
| BFO | `BFO.SA` | `<table>` with exact cm column widths + block divs below |
| BGA | `BGA` | `<table>` with exact cm column widths + block divs below |
| BUN | `BUN.SA` | `<table>` with exact cm column widths + block divs below |
| Fallback | `@else` | Generic layout with `<span>` pixel margins |

**mPDF positioning rules** (discovered through testing):
- `position:absolute` inside `position:relative` — does NOT work reliably
- `display:inline-block` with fixed widths — works but has whitespace gaps
- Negative `margin-top` on block divs — works for stacking same-Y fields, but each subsequent field needs a larger negative margin (cumulative)
- `<table>` with exact cm column widths — reliable for same-line fields
- `border="1"` attribute may not render; use inline `border:1px solid #000` + `border-collapse:collapse`

Check dimensions: 16.7cm × 5.2cm, on Letter page with no margins.

### PDF files

| File | Description |
|---|---|
| `receipt.blade.php` | Check overlay (cheque) per bank |
| `liquidation.blade.php` | Mineral liquidation document (header table + data table + signatures) |
| `contract.blade.php` | Contract document |
| `retentionForm.blade.php` | Retention receipt form |

### Liquidations feature

New feature (under development). Files:
- `app/Models/Liquidation.php` — model
- `app/Livewire/Liquidations/LiquidationForm.php` — form component
- `app/Livewire/Liquidations/LiquidationComponent.php` — list component
- `resources/views/PDF/liquidation.blade.php` — PDF generation with 8-column data table
- `database/migrations/2026_07_14_000001_create_liquidations_table.php` — migration

### Contracts feature

Files:
- `app/Models/Contract.php` — model
- `app/Livewire/Contracts/` — Livewire components
- `resources/views/PDF/contract.blade.php` — PDF
- `database/migrations/2026_04_03_193043_create_contracts_table.php` — migration

Sidebar menu in `resources/views/layouts/app.blade.php` (permission: `Ver contratos`).

## Session summary (Jul 2026)

### Purchase form — Alpine items

`resources/views/livewire/inventory/purchase-form-component.blade.php`: Items managed entirely in Alpine (`x-data` + `template x-for`); `addItem`/`removeItem` no longer make Livewire requests. Items sync to Livewire only on form submit via `syncItems()` → `$wire.set('items', ...)`. Uses `x-model` on `<x-select.styled>` (supported via `x-modelable="model"`) and `<x-input>`. Validation errors displayed via `$wire.$errors`.

- `app/Livewire/Inventory/PurchaseFormComponent.php`: `addItem()`/`removeItem()` methods removed (replaced by Alpine).
- Tests: 28/28 passing (ProductTest 7, ProductHttpTest 13, PurchaseHttpTest 4, PurchaseServiceTest 4).

### Agents

| File | Purpose |
|---|---|
| `.opencode/agents/sic-minch-docs.json` | Agent specialized in the 10 academic activities. |
| `.opencode/skills/sic-minch-liquidations.json` | Skill for liquidations module (Zn/Pb formulas, PDF, Alpine). |
| `.opencode/skills/sic-minch-rrhh.json` | Skill for RRHH subsystem (employees, payroll, vacations, bank integration). |

## Session context (Jul 2026)

### RRHH subsystem (new)

Files in `app/Livewire/Rrhh/`, `resources/views/livewire/rrhh/`, and `app/Models/` (Employee, Payroll, PayrollDetail, Vacation).

**Key points:**
- Employee uses Person for personal data (like Supplier/Customer)
- 4 migrations: employees, payrolls, payroll_details (with movement_id), vacations
- Payroll uses `Departament::where('area', 'Recursos Humanos')->first()?->account` for bank (NOT auth user's department)
- `Payroll::pay()` creates Movement (type 'C') + Transaction per employee detail
- Routes at `/rrhh/empleados`, `/rrhh/planillas`, `/rrhh/vacaciones`
- Sidebar group "RRHH" under separator
- Demo data: 4 employees + 1 draft payroll
