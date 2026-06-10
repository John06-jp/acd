<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Important for mobile scaling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .rounded-custom {
            border-radius: 20px;
            padding: 14px;
            font-size: 18px;
        }

        .login-title {
            font-size: 26px;
        }

        .login-subtitle {
            font-size: 17px;
        }

        .password-field {
            position: relative;
        }

        .password-field .form-control {
            padding-right: 52px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: 0;
            background: transparent;
            color: #6c757d;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .password-toggle:hover,
        .password-toggle:focus {
            color: #0d6efd;
            outline: none;
        }

        .password-toggle svg {
            width: 22px;
            height: 22px;
        }

        .password-toggle .eye-off-icon {
            display: none;
        }

        .password-toggle.is-visible .eye-icon {
            display: none;
        }

        .password-toggle.is-visible .eye-off-icon {
            display: block;
        }

        @media (max-width: 576px) {
            .login-title {
                font-size: 22px;
            }

            .login-subtitle {
                font-size: 15px;
            }

            .rounded-custom {
                font-size: 16px;
                padding: 12px;
            }

            .card {
                padding: 2rem 1rem !important;
            }
        }
    </style>
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">

    <div class="card shadow login-card w-100 mx-3" style="max-width: 450px; padding: 3rem;">
        <div class="text-center">
            <img src="{{ asset('images/d.png') }}" alt="Area 51 Logo" class="mb-3" style="max-width: 180px; width: 100%;">
        </div>

        <h5 class="text-center fw-bold login-title">Welcome! Let’s Begin</h5>
        <p class="text-center text-muted login-subtitle">Log in to Continue</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <input type="email" name="email" class="form-control rounded-custom" placeholder="Email" required autofocus>
            </div>
            <div class="mb-3 password-field">
                <input type="password" name="password" id="password" class="form-control rounded-custom" placeholder="Password" required>
                <button class="password-toggle" type="button" aria-label="Show password" aria-controls="password">
                    <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 3l18 18"></path>
                        <path d="M10.6 10.6A2 2 0 0 0 12 14a2 2 0 0 0 1.4-.6"></path>
                        <path d="M9.9 4.2A9.4 9.4 0 0 1 12 4c6.5 0 10 8 10 8a18.6 18.6 0 0 1-3.1 4.4"></path>
                        <path d="M6.4 6.4C3.5 8.4 2 12 2 12s3.5 8 10 8a9.7 9.7 0 0 0 4.1-.9"></path>
                    </svg>
                </button>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label text-lowercase" for="remember">
                        Remember me
                    </label>
                </div>
                <a href="#" class="text-primary">Forgot password?</a>
            </div>

            @error('email')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Login</button>
            </div>
            
        
            
            <div class="d-grid mt-3">
                <a href="{{ route('patron.register') }}" class="btn btn-outline-primary btn-lg">
                    Register
                </a>
            </div>

        </form>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.querySelector('.password-toggle');

        passwordToggle?.addEventListener('click', () => {
            const shouldShow = passwordInput.type === 'password';

            passwordInput.type = shouldShow ? 'text' : 'password';
            passwordToggle.classList.toggle('is-visible', shouldShow);
            passwordToggle.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');
        });
    </script>
</body>
</html>
