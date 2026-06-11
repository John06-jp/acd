@extends('layouts.sec')

@section('title', 'Patron Reports')

@section('content')
<div class="attendance-logs-page container mt-2">
    <div class="al-reports-panel">
        <div class="al-reports-panel__header">
            <h4 class="mb-3" style="font-family:'Poppins',sans-serif;font-weight:700;">Patron gate — reports</h4>
            <p class="text-muted small mb-4">
                Summaries are built from <strong>school gate IN scans</strong>. If someone forgets to scan OUT, the system automatically closes their visit at the end of the day.
            </p>
        </div>

        <div class="mb-4 al-reports-filters">
            <form method="GET">
                <div class="al-field" style="flex:0 1 auto;">
                    <label>From</label>
                    <input type="date" name="from" value="{{ request('from') }}">
                </div>
                <div class="al-field" style="flex:0 1 auto;">
                    <label>To</label>
                    <input type="date" name="to" value="{{ request('to') }}">
                </div>
                <div class="al-field" style="flex:0 1 auto;">
                    <label>&nbsp;</label>
                    <div class="al-status-btns">
                        <button type="submit" class="al-btn">Apply</button>
                        <a href="{{ route('attendance_logs.reports.hub') }}" class="al-btn al-btn--ghost">Clear</a>
                    </div>
                </div>
            </form>
            @if(request('from') || request('to'))
                <p class="text-muted small mt-2 mb-0">
                    Filtering all reports to: <strong>{{ request('from') ?: '…' }}</strong> → <strong>{{ request('to') ?: '…' }}</strong>
                </p>
            @endif
        </div>

        <div class="al-reports-links al-report-card-list mb-4">
            <a href="{{ route('attendance_logs.reports.dashboard', request()->only(['from','to'])) }}" class="al-report-card al-report-card--primary">
                <strong>Full dashboard</strong>
                <span>All tables on one page</span>
            </a>
            <a href="{{ route('attendance_logs.reports.export', request()->only(['from','to'])) }}" class="al-report-card al-report-card--secondary">
                <strong>CSV export</strong>
                <span>Download combined data</span>
            </a>
        </div>

        <p class="small text-muted mb-2">Open a single report:</p>
        <div class="al-reports-chips">
            <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'top-ins'])) }}" class="al-report-chip">Top INs</a>
            <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'distinct-days'])) }}" class="al-report-chip">Distinct IN days</a>
            <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'program-totals'])) }}" class="al-report-chip">Program totals</a>
            <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'weekly'])) }}" class="al-report-chip">Weekly trend</a>
            <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'monthly'])) }}" class="al-report-chip">Monthly trend</a>
            <a href="{{ route('attendance_logs.reports.dashboard', array_merge(request()->only(['from','to']), ['only' => 'busiest-hour'])) }}" class="al-report-chip">Busiest hour</a>
        </div>

        <a href="{{ route('attendance_logs.index') }}" class="al-btn al-btn--ghost mt-3">← Back to attendance logs</a>
    </div>
</div>
@endsection
