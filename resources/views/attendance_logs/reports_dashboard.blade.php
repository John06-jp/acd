@extends('layouts.sec')

@section('styles')
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="container py-4">

    <!-- Page header card -->
    <div class="bg-white rounded-4 shadow-sm p-3 p-sm-4 mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:46px;height:46px;background:#eef2ff;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                         stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         viewBox="0 0 24 24">
                        <rect x="2" y="3" width="20" height="14" rx="2"/>
                        <line x1="8" y1="21" x2="16" y2="21"/>
                        <line x1="12" y1="17" x2="12" y2="21"/>
                    </svg>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">Patron gate — report dashboard</h5>
                    <small class="text-muted">School gate IN scans &middot; Asia/Manila</small>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('attendance_logs.reports.hub', request()->only(['from','to'])) }}"
                   class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Reports menu
                </a>
                <a href="{{ route('attendance_logs.reports.export', request()->only(['from','to'])) }}"
                   class="btn btn-sm btn-success d-inline-flex align-items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="8 17 12 21 16 17"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.36"/></svg>
                    Export CSV
                </a>
                <a href="{{ route('attendance_logs.index') }}"
                   class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Attendance logs
                </a>
            </div>
        </div>

        <!-- Info callout -->
        <div class="rounded-3 mt-3 px-3 py-2 d-flex gap-2 align-items-start"
             style="background:#f0f9ff;border:1px solid #bae6fd;font-size:.82rem;color:#0369a1;">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor"
                 stroke-width="2" viewBox="0 0 24 24" class="flex-shrink-0 mt-1">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span>
                "Distinct days with IN" counts at most <strong>one calendar day per patron</strong> per distinct date.
                <strong>Auto&nbsp;OUT:</strong> patrons still IN after their scan day receive an OUT at <strong>end of that day</strong>.
            </span>
        </div>
    </div>

    @if(!empty($only))
        <div class="mb-3">
            <a href="{{ route('attendance_logs.reports.dashboard', request()->only(['from','to'])) }}"
               class="btn btn-outline-primary btn-sm">Open full dashboard</a>
        </div>
    @endif

    @include('attendance_logs.partials.patron_reports_body', ['only' => $only ?? null])
</div>
@endsection
