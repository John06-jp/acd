@extends('layouts.sec')

@section('title', 'Create User Account')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/layout/data-pages.css') }}">
    <style>
        .create-user-page {
            min-height: calc(100vh - 120px);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 0.75rem 0 1.5rem;
            font-family: var(--brand-font-family, 'Inter', sans-serif);
        }

        .create-user-card {
            width: min(100%, 680px);
            background: #fff;
            border: 1px solid rgba(17, 24, 39, 0.12);
            border-radius: 12px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.09);
            overflow: hidden;
        }

        .create-user-header {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 1.25rem 1.75rem;
            background: #2167a8;
            color: #fff;
        }

        .create-user-icon {
            width: 46px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.16);
            font-size: 1.15rem;
        }

        .create-user-header h4 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: 0;
            color: #fff;
        }

        .create-user-header p {
            margin: 0.2rem 0 0;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.78);
        }

        .create-user-body {
            padding: 1.35rem 1.75rem 1.6rem;
        }

        .create-user-alert {
            border-radius: 10px;
            font-size: 0.82rem;
            margin-bottom: 1rem;
        }

        .create-user-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.9rem 1rem;
        }

        .create-user-field {
            min-width: 0;
        }

        .create-user-field--full {
            grid-column: 1 / -1;
        }

        .create-user-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.3rem;
            color: #3f3f46;
            font-size: 0.82rem;
            font-weight: 800;
        }

        .create-user-label i {
            width: 0.9rem;
            color: #4b5563;
            font-size: 0.82rem;
            text-align: center;
        }

        .create-user-input {
            width: 100%;
            min-height: 42px;
            padding: 0.55rem 0.75rem;
            border: 1.5px solid #d8d8d8;
            border-radius: 8px;
            background: #fff;
            color: #111827;
            font-size: 0.92rem;
            line-height: 1.2;
            transition: border-color 0.16s ease, box-shadow 0.16s ease;
        }

        .create-user-input::placeholder {
            color: #111827;
            opacity: 1;
        }

        .create-user-input:focus {
            border-color: #2167a8;
            box-shadow: 0 0 0 3px rgba(33, 103, 168, 0.14);
            outline: none;
        }

        .password-rule {
            margin-top: 0.55rem;
            padding-top: 0.55rem;
            border-top: 3px solid #d9d9d9;
            color: #71717a;
            font-size: 0.78rem;
        }

        .create-user-divider {
            grid-column: 1 / -1;
            height: 1px;
            background: #dedede;
        }

        .role-options {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.65rem;
        }

        .role-card {
            position: relative;
            min-height: 82px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.2rem;
            padding: 0.65rem;
            border: 1.5px solid #bcbcbc;
            border-radius: 8px;
            background: #fff;
            color: #3f3f46;
            cursor: pointer;
            text-align: center;
            transition: border-color 0.16s ease, box-shadow 0.16s ease, color 0.16s ease;
        }

        .role-card input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .role-card i {
            font-size: 0.9rem;
            color: #3f3f46;
        }

        .role-card strong {
            font-size: 0.88rem;
            font-weight: 800;
        }

        .role-card span {
            color: #71717a;
            font-size: 0.76rem;
        }

        .role-card:has(input:checked) {
            border-color: #2f80ed;
            box-shadow: 0 0 0 3px rgba(47, 128, 237, 0.12);
            color: #155ca3;
        }

        .role-card:has(input:checked) i,
        .role-card:has(input:checked) span {
            color: #2f80ed;
        }

        .create-user-actions {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(160px, 0.85fr);
            gap: 0.75rem;
            margin-top: 0.25rem;
        }

        .create-user-action {
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: 1.5px solid #bcbcbc;
            border-radius: 8px;
            background: #fff;
            color: #171717;
            font-size: 0.86rem;
            font-weight: 800;
            text-decoration: none;
            transition: background 0.16s ease, border-color 0.16s ease, box-shadow 0.16s ease;
        }

        .create-user-action:hover {
            background: #f8fafc;
            border-color: #2167a8;
            color: #111827;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
        }

        .create-user-action--secondary {
            font-weight: 600;
        }

        @media (max-width: 767.98px) {
            .create-user-page {
                padding: 0.75rem 0 1.5rem;
            }

            .create-user-card {
                border-radius: 10px;
            }

            .create-user-header,
            .create-user-body {
                padding: 1rem;
            }

            .create-user-header {
                align-items: flex-start;
            }

            .create-user-icon {
                width: 42px;
                height: 42px;
                font-size: 1rem;
            }

            .create-user-header h4 {
                font-size: 1.1rem;
            }

            .create-user-header p {
                font-size: 0.8rem;
            }

            .create-user-grid,
            .role-options,
            .create-user-actions {
                grid-template-columns: 1fr;
            }

            .create-user-input {
                min-height: 42px;
                font-size: 0.9rem;
            }

            .role-card {
                min-height: 78px;
            }

            .create-user-action {
                min-height: 42px;
                font-size: 0.85rem;
            }
        }
    </style>
@endpush

@section('content')
<div class="create-user-page">
    <div class="create-user-card">
        <div class="create-user-header">
            <div class="create-user-icon" aria-hidden="true">
                <i class="bi bi-person-plus"></i>
            </div>
            <div>
                <h4>Create user account</h4>
                <p>Add a new member to your workspace</p>
            </div>
        </div>

        <div class="create-user-body">
            @if(session('success'))
                <div class="alert alert-success create-user-alert">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger create-user-alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.store') }}" method="POST" class="create-user-grid">
                @csrf

                <div class="create-user-field">
                    <label class="create-user-label" for="fname">
                        <i class="bi bi-person"></i>
                        First name
                    </label>
                    <input id="fname" type="text" name="fname" class="create-user-input" value="{{ old('fname') }}" placeholder="e.g. Jane" required>
                </div>

                <div class="create-user-field">
                    <label class="create-user-label" for="lname">
                        <i class="bi bi-person"></i>
                        Last name
                    </label>
                    <input id="lname" type="text" name="lname" class="create-user-input" value="{{ old('lname') }}" placeholder="e.g. Smith" required>
                </div>

                <div class="create-user-field create-user-field--full">
                    <label class="create-user-label" for="email">
                        <i class="bi bi-envelope"></i>
                        Email address
                    </label>
                    <input id="email" type="email" name="email" class="create-user-input" value="{{ old('email') }}" placeholder="jane@example.com" required>
                </div>

                <div class="create-user-field create-user-field--full">
                    <label class="create-user-label" for="password">
                        <i class="bi bi-lock"></i>
                        Password
                    </label>
                    <input id="password" type="password" name="password" class="create-user-input" placeholder="Create a strong password" required>
                    <div class="password-rule">Use 6+ characters, mix of letters and numbers</div>
                </div>

                <div class="create-user-divider"></div>

                <div class="create-user-field create-user-field--full">
                    <div class="create-user-label">
                        <i class="bi bi-shield-check"></i>
                        Role
                    </div>
                    <div class="role-options">
                        <label class="role-card">
                            <input type="radio" name="role" value="admin" {{ old('role') === 'admin' ? 'checked' : '' }} required>
                            <i class="bi bi-shield-lock"></i>
                            <strong>Admin</strong>
                            <span>Full access</span>
                        </label>
                        <label class="role-card">
                            <input type="radio" name="role" value="staff" {{ old('role', 'staff') === 'staff' ? 'checked' : '' }} required>
                            <i class="bi bi-briefcase"></i>
                            <strong>Staff</strong>
                            <span>Day-to-day ops</span>
                        </label>
                        <label class="role-card">
                            <input type="radio" name="role" value="faculty" {{ old('role') === 'faculty' ? 'checked' : '' }} required>
                            <i class="bi bi-mortarboard"></i>
                            <strong>Faculty</strong>
                            <span>Teaching access</span>
                        </label>
                    </div>
                </div>

                <div class="create-user-actions">
                    <button type="submit" class="create-user-action">
                        <i class="bi bi-person-plus"></i>
                        Create account
                    </button>
                    <a href="{{ route('users.index') }}" class="create-user-action create-user-action--secondary">
                        <i class="bi bi-list-ul"></i>
                        View users
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
