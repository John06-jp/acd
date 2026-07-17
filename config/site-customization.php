<?php

/*
|--------------------------------------------------------------------------
| Site customization registry
|--------------------------------------------------------------------------
|
| This is the only allowlist of values administrators may customize. Values
| stored in the settings table are overrides; these defaults remain the
| factory design when the table is empty. Rules validate persisted values.
| Upload rules are kept separately because image defaults are built-in assets.
|
*/

$setting = static fn (
    string $group,
    string $type,
    mixed $default,
    array $rules,
    string $label,
    string $control = 'text',
    array $extra = [],
): array => array_merge([
    'group' => $group,
    'type' => $type,
    'default' => $default,
    'rules' => $rules,
    'label' => $label,
    'control' => $control,
], $extra);

$image = static fn (string $group, string $default, string $label, int $maxKb, int $minWidth, int $minHeight, int $maxWidth, int $maxHeight): array => [
    'group' => $group,
    'type' => 'image',
    'default' => $default,
    'rules' => ['nullable', 'string', 'max:255', 'regex:/^site-customization\/[A-Za-z0-9._-]+$/'],
    'label' => $label,
    'control' => 'image',
    'upload_rules' => [
        'required',
        'file',
        'image',
        'mimes:png,jpg,jpeg,webp',
        'mimetypes:image/png,image/jpeg,image/webp',
        "max:{$maxKb}",
        "dimensions:min_width={$minWidth},min_height={$minHeight},max_width={$maxWidth},max_height={$maxHeight}",
    ],
    'accept' => ['image/png', 'image/jpeg', 'image/webp'],
];

$definitions = [
    'branding.site_name' => $setting('branding', 'string', config('app.name', 'Pantas'), ['required', 'string', 'max:80'], 'Site name'),
    'branding.favicon' => $image('branding', 'favicon.ico', 'Favicon', 1024, 16, 16, 512, 512),
    'branding.landing_logo' => $image('branding', 'images/pantasLogo.png', 'Landing logo', 4096, 120, 40, 5000, 3000),
    'branding.login_logo' => $image('branding', 'images/d.png', 'Login logo', 4096, 120, 40, 5000, 3000),
    'branding.sidebar_logo' => $image('branding', 'images/pantasLogo.png', 'Sidebar logo', 4096, 120, 40, 5000, 3000),
    'branding.sidebar_compact_logo' => $image('branding', 'images/pantasLogo.png', 'Compact sidebar logo', 2048, 32, 32, 2000, 2000),

    'landing.hero_image' => $image('landing', 'images/Bannernew.jpg', 'Hero image', 8192, 800, 240, 8000, 5000),
    'landing.show_hero_content' => $setting('landing', 'boolean', false, ['required', 'boolean'], 'Show hero text and actions', 'toggle'),
    'landing.hero_heading' => $setting('landing', 'string', 'Welcome to Pantas', ['required', 'string', 'max:120'], 'Hero heading'),
    'landing.hero_description' => $setting('landing', 'string', 'Assumption College of Davao Library', ['nullable', 'string', 'max:500'], 'Hero description', 'textarea'),
    'landing.primary_button_label' => $setting('landing', 'string', 'Register', ['required', 'string', 'max:40'], 'Primary button label'),
    'landing.primary_button_url' => $setting('landing', 'url', '/register', ['required', 'string', 'max:2048'], 'Primary button destination', 'url', ['url_policy' => 'safe_destination']),
    'landing.secondary_button_label' => $setting('landing', 'string', 'Login', ['required', 'string', 'max:40'], 'Secondary button label'),
    'landing.secondary_button_url' => $setting('landing', 'url', '/login', ['required', 'string', 'max:2048'], 'Secondary button destination', 'url', ['url_policy' => 'safe_destination']),
    'landing.show_faq' => $setting('landing', 'boolean', true, ['required', 'boolean'], 'Show FAQ section', 'toggle'),
    'landing.section_heading' => $setting('landing', 'string', 'Frequently Asked Questions', ['nullable', 'string', 'max:120'], 'Section heading'),
    'landing.section_subheading' => $setting('landing', 'string', 'Getting Started', ['nullable', 'string', 'max:120'], 'Section subheading'),
    'landing.section_description' => $setting('landing', 'string', '', ['nullable', 'string', 'max:1000'], 'Section description', 'textarea'),
    'landing.footer_text' => $setting('landing', 'string', 'Pantas © 2025. All Rights Reserved.', ['nullable', 'string', 'max:200'], 'Footer text'),
    'landing.show_footer' => $setting('landing', 'boolean', false, ['required', 'boolean'], 'Show landing footer', 'toggle'),

    'login.title' => $setting('login', 'string', 'Welcome! Let’s Begin', ['required', 'string', 'max:100'], 'Login title'),
    'login.subtitle' => $setting('login', 'string', 'Log in to Continue', ['nullable', 'string', 'max:200'], 'Login subtitle'),
    'login.email_label' => $setting('login', 'string', 'Email address', ['required', 'string', 'max:60'], 'Email label'),
    'login.password_label' => $setting('login', 'string', 'Password', ['required', 'string', 'max:60'], 'Password label'),
    'login.button_text' => $setting('login', 'string', 'Login', ['required', 'string', 'max:40'], 'Login button text'),
    'login.register_button_text' => $setting('login', 'string', 'Register', ['required', 'string', 'max:40'], 'Registration button text'),
    'login.footer_message' => $setting('login', 'string', '', ['nullable', 'string', 'max:240'], 'Login footer message', 'textarea'),
    'login.page_background' => $setting('login', 'color', '#f8f9fa', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Page background', 'color'),
    'login.card_background' => $setting('login', 'color', '#ffffff', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Card background', 'color'),
    'login.text_color' => $setting('login', 'color', '#212529', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Text color', 'color'),
    'login.muted_text_color' => $setting('login', 'color', '#6c757d', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Muted text color', 'color'),
    'login.button_background' => $setting('login', 'color', '#0d6efd', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Button background', 'color'),
    'login.button_text_color' => $setting('login', 'color', '#ffffff', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Button text', 'color'),
    'login.button_hover_background' => $setting('login', 'color', '#0b5ed7', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Button hover background', 'color'),
    'login.card_radius' => $setting('login', 'integer', 20, ['required', 'integer', 'min:0', 'max:48'], 'Card radius', 'number', ['unit' => 'px']),

    'sidebar.dashboard_label' => $setting('sidebar', 'string', 'Dashboard', ['required', 'string', 'max:40'], 'Dashboard label'),
    'sidebar.background' => $setting('sidebar', 'color', '#0373e3', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Background', 'color'),
    'sidebar.text' => $setting('sidebar', 'color', '#ffffff', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Text', 'color'),
    'sidebar.muted_text' => $setting('sidebar', 'color', '#6b7280', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Muted text', 'color'),
    'sidebar.active_background' => $setting('sidebar', 'color', '#ffc233', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Active background', 'color'),
    'sidebar.active_text' => $setting('sidebar', 'color', '#ffffff', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Active text', 'color'),
    'sidebar.hover_background' => $setting('sidebar', 'color', '#0373e3', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Hover background', 'color'),
    'sidebar.hover_text' => $setting('sidebar', 'color', '#ffffff', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Hover text', 'color'),
    'sidebar.divider' => $setting('sidebar', 'color', '#ffffff', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Divider', 'color'),
    'sidebar.expanded_width' => $setting('sidebar', 'integer', 260, ['required', 'integer', 'min:220', 'max:360'], 'Expanded width', 'number', ['unit' => 'px']),
    'sidebar.collapsed_width' => $setting('sidebar', 'integer', 72, ['required', 'integer', 'min:56', 'max:100'], 'Collapsed width', 'number', ['unit' => 'px']),
    'sidebar.home_label' => $setting('sidebar', 'string', 'Home', ['required', 'string', 'max:40'], 'Home label'),
    'sidebar.attendance_label' => $setting('sidebar', 'string', 'Attendance', ['required', 'string', 'max:40'], 'Attendance label'),
    'sidebar.data_label' => $setting('sidebar', 'string', 'Data', ['required', 'string', 'max:40'], 'Data label'),
    'sidebar.communication_label' => $setting('sidebar', 'string', 'Communication', ['required', 'string', 'max:40'], 'Communication label'),
    'sidebar.show_attendance' => $setting('sidebar', 'boolean', true, ['required', 'boolean'], 'Show Attendance menu', 'toggle'),
    'sidebar.show_data' => $setting('sidebar', 'boolean', true, ['required', 'boolean'], 'Show Data menu', 'toggle'),
    'sidebar.show_communication' => $setting('sidebar', 'boolean', true, ['required', 'boolean'], 'Show Communication menu', 'toggle'),

    'tables.header_background' => $setting('tables', 'color', '#29abe2', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Header background', 'color'),
    'tables.header_text' => $setting('tables', 'color', '#ffffff', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Header text', 'color'),
    'tables.body_background' => $setting('tables', 'color', '#ffffff', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Body background', 'color'),
    'tables.body_text' => $setting('tables', 'color', '#212529', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Body text', 'color'),
    'tables.border' => $setting('tables', 'color', '#dee2e6', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Border', 'color'),
    'tables.stripe_background' => $setting('tables', 'color', '#f8f9fa', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Striped row', 'color'),
    'tables.hover_background' => $setting('tables', 'color', '#e3f4fc', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Hover row', 'color'),
    'tables.selected_background' => $setting('tables', 'color', '#cfefff', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Selected row', 'color'),
    'tables.radius' => $setting('tables', 'integer', 8, ['required', 'integer', 'min:0', 'max:32'], 'Table radius', 'number', ['unit' => 'px']),
    'tables.spacing' => $setting('tables', 'enum', 'comfortable', ['required', 'in:compact,comfortable'], 'Row spacing', 'select', ['options' => ['compact', 'comfortable']]),
    'tables.compact_padding' => $setting('tables', 'integer', 6, ['required', 'integer', 'min:2', 'max:16'], 'Compact cell padding', 'number', ['unit' => 'px']),
    'tables.comfortable_padding' => $setting('tables', 'integer', 12, ['required', 'integer', 'min:6', 'max:24'], 'Comfortable cell padding', 'number', ['unit' => 'px']),

    'advanced_theme.font_family' => $setting('advanced_theme', 'enum', 'Inter', ['required', 'in:Inter,Segoe UI,Arial,Georgia'], 'Font family', 'select', ['options' => ['Inter', 'Segoe UI', 'Arial', 'Georgia']]),
    'advanced_theme.page_background' => $setting('advanced_theme', 'color', '#f4f9fc', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Page background', 'color'),
    'advanced_theme.focus_ring' => $setting('advanced_theme', 'color', '#29abe2', ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], 'Focus ring', 'color'),
];

$buttonDefaults = [
    'primary' => ['#29abe2', '#ffffff', '#29abe2', '#1a8fc4', '#ffffff'],
    'secondary' => ['#6c757d', '#ffffff', '#6c757d', '#5c636a', '#ffffff'],
    'success' => ['#198754', '#ffffff', '#198754', '#157347', '#ffffff'],
    'warning' => ['#ffb845', '#125a82', '#ffb845', '#e5a53e', '#125a82'],
    'danger' => ['#dc3545', '#ffffff', '#dc3545', '#bb2d3b', '#ffffff'],
    'neutral' => ['#f8f9fa', '#212529', '#d3d6d8', '#e2e6ea', '#212529'],
];

foreach ($buttonDefaults as $variant => [$background, $text, $border, $hoverBackground, $hoverText]) {
    $label = ucfirst($variant);
    $definitions["buttons.{$variant}_background"] = $setting('buttons', 'color', $background, ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], "{$label} background", 'color', ['variant' => $variant]);
    $definitions["buttons.{$variant}_text"] = $setting('buttons', 'color', $text, ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], "{$label} text", 'color', ['variant' => $variant]);
    $definitions["buttons.{$variant}_border"] = $setting('buttons', 'color', $border, ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], "{$label} border", 'color', ['variant' => $variant]);
    $definitions["buttons.{$variant}_hover_background"] = $setting('buttons', 'color', $hoverBackground, ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], "{$label} hover background", 'color', ['variant' => $variant]);
    $definitions["buttons.{$variant}_hover_text"] = $setting('buttons', 'color', $hoverText, ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'], "{$label} hover text", 'color', ['variant' => $variant]);
}

$definitions['buttons.radius'] = $setting('buttons', 'integer', 8, ['required', 'integer', 'min:0', 'max:32'], 'Button radius', 'number', ['unit' => 'px']);
$definitions['buttons.shadow'] = $setting('buttons', 'enum', 'medium', ['required', 'in:none,small,medium'], 'Button shadow', 'select', ['options' => ['none', 'small', 'medium']]);

ksort($definitions);

return [
    'groups' => [
        'branding' => 'Branding',
        'landing' => 'Landing Page',
        'login' => 'Login Page',
        'sidebar' => 'Sidebar',
        'buttons' => 'Buttons',
        'tables' => 'Tables',
        'advanced_theme' => 'Advanced Theme',
    ],
    'definitions' => $definitions,
    'image_directory' => 'site-customization',
    'allowed_destination_schemes' => ['http', 'https'],
];
