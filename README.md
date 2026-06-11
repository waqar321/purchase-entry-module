# Purchase Entry Module

Laravel purchase entry application built with **Livewire** and **Alpine.js**, including role-based access control and legacy data migration.

## Features

- Dynamic purchase form with add/remove rows
- Alpine.js + Livewire entangle for reactive grand total
- Instant validation with debounce and duplicate item+brand prevention
- Role-based permissions (Admin / User)  - user may have multiple roles, roles may have multiple permissions, a middleware is created and can be used anywhere using @can('permission_name')
- Idempotent legacy data migration command
- Corrected legacy MySQLi example in `legacy/get_user_fixed.php`

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL (or SQLite for local testing)

## Setup

1. Clone the repository and install dependencies:

composer install
npm install

2. Configure environment:

cp .env.example .env
php artisan key:generate

3. Update `.env` database settings. Example for MySQL:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=purchase_entry_module
DB_USERNAME=root
DB_PASSWORD=

4. Run migrations and seed demo data:

php artisan migrate --seed

5. Build frontend assets:

npm run build

6. Start the application:

php artisan serve

Visit `http://127.0.0.1:8000`.

## Demo Accounts

| Role  | Email              | Password  |
|-------|--------------------|-----------|
| Admin | admin@example.com  | password  |
| User  | user@example.com   | password  |

## Role & Permission System

Uses a many-to-many RBAC schema:

- `roles` — role definitions (Admin, User, etc.)
- `permissions` — permission titles used in `@can()` checks
- `role_user` — users can have multiple roles
- `permission_role_pivot` — roles can have multiple permissions

`AuthGates` middleware dynamically registers Laravel Gates from the database on each authenticated request.

### Permissions

| Permission        | Admin | User |
|-------------------|-------|------|
| `purchase_view`   | Yes   | Yes  |
| `purchase_create` | Yes   | No   |
| `purchase_edit`   | Yes   | No   |
| `purchase_delete` | Yes   | No   |
| `legacy_migrate`  | Yes   | No   |

### Usage in Blade / Livewire

@can('purchase_create')
    <a href="{{ route('purchases.create') }}">New Purchase</a>
@endcan


$this->authorize('purchase_edit');

## Legacy Data Migration

The migration script maps `item_name` and `brand_name` to normalized `items` and `brands` tables, creates purchases and purchase items, and tracks migrated rows to prevent duplicates.

Run via Artisan (idempotent):

php artisan purchases:migrate-legacy

Admins can also trigger the same migration from the purchases list page.

### Sample legacy payload

[
    [
        'item_name' => 'Sugar',
        'brand_name' => 'ABC',
        'qty' => 10,
        'price' => 100,
    ],
]

## Legacy MySQLi Fix (Part 6)

- Original buggy code: `legacy/get_user_original.php`
- Corrected secure version: `legacy/get_user_fixed.php`

Fixes include prepared statements, input validation, connection/query error handling, and output escaping.

## Assumptions

- Each legacy row represents one purchase with a single line item.
- Item and brand names are unique by exact string match (trimmed).
- Purchase totals are recalculated from line items (`qty * price`).
- Authentication uses Laravel's session-based login (no registration flow).
- Authorization uses database-driven roles/permissions with dynamic Gates (`AuthGates` middleware).
- `role_user` references the `users` table (adapted from `ecom_admin_user` in the reference schema).

## Development

npm run dev
php artisan serve

## Tests

php artisan test
