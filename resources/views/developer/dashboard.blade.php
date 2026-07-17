@extends('layouts.app')

@section('title', 'Developer Dashboard')

@push('styles')
<style>
    .developer-dashboard { max-width:1000px; margin:0 auto; }
    .developer-hero, .developer-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; box-shadow:0 2px 12px rgba(15,23,42,.05); }
    .developer-hero { padding:1.5rem; margin-bottom:1rem; }
    .developer-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1rem; }
    .developer-card { padding:1.25rem; }
    .developer-stat { font-size:1.8rem; font-weight:800; color:var(--brand-primary-darker,#125a82); }
    @media (max-width:767px) { .developer-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')
<div class="developer-dashboard">
    <header class="developer-hero">
        <span class="badge text-bg-primary mb-2">Developer administration</span>
        <h1 class="h3 fw-bold">Developer Dashboard</h1>
        <p class="text-muted mb-3">Manage site branding, content, and shared visual themes separately from library administration.</p>
        <a class="site-btn site-btn-primary" href="{{ route('site-customization.index') }}">Open Site Customization</a>
    </header>

    <div class="developer-grid">
        <section class="developer-card"><div class="text-muted small">Supported settings</div><div class="developer-stat">{{ $settingCount }}</div></section>
        <section class="developer-card"><div class="text-muted small">Active overrides</div><div class="developer-stat">{{ $customizedCount }}</div></section>
        <section class="developer-card"><div class="text-muted small">Recent changes</div><div class="developer-stat">{{ $recentChanges->count() }}</div></section>
    </div>
</div>
@endsection
