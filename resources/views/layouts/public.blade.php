<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php $siteSettingsService = app(\App\Services\SiteSettingsService::class); @endphp
    <title>@yield('title', 'Registration — ' . $siteSettingsService->get('branding.site_name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ \App\Support\Branding::stylesheetUrl() }}">
    <link rel="stylesheet" href="{{ asset('css/layout/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/app-fonts.css') }}">
    @include('layouts.partials.site-theme')
    @stack('styles')
</head>
<body style="background:#f8f9fa;">
    @include('layouts.partials.navbar')
    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>
    @stack('scripts')
    @yield('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
