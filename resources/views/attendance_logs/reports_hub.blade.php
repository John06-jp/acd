@extends('layouts.sec')

@section('title', 'Patron Reports')

@push('page-styles')
<style>
.rh-page {
    max-width: 860px;
    margin: 0 auto;
    padding: 1.75rem 1rem 3rem;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.rh-badge {
    display: inline-block;
    padding: 0.22rem 0.8rem;
    background: #eff4ff;
    color: #1250e2;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    margin-bottom: 0.6rem;
}

.rh-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 0.45rem;
    letter-spacing: -0.025em;
    line-height: 1.2;
}

.rh-sub {
    color: #64748b;
    font-size: 0.88rem;
    margin-bottom: 2rem;
    max-width: 640px;
    line-height: 1.65;
}

/* ── Divider ─────────────────────────────────────────────── */
.rh-divider {
    border: none;
    border-top: 1px solid #e2e8f0;
    margin: 1.75rem 0;
}

/* ── Filter card ─────────────────────────────────────────── */
.rh-filter-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 1.15rem 1.35rem 1rem;
    box-shadow: 0 1px 5px rgba(0,0,0,0.04);
    margin-bottom: 2rem;
}

.rh-section-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #94a3b8;
    margin: 0 0 0.7rem;
}

.rh-filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: flex-end;
}

.rh-filter-field {
    display: flex;
    flex-direction: column;
    gap: 0.28rem;
}

.rh-filter-field label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #475569;
    letter-spacing: 0.01em;
}

.rh-filter-field input[type="date"] {
    border: 1.5px solid #cbd5e1;
    border-radius: 9px;
    padding: 0.48rem 0.8rem;
    font-size: 0.875rem;
    color: #1e293b;
    background: #f8fafc;
    font-family: inherit;
    outline: none;
    transition: border-color 0.15s, background 0.15s;
    min-width: 148px;
}
.rh-filter-field input[type="date"]:focus {
    border-color: #1250e2;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(18,80,226,0.1);
}

.rh-filter-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    padding-bottom: 1px;
}

.rh-filter-notice {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    margin-top: 0.75rem;
    padding: 0.32rem 0.8rem;
    background: #eff4ff;
    border: 1px solid #bfcfff;
    border-radius: 8px;
    font-size: 0.78rem;
    color: #1250e2;
    font-weight: 500;
}

/* ── Primary action cards ────────────────────────────────── */
.rh-primary-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 2rem;
}
@media (max-width: 520px) { .rh-primary-grid { grid-template-columns: 1fr; } }

.rh-primary-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.1rem 1.2rem;
    border-radius: 13px;
    text-decoration: none;
    border: 1.5px solid transparent;
    transition: transform 0.16s ease, box-shadow 0.16s ease, border-color 0.16s ease;
}
.rh-primary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.09);
    text-decoration: none;
}
.rh-primary-card:active { transform: translateY(0); box-shadow: none; }

.rh-primary-card--blue {
    background: linear-gradient(135deg, #eff4ff 0%, #dce8ff 100%);
    border-color: #c7d8ff;
    color: #1250e2;
}
.rh-primary-card--blue:hover { border-color: #1250e2; color: #0e42c2; }

.rh-primary-card--green {
    background: linear-gradient(135deg, #f0fdf4 0%, #d9f7e4 100%);
    border-color: #bbf7d0;
    color: #16a34a;
}
.rh-primary-card--green:hover { border-color: #16a34a; color: #15803d; }

.rh-pcard-icon {
    flex: 0 0 auto;
    width: 46px;
    height: 46px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.45rem;
}
.rh-primary-card--blue .rh-pcard-icon  { background: rgba(18,80,226,0.13); }
.rh-primary-card--green .rh-pcard-icon { background: rgba(22,163,74,0.13); }

.rh-pcard-body { flex: 1 1 auto; min-width: 0; }
.rh-pcard-title {
    font-size: 1rem;
    font-weight: 700;
    line-height: 1.25;
    margin-bottom: 0.15rem;
}
.rh-pcard-sub { font-size: 0.8rem; opacity: 0.72; line-height: 1.35; }

.rh-pcard-arrow {
    flex: 0 0 auto;
    font-size: 1.15rem;
    opacity: 0.4;
    transition: opacity 0.15s, transform 0.15s;
}
.rh-primary-card:hover .rh-pcard-arrow { opacity: 1; transform: translateX(3px); }

/* ── Single report chip grid ─────────────────────────────── */
.rh-chip-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
    margin-bottom: 2rem;
}
@media (max-width: 580px) { .rh-chip-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 340px) { .rh-chip-grid { grid-template-columns: 1fr; } }

.rh-chip-card {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.8rem 0.9rem;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 11px;
    text-decoration: none;
    color: #1e293b;
    font-size: 0.875rem;
    font-weight: 600;
    line-height: 1.25;
    transition: border-color 0.15s, color 0.15s, box-shadow 0.15s, transform 0.13s, background 0.15s;
}
.rh-chip-card:hover {
    border-color: var(--chip-color, #1250e2);
    color: var(--chip-color, #1250e2);
    background: var(--chip-bg, #eff4ff);
    box-shadow: 0 4px 14px rgba(0,0,0,0.07);
    transform: translateY(-1px);
    text-decoration: none;
}
.rh-chip-card:active { transform: translateY(0); box-shadow: none; }

.rh-chip-icon {
    flex: 0 0 auto;
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: var(--chip-bg, #eff4ff);
    color: var(--chip-color, #1250e2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    transition: background 0.15s, color 0.15s;
}
.rh-chip-card:hover .rh-chip-icon {
    background: var(--chip-color, #1250e2);
    color: #fff;
}

/* ── Ghost button (if not in external CSS) ───────────────── */
.al-btn--ghost {
    background: transparent;
    border-color: #d1d5db;
    color: #64748b;
    text-decoration: none;
}
.al-btn--ghost:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #374151;
    text-decoration: none;
    box-shadow: none;
    transform: none;
}
</style>
@endpush

@section('content')
<div class="rh-page">

    {{-- Header ─────────────────────────────────────────────────── --}}
    <span class="rh-badge"><i class="bi bi-graph-up-arrow me-1"></i>Reports</span>
    <h1 class="rh-title">Patron gate — reports</h1>
    <p class="rh-sub">
        Summaries are built from <strong>school gate IN scans</strong>.
        If someone forgets to scan OUT, the system automatically closes their visit at end of day.
    </p>

    {{-- Filter card ─────────────────────────────────────────────── --}}
    <div class="rh-filter-card">
        <div class="rh-section-label"><i class="bi bi-funnel me-1"></i>Filter by date range</div>
        <form method="GET">
            <div class="rh-filter-row">
                <div class="rh-filter-field">
                    <label for="rh-from">From</label>
                    <input type="date" id="rh-from" name="from" value="{{ request('from') }}">
                </div>
                <div class="rh-filter-field">
                    <label for="rh-to">To</label>
                    <input type="date" id="rh-to" name="to" value="{{ request('to') }}">
                </div>
                <div class="rh-filter-actions">
                    <button type="submit" class="btn-search">
                        <i class="bi bi-check2"></i> Apply
                    </button>
                    <a href="{{ route('attendance_logs.reports.hub') }}" class="al-btn al-btn--ghost">
                        <i class="bi bi-x"></i> Clear
                    </a>
                </div>
            </div>
        </form>
        @if(request('from') || request('to'))
            <div class="rh-filter-notice">
                <i class="bi bi-funnel-fill"></i>
                Active filter: <strong>{{ request('from') ?: '…' }}</strong> → <strong>{{ request('to') ?: '…' }}</strong>
            </div>
        @endif
    </div>

    {{-- Primary actions ─────────────────────────────────────────── --}}
    <p class="rh-section-label">Quick access</p>
    <div class="rh-primary-grid">
        <a href="{{ route('attendance_logs.reports.dashboard', request()->only(['from','to'])) }}"
           class="rh-primary-card rh-primary-card--blue">
            <div class="rh-pcard-icon">
                <i class="bi bi-grid-3x3-gap-fill"></i>
            </div>
            <div class="rh-pcard-body">
                <div class="rh-pcard-title">Full dashboard</div>
                <div class="rh-pcard-sub">All 6 reports on one page</div>
            </div>
            <i class="bi bi-arrow-right-circle rh-pcard-arrow"></i>
        </a>

        <a href="{{ route('attendance_logs.reports.export', request()->only(['from','to'])) }}"
           class="rh-primary-card rh-primary-card--green">
            <div class="rh-pcard-icon">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
            </div>
            <div class="rh-pcard-body">
                <div class="rh-pcard-title">CSV export</div>
                <div class="rh-pcard-sub">Download combined data</div>
            </div>
            <i class="bi bi-download rh-pcard-arrow"></i>
        </a>
    </div>

    {{-- Single report grid ──────────────────────────────────────── --}}
    <p class="rh-section-label">Open a single report</p>
    <div class="rh-chip-grid">
        <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'top-ins'])) }}"
           class="rh-chip-card" style="--chip-color:#1250e2;--chip-bg:#eff4ff;">
            <span class="rh-chip-icon"><i class="bi bi-person-fill-up"></i></span>
            Top INs
        </a>
        <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'distinct-days'])) }}"
           class="rh-chip-card" style="--chip-color:#16a34a;--chip-bg:#f0fdf4;">
            <span class="rh-chip-icon"><i class="bi bi-calendar-check-fill"></i></span>
            Distinct IN days
        </a>
        <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'program-totals'])) }}"
           class="rh-chip-card" style="--chip-color:#d97706;--chip-bg:#fffbeb;">
            <span class="rh-chip-icon"><i class="bi bi-bar-chart-fill"></i></span>
            Program totals
        </a>
        <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'weekly'])) }}"
           class="rh-chip-card" style="--chip-color:#7c3aed;--chip-bg:#f5f3ff;">
            <span class="rh-chip-icon"><i class="bi bi-graph-up-arrow"></i></span>
            Weekly trend
        </a>
        <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'monthly'])) }}"
           class="rh-chip-card" style="--chip-color:#dc2626;--chip-bg:#fef2f2;">
            <span class="rh-chip-icon"><i class="bi bi-calendar3"></i></span>
            Monthly trend
        </a>
        <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'busiest-hour'])) }}"
           class="rh-chip-card" style="--chip-color:#0891b2;--chip-bg:#ecfeff;">
            <span class="rh-chip-icon"><i class="bi bi-clock-history"></i></span>
            Busiest hour
        </a>
    </div>

    <hr class="rh-divider">
    <a href="{{ route('attendance_logs.index') }}" class="al-btn al-btn--ghost">
        <i class="bi bi-arrow-left"></i> Back to attendance logs
    </a>

</div>
@endsection
