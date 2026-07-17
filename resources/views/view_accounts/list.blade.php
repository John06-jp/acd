@extends('layouts.sec')

@section('title', 'User Accounts')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/layout/data-pages.css') }}">
    <style>
        .accounts-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(17,24,39,0.07);
            overflow: hidden;
        }

        .accounts-card-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            background: var(--brand-primary-darker, #125a82);
            color: #fff;
        }

        .accounts-card-header h4 {
            font-family: var(--brand-font-family, 'Inter', sans-serif);
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin: 0;
            color: #fff;
        }

        .accounts-card-body { padding: 1rem 1.25rem; }

        /* Search row */
        .accounts-search-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .accounts-search-row .form-control {
            font-family: var(--brand-font-family, 'Inter', sans-serif);
            font-size: 0.875rem;
            border-radius: 8px;
            border-color: rgba(17,24,39,0.15);
            flex: 1 1 220px;
        }

        .accounts-search-row .form-control:focus {
            border-color: var(--brand-primary, #29abe2);
            box-shadow: 0 0 0 3px rgba(41, 171, 226, 0.15);
        }

        /* User cell */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: #fff;
            line-height: 1;
        }

        .user-name-block strong {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            color: #111827;
            line-height: 1.2;
        }

        .user-name-block small {
            font-size: 0.78rem;
            color: #6b7280;
        }

        /* Table */
        .accounts-table { font-size: 0.875rem; margin: 0; }

        .accounts-table thead th {
            background: var(--brand-primary, #29abe2);
            color: #fff;
            font-weight: 600;
            font-size: 0.78rem;
            letter-spacing: 0.03em;
            padding: 0.65rem 0.85rem;
            border-color: rgba(255,255,255,0.15);
            vertical-align: middle;
        }

        .accounts-table tbody td {
            padding: 0.6rem 0.85rem;
            vertical-align: middle;
            border-color: rgba(17,24,39,0.06);
            color: #374151;
        }

        .accounts-table tbody tr:hover td { background: rgba(41, 171, 226, 0.04); }

        /* Role badges */
        .role-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25em 0.65em;
            border-radius: 99px;
        }

        .role-pill-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .role-admin   { background: #1e293b; color: #f8fafc; }
        .role-staff   { background: #dbeafe; color: #1d4ed8; }
        .role-faculty { background: #fef3c7; color: #b45309; }
        .role-student { background: #f3f4f6; color: #374151; }

        /* Action buttons */
        .action-btns { display: flex; gap: 0.35rem; justify-content: center; }

        .btn-edit-sm {
            display: inline-flex; align-items: center; gap: 0.25rem;
            font-size: 0.78rem; font-weight: 600;
            padding: 0.3rem 0.6rem; border-radius: 7px;
            background: var(--brand-primary, #29abe2);
            border: 1px solid var(--brand-primary, #29abe2);
            color: #fff; text-decoration: none;
            transition: background 0.15s, border-color 0.15s;
        }

        .btn-edit-sm:hover {
            background: var(--brand-primary-dark, #1a8fc4);
            border-color: var(--brand-primary-dark, #1a8fc4);
            color: #fff;
        }

        .btn-delete-sm {
            display: inline-flex; align-items: center; gap: 0.25rem;
            font-size: 0.78rem; font-weight: 600;
            padding: 0.3rem 0.6rem; border-radius: 7px;
            background: transparent;
            border: 1px solid rgba(239,68,68,0.3);
            color: #dc2626;
            transition: background 0.15s, border-color 0.15s;
        }

        .btn-delete-sm:hover {
            background: #fee2e2;
            border-color: #dc2626;
        }
    </style>
@endpush

@section('content')
@php
    $avatarColors = ['#125a82','#16a34a','#d97706','#7c3aed','#db2777','#0891b2','#059669'];
    $colorFor = fn(string $seed) => $avatarColors[abs(crc32($seed)) % count($avatarColors)];
@endphp

<div class="data-page accounts-page mt-3">
    <div class="accounts-card">
        <div class="accounts-card-header">
            <h4>User Accounts</h4>
            <a href="{{ route('users.create') }}" class="btn btn-sm btn-add" style="background:#fff; color:var(--brand-primary-darker,#125a82); font-weight:700; font-size:0.8rem; border-radius:8px; border:none;">
                + Create Account
            </a>
        </div>

        <div class="accounts-card-body">
            @if(session('success'))
                <div class="alert alert-success rounded-3 py-2 px-3 mb-3" style="font-size:0.875rem;">
                    {{ session('success') }}
                </div>
            @endif

            <form method="GET" action="{{ route('users.index') }}" class="accounts-search-row">
                <input type="search" name="search" class="form-control form-control-sm"
                       placeholder="Search by name, email, or role…"
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-search-filter" style="white-space:nowrap">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary" style="white-space:nowrap">
                        Clear
                    </a>
                @endif
            </form>

            <div class="table-responsive site-table-wrap">
                <table class="table table-hover accounts-table site-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th class="text-center">Role</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            @php
                                $initials = strtoupper(substr($user->fname ?? $user->name ?? '?', 0, 1)) . strtoupper(substr($user->lname ?? '', 0, 1));
                                $initials = trim($initials) ?: '?';
                                $bgColor = $colorFor($user->email ?? $user->id);
                                $roleClass = 'role-' . ($user->role ?? 'student');
                            @endphp
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar" style="background: {{ $bgColor }};" title="{{ $user->name }}">
                                            {{ $initials }}
                                        </div>
                                        <div class="user-name-block">
                                            <strong>{{ $user->fname ?? '' }} {{ $user->lname ?? $user->name ?? '' }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:0.82rem; color:#4b5563;">{{ $user->email }}</td>
                                <td class="text-center">
                                    <span class="role-pill {{ $roleClass }}">
                                        <span class="role-pill-dot"></span>
                                        {{ ucfirst($user->role ?? 'student') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn-edit-sm">
                                            Edit
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete {{ addslashes($user->name ?? 'this user') }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <div style="font-size:2rem; margin-bottom:0.5rem">👤</div>
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
