---
name: database-audit
description: >-
  Audit, analyze, and refactor Laravel database schemas, migrations, models, foreign keys,
  indexes, data types, and table/column naming conventions (including Indonesian localization).
  Use this skill whenever the user asks to analyze the database, review migrations, check schema
  professionalism, optimize database performance/indexes, or rename tables and columns.
---

# Database Audit & Professional Schema Design Skill

Use this skill to perform a comprehensive, professional-grade audit of a Laravel application's database layer (`database/migrations/`, `app/Models/`, `database/seeders/`, and Eloquent queries in controllers).

## Golden Rule: Investigate First, Ask Before Refactoring

**NEVER rewrite or overhaul working code without first diagnosing the exact root cause and impacts, presenting the findings to the user, and getting explicit approval.**
- Before touching migrations, models, controllers, or views, map out every file and line affected by the proposed change.
- Distinguish clearly between changes that are isolated to the database/model layer vs. changes that require touching Controllers and Blade views.
- Present options with clear trade-offs and wait for the user's decision so existing working code is never broken or unnecessarily rewritten.

## Audit Checklist & Procedure

When asked to analyze or improve the database, execute the following steps systematically:

### 1. Migration Hygiene & History Audit
- **Squash / Clean Up "Churn" Migrations**: AI-generated or iterative projects often contain "add-then-drop" migrations (e.g., creating tables like `ingredients` or columns like `hpp` and dropping them a few migrations later) or patch migrations fixing missing foreign keys.
  - *Action*: Identify dead/cancelled-out migrations. Recommend consolidating into clean, baseline migrations (1 table = 1 clean `create_*_table` migration) if the project is in pre-production or local development.
- **Driver Compatibility**: Check for raw `DB::statement("ALTER TABLE ...")` calls that break SQLite in-memory testing without driver guards.
- **Honest `down()` Methods**: Verify every migration has a valid `down()` method that drops constraints/tables in the reverse dependency order.

### 2. Naming Convention & Localization Consistency
- **Single-Language Standard**: Never mix English and Indonesian table names (e.g., `categories`, `items`, `orders` alongside `pengeluaran`, `buku_kas_harian`).
- **Plural vs. Singular**:
  - In English Laravel convention: snake_case plural (`categories`, `order_details`).
  - In Indonesian convention: snake_case singular/collective without English `-s` suffixes (`pengguna`, `kategori`, `menu` / `produk`, `pesanan`, `detail_pesanan`, `pengeluaran`, `buku_kas_harian`).
- **Eloquent Mapping**: When using Indonesian table names while keeping or renaming Eloquent Models:
  - Explicitly set `protected $table = 'nama_tabel';` on every Model.
  - Explicitly pass table and foreign key names in migrations (`->constrained('nama_tabel')`) and validation rules (`unique:nama_tabel,kolom`, `exists:nama_tabel,id`).

### 3. Data Types & Monetary Precision
- **Consistent Currency Types**: Check all monetary columns (`price`, `total_amount`, `amount`, `starting_cash`, etc.).
  - Do not mix `integer` (4-byte signed max ~2.14 billion) in some tables with `decimal(12, 2)` in others.
  - For IDR (Rupiah without cents), use `unsignedBigInteger` (or `bigInteger` for columns that can be negative like `discrepancy` / `selisih_kas`) OR `decimal(15, 2)` consistently across all tables.
- **Unsigned Quantities & Stock**: Columns that should never be negative (`price`, `quantity`, `subtotal`) should use unsigned types or application-level guards.

### 4. Referential Integrity (Foreign Keys & `onDelete` Behavior)
- **Audit Danger of `cascadeOnDelete()` on Financial Records**:
  - Never use `cascadeOnDelete()` from `orders` to `users` or `items` to `categories` if historical transactions depend on them. Even with `SoftDeletes`, a `forceDelete()` on a user or category would silently wipe out historical financial `orders` and `order_details`!
  - Prefer `restrictOnDelete()` for core transactional references (`orders.user_id`, `order_details.item_id`, `items.category_id`).
- **Unconstrained Audit Columns**:
  - Check columns like `created_by` and `updated_by`. If a controller calls `$model->update(['updated_by' => ...])`, verify that:
    1. The column actually exists in the migration (check for silent mass-assignment drops!).
    2. It is listed in `$fillable`.
    3. It has `->nullable()->constrained('users')->nullOnDelete()`.

### 5. Indexing Strategy for Real Queries
Inspect Controller queries (`where`, `whereBetween`, `whereDate`, `groupBy`, `orderBy`) and match them against schema indexes:
- **Avoid `WHERE DATE(created_at) = ?` on unindexed or timestamp columns** when possible, or ensure composite indexes exist for high-frequency filters:
  - `orders`: Queries frequently filter by `status`, `payment_method`, and `created_at`. Add composite index `['status', 'created_at']` and `['status', 'payment_method', 'created_at']`.
  - `pengeluaran`: Queries filter by `date` and group by `category`. Add index on `date` and composite `['date', 'category']`.
  - `items`: POS queries filter by `category_id` and `is_available`, and reports filter by `stock < 5`. Add composite index `['category_id', 'is_available']` and index on `stock`.

### 6. Model Casts & Attributes
- Ensure every Model defines `protected function casts(): array` for:
  - Booleans (`is_available` => `'boolean'`)
  - Dates (`date` => `'date'`)
  - Integers/Decimals (`price`, `stock`, `total_amount`) so JSON serialization and strict comparisons work reliably.

