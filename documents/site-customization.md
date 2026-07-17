# Site Customization Guide

## Administrator usage

Developer administrators sign in to the separate `/developer/dashboard` area and can open **Developer → Site Customization**. Library administrators, staff, students, and unauthenticated users cannot access any `/developer` route. The account must use the distinct `admindeveloper` role.

Set `ADMINDEVELOPER_EMAIL` and `ADMINDEVELOPER_PASSWORD`, then create or update the separate account with:

```bash
php artisan db:seed --class=AdminDeveloperUserSeeder
```

The editor contains Branding, Landing Page, Login Page, Sidebar, Buttons, Tables, and Advanced Theme tabs. Use **Save Section** to publish one tab or **Save All** to publish all text and theme fields. Images are uploaded independently so a failed upload cannot discard other valid edits.

Color controls provide both a color picker and editable six-digit hexadecimal value. Reset and restore operations always ask for confirmation. **Reset Section** removes overrides from one tab; **Reset All** removes only site-customization overrides and does not remove attendance settings. Recent Changes can restore the values from before a published batch.

The preview responds to draft landing text, primary color, and local image selections without saving them. Refreshing or leaving the page discards preview-only changes.

## Images

- Formats: PNG, JPEG/JPG, and WebP.
- Files are validated by extension, detected MIME type, decoded image status, size, and dimensions.
- Limits are role-specific and are defined in `config/site-customization.php`.
- Uploaded files are stored on the `public` disk under `site-customization/` with randomized names.
- Built-in defaults in `public/images` and the root favicon are never deleted.

Create the public storage link once per deployment:

```bash
php artisan storage:link
```

The web/PHP process needs write access to `storage/app/public/site-customization`, `storage/framework`, and `bootstrap/cache`.

## Developer contract

`config/site-customization.php` is the only supported-key registry. Each entry declares its group, type, default, validation rules, and editor control. Do not accept arbitrary HTML, CSS, setting names, or URL schemes.

Read and write settings through `App\Services\SiteSettingsService`. Missing or invalid database values resolve to source-controlled defaults. Customization resets target only registered keys, preserving `App\Models\Setting` attendance keys.

Public image URLs must be resolved with `SiteSettingsService::publicUrl()`. Upload mutations must use `SiteCustomizationImageService` so path checks and replacement cleanup remain enforced.

## Deployment

1. Back up the database and `storage/app/public/site-customization`.
2. Deploy the locked application dependencies.
3. Run `php artisan migrate --force`.
4. Run `php artisan storage:link` if the link does not exist.
5. Clear stale caches with `php artisan optimize:clear`.
6. Rebuild production caches using the deployment's normal `config:cache`, `route:cache`, and `view:cache` process.
7. Smoke test landing, login, customization save/reset, image display, both sidebar states, and a representative themed table.

For rollback, restore the matching database and customization-storage backups before rolling application code back. Routine rollback must not delete the customization upload directory. If an image is missing, confirm the storage link, file permissions, and referenced path before resetting that image to its built-in default.
