@php
    $siteSettings = app(\App\Services\SiteSettingsService::class);
    $siteTheme = $siteSettings->all();
    $buttonShadow = match ($siteTheme['buttons.shadow']) { 'none' => 'none', 'small' => '0 2px 5px rgba(15,23,42,.10)', default => '0 6px 14px rgba(15,23,42,.14)' };
    $tablePadding = $siteTheme['tables.spacing'] === 'compact' ? $siteTheme['tables.compact_padding'] : $siteTheme['tables.comfortable_padding'];
@endphp
<link rel="icon" href="{{ $siteSettings->publicUrl('branding.favicon') }}">
<link rel="stylesheet" href="{{ asset('css/site-theme.css') }}">
<style id="site-customization-theme">
:root {
    --site-font-family: {{ json_encode($siteTheme['advanced_theme.font_family']) }}, sans-serif;
    --site-page-bg: {{ $siteTheme['advanced_theme.page_background'] }};
    --site-focus-ring: {{ $siteTheme['advanced_theme.focus_ring'] }};
    --site-sidebar-bg: {{ $siteTheme['sidebar.background'] }};
    --site-sidebar-text: {{ $siteTheme['sidebar.text'] }};
    --site-sidebar-muted: {{ $siteTheme['sidebar.muted_text'] }};
    --site-sidebar-active-bg: {{ $siteTheme['sidebar.active_background'] }};
    --site-sidebar-active-text: {{ $siteTheme['sidebar.active_text'] }};
    --site-sidebar-hover-bg: {{ $siteTheme['sidebar.hover_background'] }};
    --site-sidebar-hover-text: {{ $siteTheme['sidebar.hover_text'] }};
    --site-sidebar-divider: {{ $siteTheme['sidebar.divider'] }};
    --site-sidebar-width: {{ $siteTheme['sidebar.expanded_width'] }}px;
    --site-sidebar-collapsed-width: {{ $siteTheme['sidebar.collapsed_width'] }}px;
    --site-button-radius: {{ $siteTheme['buttons.radius'] }}px;
    --site-button-shadow: {{ $buttonShadow }};
    @foreach(['primary','secondary','success','warning','danger','neutral'] as $variant)
    --site-button-{{ $variant }}-bg: {{ $siteTheme["buttons.{$variant}_background"] }};
    --site-button-{{ $variant }}-text: {{ $siteTheme["buttons.{$variant}_text"] }};
    --site-button-{{ $variant }}-border: {{ $siteTheme["buttons.{$variant}_border"] }};
    --site-button-{{ $variant }}-hover-bg: {{ $siteTheme["buttons.{$variant}_hover_background"] }};
    --site-button-{{ $variant }}-hover-text: {{ $siteTheme["buttons.{$variant}_hover_text"] }};
    @endforeach
    --site-table-header-bg: {{ $siteTheme['tables.header_background'] }};
    --site-table-header-text: {{ $siteTheme['tables.header_text'] }};
    --site-table-body-bg: {{ $siteTheme['tables.body_background'] }};
    --site-table-body-text: {{ $siteTheme['tables.body_text'] }};
    --site-table-border: {{ $siteTheme['tables.border'] }};
    --site-table-stripe: {{ $siteTheme['tables.stripe_background'] }};
    --site-table-hover: {{ $siteTheme['tables.hover_background'] }};
    --site-table-selected: {{ $siteTheme['tables.selected_background'] }};
    --site-table-radius: {{ $siteTheme['tables.radius'] }}px;
    --site-table-cell-padding: {{ $tablePadding }}px;
}
</style>
