@php
    $siteSettings = app(\App\Services\SiteSettingsService::class);
    $loginSettings = $siteSettings->group('login');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $loginSettings['login.title'] }} — {{ $siteSettings->get('branding.site_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @include('layouts.partials.site-theme')
    <style>
        body { min-height:100vh; background:{{ $loginSettings['login.page_background'] }} !important; color:{{ $loginSettings['login.text_color'] }}; }
        .login-card { max-width:450px; padding:3rem; background:{{ $loginSettings['login.card_background'] }}; border-radius:{{ $loginSettings['login.card_radius'] }}px; }
        .rounded-custom { border-radius:20px; padding:14px; font-size:18px; }
        .login-title { font-size:26px; }
        .login-subtitle { font-size:17px; color:{{ $loginSettings['login.muted_text_color'] }} !important; }
        .password-control { position:relative; }
        .password-control .form-control { padding-right:52px; }
        .password-toggle { position:absolute; right:14px; bottom:12px; display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border:0; background:transparent; color:#6c757d; cursor:pointer; }
        .password-toggle:hover, .password-toggle:focus { color:{{ $loginSettings['login.button_background'] }}; }
        .password-toggle svg { width:22px; height:22px; }
        .password-toggle .eye-off-icon, .password-toggle.is-visible .eye-icon { display:none; }
        .password-toggle.is-visible .eye-off-icon { display:block; }
        .login-submit { background:{{ $loginSettings['login.button_background'] }}; color:{{ $loginSettings['login.button_text_color'] }}; border-color:{{ $loginSettings['login.button_background'] }}; }
        .login-submit:hover, .login-submit:focus { background:{{ $loginSettings['login.button_hover_background'] }}; color:{{ $loginSettings['login.button_text_color'] }}; }
        @media (max-width:576px) { .login-card { padding:2rem 1rem; } .login-title { font-size:22px; } .login-subtitle { font-size:15px; } .rounded-custom { font-size:16px; padding:12px; } }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center">
    <main class="card shadow login-card w-100 mx-3">
        <div class="text-center"><img src="{{ $siteSettings->publicUrl('branding.login_logo') }}" alt="{{ $siteSettings->get('branding.site_name') }}" class="mb-3" style="max-width:180px;width:100%;"></div>
        <h1 class="text-center fw-bold login-title h5">{{ $loginSettings['login.title'] }}</h1>
        @if($loginSettings['login.subtitle'])<p class="text-center login-subtitle">{{ $loginSettings['login.subtitle'] }}</p>@endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label" for="email">{{ $loginSettings['login.email_label'] }}</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control rounded-custom @error('email') is-invalid @enderror" autocomplete="email" required autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 password-control">
                <label class="form-label" for="password">{{ $loginSettings['login.password_label'] }}</label>
                <input type="password" name="password" id="password" class="form-control rounded-custom" autocomplete="current-password" required>
                <button class="password-toggle" type="button" aria-label="Show password" aria-controls="password">
                    <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 3l18 18"/><path d="M6.4 6.4C3.5 8.4 2 12 2 12s3.5 8 10 8a9.7 9.7 0 0 0 4.1-.9"/></svg>
                </button>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
                <div class="form-check"><input class="form-check-input" type="checkbox" id="remember" name="remember"><label class="form-check-label" for="remember">Remember me</label></div>
                @if(Route::has('password.request'))<a href="{{ route('password.request') }}">Forgot password?</a>@endif
            </div>
            <div class="d-grid mt-4"><button type="submit" class="btn btn-lg login-submit">{{ $loginSettings['login.button_text'] }}</button></div>
            <div class="d-grid mt-3"><a href="{{ route('patron.register') }}" class="btn btn-outline-primary btn-lg">{{ $loginSettings['login.register_button_text'] }}</a></div>
        </form>
        @if($loginSettings['login.footer_message'])<p class="text-center small mt-3 mb-0">{{ $loginSettings['login.footer_message'] }}</p>@endif
    </main>
    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.querySelector('.password-toggle');
        passwordToggle?.addEventListener('click', () => { const show=passwordInput.type==='password'; passwordInput.type=show?'text':'password'; passwordToggle.classList.toggle('is-visible',show); passwordToggle.setAttribute('aria-label',show?'Hide password':'Show password'); });
    </script>
</body>
</html>
