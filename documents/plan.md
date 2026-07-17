# Site Customization Implementation Plan

## Objective

Implement the admin-only site customization feature described in `task.md` without changing existing authentication, attendance behavior, or the appearance of installations that have no saved customization values.

## Current Project Baseline

- Laravel 12 / PHP 8.2 application using Blade views and Vite.
- Customization-compatible values are already stored in the `settings` table through `App\Models\Setting`.
- Existing attendance settings use the keys `logout_feedback_enabled`, `section_picker_enabled`, `attendance_sections`, and `scan_sms`; these must remain compatible.
- Administrator authorization is provided by the existing `can:isAdmin` gate.
- Public landing content is primarily in `resources/views/home/index.blade.php` and shared public layouts.
- Login branding is currently hardcoded in `resources/views/auth/login.blade.php`.
- Admin navigation is in `resources/views/layouts/partials/admin-sidebar.blade.php`.
- Existing colors and component styles are spread across Blade files, public branding CSS, Bootstrap classes, and inline styles. The values present before this feature is implemented are the factory defaults.

## Implementation Principles

1. Defaults live in source control; only administrator overrides live in the database.
2. Reads go through one service. Controllers and views must not add direct customization queries.
3. Settings use an allowlist with explicit type, group, default, validation, and UI metadata.
4. Invalid or missing persisted values resolve to safe defaults.
5. CSS customization is emitted only through predefined CSS custom properties; arbitrary CSS and HTML are never accepted.
6. Uploaded customization files live on the public storage disk under `site-customization/`; built-in assets remain untouched.
7. Each milestone must leave the application functional and testable.

## Proposed Architecture

### Settings definition

Add `config/site-customization.php` as the canonical registry. Each entry should define:

- `key`
- `group`
- `type`: `string`, `boolean`, `integer`, `color`, `url`, `json`, `image`, or constrained enum
- `default`
- Laravel validation rules
- optional bounds, allowed values, or approved URL policy
- UI label, help text, control type, and ordering where useful

Groups: `branding`, `landing`, `login`, `sidebar`, `buttons`, `tables`, and `advanced_theme`.

Before coding the registry, inventory the exact hardcoded values in the active layouts, login page, landing page, sidebar CSS, shared buttons, and representative tables. Record those exact values as defaults. Include all keys required by Task 1, including per-variant button states and complete table tokens.

### Service layer

Add `App\Services\SiteSettingsService` with methods equivalent to:

- `get(string $key)`
- `group(string $group): array`
- `all(): array`
- `set(string $key, mixed $value, ...)`
- `setMany(array $values, ...)`
- `reset(string $key, ...)`
- `resetGroup(string $group, ...)`
- `resetAll(...)`
- `publicUrl(string $key): string`
- `cssVariables(): array`

The service will validate keys against the registry, cast stored values, validate values again while reading, fall back safely, and cache resolved groups/versioned snapshots. Writes and resets will use database transactions and invalidate caches immediately. Existing attendance helpers on `Setting` remain operational; they may later delegate internally only after regression tests prove parity.

Register the service as a singleton in a service provider. Share only the small resolved branding/theme payload needed by common layouts, avoiding repeated database work.

### Persistence and revisions

Reuse the existing `settings` table for current values. Add a `setting_revisions` table with:

- actor/user ID (nullable for system operations)
- batch UUID
- action (`update`, `reset_section`, `reset_all`, `restore`, `remove_image`)
- setting key
- previous serialized value
- new serialized value
- safe request metadata such as IP and user agent, with conservative lengths
- timestamps

Use a model such as `SettingRevision`. Never log passwords, session data, request bodies, or uploaded binary data. A batch UUID groups one save operation and provides the restore unit.

### HTTP layer

Add `SiteCustomizationController` under the existing `auth` + `can:isAdmin` route group. Use named routes under `/admin/site-customization` for:

- `GET /` — editor and revision history
- `PUT /section/{group}` — save one group
- `PUT /` — save all groups
- `POST /images/{key}` — upload/replace an allowed image key
- `DELETE /images/{key}` — remove an uploaded override
- `DELETE /section/{group}` — reset a group
- `DELETE /` — reset all customization values
- `POST /revisions/{batch}/restore` — restore a revision batch
- `POST /preview` — render a non-persisted preview, if an iframe endpoint is used

Create dedicated form requests for section/all saves, image uploads, preview payloads, and restores. Every request must authorize with `isAdmin`, reject unknown keys/groups, normalize booleans, and apply registry rules. Standard Laravel web middleware supplies CSRF protection.

### Image management

Add a focused upload service or keep tightly scoped image logic in the settings service. The flow is:

1. Confirm the requested key is a registered image setting.
2. Validate upload error, MIME, allowed extension, size, and decoded image dimensions.
3. Store with a randomized filename on the `public` disk in `site-customization/`.
4. Commit the new setting and revision in a transaction.
5. Delete the previous file only after the replacement is safely stored and the database update succeeds.
6. On failure, remove the newly stored unreferenced file.
7. Delete files only when their normalized path is inside the customization directory; never delete defaults or other public files.

Initial formats: PNG, JPEG, and WebP. Define limits per image role in the settings registry. Use Laravel fake storage and generated fake images for tests.

### Presentation layer

Add a customization editor view and reusable field partials/components for text, textarea, toggle, image, numeric, select, and paired color/hex controls. Use progressive enhancement: saving and validation must work without JavaScript; live preview and unsaved-change warnings enhance it.

Create a shared theme-variable partial or service-generated style block. Escape all values and emit only registered properties, for example `--site-sidebar-bg`, `--site-button-primary-bg`, and `--site-table-header-bg`. Prefer shared semantic component classes over modifying unrelated Bootstrap behavior globally.

## Delivery Phases

## Phase 0 — Inventory and Contract

1. Inventory all current assets, visible strings, colors, dimensions, radii, shadows, and spacing values in the landing, login, sidebar, shared buttons, and target tables.
2. Identify every target table view: students, employees, accounts, attendance, feedback, files, and reports.
3. Create the complete settings matrix in `config/site-customization.php`.
4. Document key naming conventions and destination URL policy.
5. Add unit tests that assert unique keys, recognized types/groups, valid defaults, and validation metadata for every definition.

Exit criteria:

- Every requested setting has a documented exact default and validation contract.
- Registry tests reject duplicate, incomplete, and unknown definitions.

## Phase 1 — Core Settings (Tasks 1–4)

1. Implement typed value casting and safe validation helpers.
2. Implement `SiteSettingsService`, cache keys/tags or versioned cache invalidation, grouped retrieval, writes, and resets.
3. Add service registration and a safe mechanism for layouts/controllers to consume resolved settings.
4. Add the revision migration/model and record all changes in batch transactions.
5. Add admin controller, form requests, routes, policies/gate checks, and response/redirect behavior.
6. Implement secure image upload, replacement, removal, path resolution, and cleanup.
7. Preserve and regression-test the existing attendance settings API.

Verification:

- Empty `settings` table returns defaults.
- Malformed stored values return defaults.
- Unknown keys cannot be read as customization values or written.
- Staff receive 403 and guests are redirected/unauthorized according to current middleware behavior.
- Cache changes are visible immediately after save/reset.
- Upload negative tests cover MIME spoofing, extensions, size, dimensions, non-image data, and path safety.

## Phase 2 — Admin UI and Public Branding (Tasks 5–7)

1. Add “Site Customization” to the admin-only sidebar children and make it impossible to hide through menu settings.
2. Build tabs for Branding, Landing Page, Login Page, Sidebar, Buttons, Tables, and Advanced Theme.
3. Add Save Section, Save All, Reset Section, and Reset All flows with confirmation dialogs.
4. Add image previews, replace/remove/reset controls, paired color controls, validation summaries, per-field errors, and flash messages.
5. Retain submitted valid values after a validation failure.
6. Connect site name, favicon, landing logo/content/actions/sections/footer to resolved settings.
7. Validate landing destinations as relative internal paths or explicitly approved `http`/`https` URLs; reject dangerous schemes and protocol-relative values.
8. Connect login logo, text, labels, footer, backgrounds, card, and button tokens while preserving form names, routes, validation, password toggle, labels, focus order, and authentication behavior.
9. Conditionally omit optional landing sections so hidden/empty content produces no layout gaps.

Verification:

- Browser checks at desktop, tablet, and mobile widths.
- Feature tests assert immediate public rendering after save and exact fallback rendering after reset.
- Authentication regression tests continue to pass.
- Staff never see the navigation item and cannot open its route directly.

## Phase 3 — Shared Admin Theme (Tasks 8–10)

1. Define a stable CSS custom-property contract for sidebar, button, and table tokens.
2. Refactor the sidebar to use configurable full/compact logos, label text, colors, dividers, widths, active, and hover values.
3. Add allowlisted menu label/visibility settings only for approved items. Essential administration and Site Customization entries remain hardcoded visible to administrators.
4. Verify expanded, collapsed, and mobile sidebar behavior, including safe width bounds.
5. Create shared semantic button components/classes for primary, secondary, success, warning, danger, and neutral variants with normal, hover, focus, active, disabled, and loading states.
6. Migrate relevant buttons incrementally, view by view, removing inline color declarations only from migrated controls.
7. Create shared responsive table wrapper/table classes with header, body, text, border, stripe, hover, selected row, radius, and compact/comfortable spacing tokens.
8. Migrate student, employee, account, attendance, feedback, file, and report tables one screen at a time.
9. Preserve sorting, pagination, responsive wrappers, forms, JavaScript hooks, action columns, and print/export-specific output.

Verification:

- Add rendering tests for variable emission and semantic classes.
- Run focused feature tests after each screen migration.
- Manually verify destructive buttons, keyboard focus, disabled states, horizontal table scrolling, and selected/hover contrast.

## Phase 4 — Preview, Restore, QA, and Release (Tasks 11–14)

1. Build a scoped preview using an iframe or a strictly namespaced preview document so draft variables cannot affect the editor.
2. Support desktop/mobile preview widths and client-side updates for text, colors, visibility, spacing, and local image object URLs.
3. If server rendering is required, send validated preview data to a non-persisting endpoint and never call write/revision methods.
4. Show recent revision batches with actor, action, time, and changed keys.
5. Implement transactional batch restore. Restores create their own revision batch and handle image references safely.
6. Distinguish “restore previous version” from “factory defaults.” Require confirmation for both.
7. Complete the test matrix listed below.
8. Perform responsive visual QA and accessibility checks.
9. Document administrator usage and developer/deployment procedures.

Exit criteria:

- Preview changes are discarded on refresh/close unless saved.
- All published changes and restores are attributable and reversible where referenced files still exist.
- Full automated test suite passes.
- Deployment and rollback steps are documented.

## Test Matrix

### Definition and service tests

- Every registered default passes its rules and casts to its declared type.
- Unknown keys/groups throw or return a controlled validation error.
- Empty, null, corrupt JSON, invalid colors, and out-of-range numbers fall back.
- Single and grouped reads use cache; writes/resets invalidate it immediately.
- Existing attendance getters/setters retain their current default and serialization behavior.

### Authorization and request tests

- Guest, staff, and admin access for every endpoint.
- CSRF remains enabled for all mutations (covered by web middleware plus route assertions/integration tests as practical).
- Section requests cannot alter another group.
- Save-all rejects unknown or malformed fields atomically.
- Unsafe landing URLs and arbitrary HTML/CSS are rejected or escaped.

### Upload tests

- Accept valid PNG, JPEG, and WebP within constraints.
- Reject MIME/extension mismatches, SVG, scripts renamed as images, oversized files, and invalid dimensions.
- Replacement deletes the prior customization file only after success.
- Failed replacements preserve the prior value/file.
- Remove/reset restores the built-in asset and never deletes it.
- Restore behavior for image settings is explicitly covered.

### Rendering tests

- Defaults render with no customization rows.
- Saved landing/login/sidebar values render escaped and immediately.
- Optional sections disappear cleanly.
- Theme CSS contains only known variables and sanitized typed values.
- Button/table variants and responsive wrapper classes exist on every migrated screen.

### Revision tests

- Section and save-all operations create correctly grouped records.
- Actor, before/after value, timestamp, and safe metadata are recorded.
- Reset and restore operations are logged.
- Restore is transactional and clears cache.

## Accessibility and Safety Gates

- All form controls have programmatic labels, descriptions where needed, and associated error messages.
- Color controls remain keyboard operable and have synchronized text inputs.
- Focus indicators are visible for links, controls, buttons, tabs, sidebar items, and dialogs.
- Normal and hover color combinations are checked for WCAG contrast; warn or reject combinations that would make essential controls unreadable.
- Uploaded image previews have meaningful or intentionally empty alt text.
- Reset/restore actions require explicit confirmation and cannot be triggered by GET requests.
- Content is rendered with escaped Blade output; no setting is rendered through `{!! !!}`.

## Documentation Deliverables

Update the main project documentation or add `docs/site-customization.md` with:

- supported settings and factory defaults
- supported image formats, file-size limits, and dimension rules
- administrator save, preview, reset, and restore instructions
- `php artisan storage:link` requirement
- migration, deployment, config/cache clearing, and queue considerations
- file permissions for `storage/app/public/site-customization`
- backup and rollback instructions, including revision/image limitations
- troubleshooting for missing images, stale caches, and rejected colors/URLs

## Deployment Checklist

1. Back up the database and public customization storage.
2. Deploy application files and install locked dependencies.
3. Run database migrations.
4. Ensure the public storage link exists.
5. Ensure the web process can write only to the required storage/cache locations.
6. Clear and rebuild Laravel configuration/view/application caches in the project’s established deployment order.
7. Run automated tests or the production smoke-test subset.
8. Smoke test public landing, login, admin editor, save/reset, image display, sidebar states, and representative buttons/tables.
9. Roll back application code and migrations only with a verified database/storage backup; do not delete customization uploads during routine rollback.

## Recommended Work Order by Concrete Area

1. `config/site-customization.php` and definition tests.
2. `App\Services\SiteSettingsService`, service provider registration, and service tests.
3. Revision migration/model and transactional write logging.
4. Form requests, controller, routes, and authorization tests.
5. Image management and storage tests.
6. Admin editor and sidebar navigation link.
7. Shared theme-variable output.
8. Landing and login integration.
9. Sidebar token migration.
10. Shared button variants and screen-by-screen migration.
11. Shared table theme and screen-by-screen migration.
12. Live preview and revision history/restore UI.
13. Full regression, responsive/accessibility QA, and documentation.

## Definition of Complete

The work is complete only when an administrator can safely edit, preview, publish, reset, and restore all supported customization values; guests and staff cannot access mutation endpoints; invalid settings and uploads fail safely; an empty settings table reproduces the current design; existing attendance settings remain compatible; all targeted responsive views use the shared theme contracts; automated tests pass; and deployment/operation documentation is available.
