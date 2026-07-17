# Developer Admin Site Customization Tasks

## Goal

Build an admin-only site customization area that lets an administrator change landing-page images and text, login and landing logos, sidebar appearance, button colors, and table colors without editing source code.

## Scope Rules

- Only authenticated users with the `admin` role can change customization settings.
- Existing pages must continue working when no custom settings have been saved.
- Every setting must have a safe default matching the current design.
- Use defined content fields and theme tokens; do not allow arbitrary HTML or CSS.
- Uploaded images must be validated and stored outside the source-controlled image folders.
- Changes must work on desktop and mobile layouts.

## Task 1 — Define Settings and Defaults

Create the complete list of supported branding, landing, login, sidebar, button, and table setting keys.

### Work

- Add constants or a settings definition/config file for all supported keys.
- Define the type, default value, validation rules, and group for every key.
- Include settings for:
  - Site name and favicon
  - Landing, login, sidebar, and compact sidebar logos
  - Landing hero image, headings, descriptions, buttons, and footer text
  - Login title, subtitle, labels, background, card, and button appearance
  - Sidebar background, text, active, hover, divider, and width values
  - Primary, secondary, success, warning, danger, and neutral buttons
  - Table header, body, border, stripe, hover, selected-row, radius, and spacing values
- Make current hardcoded values the initial defaults.

### Acceptance Criteria

- All supported settings have documented defaults and validation rules.
- Unknown setting keys cannot be saved.
- The application renders correctly with an empty `settings` table.

## Task 2 — Build the Site Settings Service

Add a central service responsible for reading, writing, caching, and resolving customization settings.

### Work

- Create `SiteSettingsService`.
- Support single-key and grouped setting retrieval.
- Cast values into the correct string, boolean, integer, color, URL, or JSON type.
- Cache resolved settings.
- Clear the relevant cache after updates or resets.
- Resolve uploaded image paths into public URLs.
- Keep compatibility with existing attendance-related settings in `App\Models\Setting`.

### Acceptance Criteria

- Controllers and views can retrieve settings without querying the database directly.
- Missing or invalid values fall back to defaults.
- Updating a setting invalidates the cached value immediately.

## Task 3 — Add Admin Routes, Authorization, and Controller

Create the protected backend endpoints for viewing and updating site customization.

### Work

- Add `SiteCustomizationController`.
- Add admin-only routes for:
  - Viewing customization settings
  - Saving one section
  - Saving all sections
  - Uploading or replacing images
  - Removing an uploaded image
  - Resetting one section
  - Resetting all settings
- Protect every route with authentication and the existing admin authorization gate/middleware.
- Use dedicated form requests for validation.

### Acceptance Criteria

- Administrators can access all customization endpoints.
- Staff and unauthenticated users receive the correct unauthorized response.
- CSRF protection applies to every state-changing request.
- Invalid or unknown fields are rejected server-side.

## Task 4 — Implement Secure Image Management

Create a reusable upload flow for branding and page images.

### Work

- Store files under `storage/app/public/site-customization/`.
- Support PNG, JPG/JPEG, and WebP initially.
- Validate MIME type, extension, file size, and image dimensions.
- Generate randomized filenames.
- Add image preview, replace, remove, and reset behavior.
- Delete an old customization file only after its replacement is saved successfully.
- Never delete built-in fallback assets.
- Ensure the public storage link and deployment instructions are documented.

### Acceptance Criteria

- Valid images upload and display correctly.
- Invalid, oversized, or disguised files are rejected.
- Replacing an image does not leave unnecessary orphan files.
- Resetting an image restores the built-in default.

## Task 5 — Build the Site Customization Admin Screen

Create the main admin interface at `Administration → Site Customization`.

### Work

- Add a Site Customization link to the admin-only sidebar section.
- Build tabs for:
  1. Branding
  2. Landing Page
  3. Login Page
  4. Sidebar
  5. Buttons
  6. Tables
  7. Advanced Theme
- Add suitable text inputs, text areas, toggles, image fields, color pickers, and numeric controls.
- Pair every color picker with a manual hex input.
- Add Save Section, Save All, Reset Section, and Reset All actions.
- Add confirmation dialogs for resets and unsaved-change warnings.
- Display validation errors and success messages clearly.

### Acceptance Criteria

- Every supported setting can be edited from the admin screen.
- Forms remain usable on desktop, tablet, and mobile.
- Reset operations require confirmation.
- Validation errors retain the administrator's valid input.

## Task 6 — Connect Branding and Landing Page Content

Replace hardcoded public landing-page content and assets with resolved settings.

### Work

- Connect the site name, favicon, landing logo, hero image, headings, descriptions, buttons, section text, and footer text.
- Support configurable primary and secondary button labels and destinations.
- Validate button destinations as safe internal paths or approved URLs.
- Add show/hide toggles for optional landing sections.
- Preserve current content as fallback values.

### Acceptance Criteria

- Saved landing settings appear on the public page immediately.
- Empty optional content does not leave broken spacing or elements.
- Invalid destinations cannot be saved.
- Resetting the section restores the current built-in landing design.

## Task 7 — Connect Login Page Content and Branding

Replace hardcoded login-page values with resolved settings.

### Work

- Replace the hardcoded `images/d.png` logo with the configurable login logo.
- Connect the welcome title, subtitle, field labels, button text, footer message, page background, and card background.
- Apply configurable login button colors using theme variables.
- Preserve accessible labels and adequate contrast.

### Acceptance Criteria

- Login branding and text update without changing authentication behavior.
- The login form remains responsive and keyboard accessible.
- Missing custom values use current page content and styling.

## Task 8 — Refactor Sidebar into Theme Tokens

Make the existing admin sidebar consume shared customization values.

### Work

- Add configurable sidebar and compact logos.
- Connect dashboard label text where appropriate.
- Replace hardcoded sidebar colors with CSS custom properties.
- Apply background, text, active, hover, divider, expanded-width, and collapsed-width settings.
- Allow approved menu items to be renamed or hidden.
- Prevent essential administration and customization navigation from being removed or hidden in a way that causes lockout.

### Acceptance Criteria

- Sidebar settings apply across every admin page.
- Expanded, collapsed, and mobile states continue working.
- Administrators cannot lock themselves out of the customization screen.
- Existing route permissions remain unchanged.

## Task 9 — Standardize and Theme Buttons

Create reusable button variants and remove relevant hardcoded button colors.

### Work

- Define shared primary, secondary, success, warning, danger, and neutral button classes.
- Expose background, text, border, hover, radius, and shadow tokens through CSS variables.
- Update existing Blade views to use the shared variants.
- Preserve disabled, loading, focus, and active states.
- Check color contrast for normal and hover states.

### Acceptance Criteria

- Admin button settings consistently affect all migrated buttons.
- Destructive actions remain visually distinguishable.
- Focus and disabled states remain accessible.
- No migrated button depends on an inline hardcoded color.

## Task 10 — Standardize and Theme Tables

Create a shared table theme used by data-management screens.

### Work

- Define shared table classes and CSS variables.
- Support header, body, text, border, striped-row, hover-row, selected-row, radius, and spacing settings.
- Migrate student, employee, account, attendance, feedback, file, and report tables.
- Preserve responsive wrappers, sorting, pagination, and action columns.

### Acceptance Criteria

- Table settings consistently affect all migrated tables.
- Compact and comfortable spacing modes work.
- Tables remain readable and responsive on small screens.
- Existing table interactions continue working.

## Task 11 — Add Live Preview

Let administrators preview unsaved content and theme changes safely.

### Work

- Add a preview panel with desktop and mobile widths.
- Update the preview as fields and color inputs change.
- Use a scoped preview or iframe so preview styles do not modify the admin form itself.
- Preview uploaded images before they are saved.
- Ensure preview requests do not publish or persist data.

### Acceptance Criteria

- Text, images, colors, and spacing can be previewed before saving.
- Preview changes do not affect public pages.
- Closing or refreshing without saving discards preview-only changes.

## Task 12 — Add Revisions and Restore Support

Record customization changes and make accidental updates recoverable.

### Work

- Add a `site_customization_logs` or `setting_revisions` table.
- Record administrator, setting key, previous value, new value, timestamp, and request metadata where appropriate.
- Group changes made in one save operation.
- Show recent changes in the customization area.
- Add restore-previous-version and factory-default actions.
- Do not expose sensitive request data in logs.

### Acceptance Criteria

- Every published change records who changed what and when.
- An administrator can restore a previous customization state.
- Restore operations are also recorded.

## Task 13 — Add Automated Tests

Cover authorization, validation, persistence, rendering, uploads, resets, and restoration.

### Work

- Add feature tests for admin-only access.
- Test every settings group and validation rule.
- Test upload, replace, remove, and fallback behavior using fake storage.
- Test cache invalidation.
- Test landing, login, sidebar, button, and table rendering.
- Test section reset, reset all, and revision restoration.
- Add regression tests for existing attendance settings.

### Acceptance Criteria

- All new tests pass.
- Existing tests continue passing.
- Authorization and upload security have explicit negative tests.

## Task 14 — Visual QA, Accessibility, and Documentation

Complete release verification and document how the feature is operated and maintained.

### Work

- Verify landing, login, and admin pages at desktop, tablet, and mobile sizes.
- Test sidebar expanded, collapsed, and mobile modes.
- Check text and interactive-element color contrast.
- Verify keyboard navigation, focus indicators, labels, and error messages.
- Document supported image formats, limits, defaults, storage setup, deployment steps, and reset behavior.
- Add an administrator usage guide with screenshots if required.

### Acceptance Criteria

- No broken layouts or unreadable color combinations remain in supported views.
- The feature is documented for administrators and developers.
- Production deployment includes storage setup, migrations, cache clearing, and rollback instructions.

## Recommended Delivery Milestones

### Milestone 1 — Core Settings

Tasks 1–4: definitions, service, protected endpoints, and image management.

### Milestone 2 — Admin and Public Branding

Tasks 5–7: customization UI, landing page, and login page.

### Milestone 3 — Shared Admin Theme

Tasks 8–10: sidebar, buttons, and tables.

### Milestone 4 — Safety and Release

Tasks 11–14: preview, revision history, tests, accessibility, and documentation.

## Definition of Done

- An administrator can customize the requested text, images, logos, sidebar, buttons, and tables without editing code.
- Unauthorized users cannot read or change customization settings.
- Invalid uploads and setting values are rejected safely.
- Defaults and reset controls prevent broken or unrecoverable designs.
- Changes render consistently across relevant desktop and mobile pages.
- Automated tests pass and deployment steps are documented.
