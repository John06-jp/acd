@extends('layouts.sec')

@section('title', 'Attendance Feedback')

@push('styles')
<style>
.fb-page { font-family: var(--brand-font-family, 'Inter', sans-serif); }

.fb-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.fb-page-header h2 {
    font-size: 1.35rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.02em;
    margin: 0;
}

.fb-page-header .fb-total-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: var(--brand-primary-darker, #125a82);
    color: #fff;
    font-size: 0.82rem;
    font-weight: 600;
    padding: 0.3rem 0.75rem;
    border-radius: 99px;
}

/* Stat cards row */
.fb-stats {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.fb-stat-card {
    display: block;
    text-decoration: none;
    background: #fff;
    border: 2px solid transparent;
    border-radius: 12px;
    padding: 1rem 0.85rem 0.9rem;
    text-align: center;
    box-shadow: 0 1px 4px rgba(17,24,39,0.06);
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    position: relative;
    overflow: hidden;
}

.fb-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(17,24,39,0.1);
}

.fb-stat-card.fb-active {
    border-color: currentColor;
    box-shadow: 0 4px 16px rgba(17,24,39,0.12);
}

.fb-stat-card .fb-stat-icon {
    font-size: 1.6rem;
    line-height: 1;
    margin-bottom: 0.35rem;
    display: block;
}

.fb-stat-card .fb-stat-label {
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 0.2rem;
    display: block;
}

.fb-stat-card .fb-stat-count {
    font-size: 1.75rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.04em;
    display: block;
}

.fb-stat-card .fb-stat-pct {
    font-size: 0.75rem;
    font-weight: 500;
    opacity: 0.7;
    display: block;
    margin-top: 0.15rem;
}

/* Colour variants */
.fb-stat-total   { color: var(--brand-primary-darker, #125a82); }
.fb-stat-excellent { color: #16a34a; }
.fb-stat-good    { color: #2563eb; }
.fb-stat-medium  { color: #d97706; }
.fb-stat-poor    { color: #dc2626; }
.fb-stat-verybad { color: #7c3aed; }
.fb-stat-declined{ color: #6b7280; }

/* Distribution bar */
.fb-dist-card {
    background: #fff;
    border-radius: 12px;
    padding: 1rem 1.1rem;
    box-shadow: 0 1px 4px rgba(17,24,39,0.06);
    margin-bottom: 1.5rem;
}

.fb-dist-card h6 {
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #9ca3af;
    margin-bottom: 0.75rem;
}

.fb-dist-bar {
    display: flex;
    height: 10px;
    border-radius: 99px;
    overflow: hidden;
    gap: 2px;
    margin-bottom: 0.65rem;
}

.fb-dist-bar-seg {
    border-radius: 99px;
    transition: flex 0.4s ease;
    min-width: 0;
}

.fb-dist-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem 1rem;
}

.fb-dist-legend-item {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.78rem;
    color: #374151;
}

.fb-dist-legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

/* Table */
.fb-table-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 4px rgba(17,24,39,0.06);
    overflow: hidden;
}

.fb-table-card .fb-table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 0.85rem 1.1rem;
    border-bottom: 1px solid rgba(17,24,39,0.07);
}

.fb-table-card .fb-table-header h6 {
    font-size: 0.9rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
    letter-spacing: -0.01em;
}

.fb-table-card .table {
    margin: 0;
    font-size: 0.875rem;
}

.fb-table-card .table thead th {
    background: var(--brand-primary, #29abe2);
    color: #fff;
    font-weight: 600;
    font-size: 0.8rem;
    letter-spacing: 0.02em;
    padding: 0.65rem 0.85rem;
    border-color: rgba(255,255,255,0.15);
    vertical-align: middle;
}

.fb-table-card .table tbody td {
    padding: 0.6rem 0.85rem;
    vertical-align: middle;
    border-color: rgba(17,24,39,0.06);
    color: #374151;
}

.fb-table-card .table tbody tr:hover td {
    background: rgba(41, 171, 226, 0.04);
}

.fb-rating-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.25em 0.6em;
    border-radius: 99px;
}

.fb-rating-badge.excellent { background: #dcfce7; color: #16a34a; }
.fb-rating-badge.good      { background: #dbeafe; color: #2563eb; }
.fb-rating-badge.medium    { background: #fef3c7; color: #d97706; }
.fb-rating-badge.poor      { background: #fee2e2; color: #dc2626; }
.fb-rating-badge.very_bad  { background: #ede9fe; color: #7c3aed; }
.fb-rating-badge.declined  { background: #f3f4f6; color: #6b7280; }

@media (max-width: 575.98px) {
    .fb-stats { grid-template-columns: repeat(3, 1fr); }
    .fb-stat-card .fb-stat-count { font-size: 1.4rem; }
}
</style>
@endpush

@section('content')
@php
    $active = request('rating');
    $ratingMeta = [
        'total'    => ['icon' => '📊', 'label' => 'Total',     'class' => 'fb-stat-total',    'color' => '#125a82', 'count' => $total],
        'excellent'=> ['icon' => '😊', 'label' => 'Excellent', 'class' => 'fb-stat-excellent', 'color' => '#16a34a', 'count' => $excellent],
        'good'     => ['icon' => '🙂', 'label' => 'Good',      'class' => 'fb-stat-good',     'color' => '#2563eb', 'count' => $good],
        'medium'   => ['icon' => '😐', 'label' => 'Medium',    'class' => 'fb-stat-medium',   'color' => '#d97706', 'count' => $medium],
        'poor'     => ['icon' => '😕', 'label' => 'Poor',      'class' => 'fb-stat-poor',     'color' => '#dc2626', 'count' => $poor],
        'very_bad' => ['icon' => '😠', 'label' => 'Very Bad',  'class' => 'fb-stat-verybad',  'color' => '#7c3aed', 'count' => $veryBad],
        'declined' => ['icon' => '🚫', 'label' => 'Declined',  'class' => 'fb-stat-declined', 'color' => '#6b7280', 'count' => $declined],
    ];
    $ratingIcons = ['excellent'=>'😊','good'=>'🙂','medium'=>'😐','poor'=>'😕','very_bad'=>'😠','declined'=>'🚫'];
@endphp

<div class="fb-page">

    {{-- Page header --}}
    <div class="fb-page-header">
        <h2>Attendance Feedback</h2>
        <span class="fb-total-chip">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            {{ $total }} {{ Str::plural('response', $total) }}
        </span>
    </div>

    {{-- Stat cards --}}
    <div class="fb-stats">
        {{-- Total (reset filter) --}}
        <a href="{{ route('feedback.index') }}" class="fb-stat-card fb-stat-total {{ !$active ? 'fb-active' : '' }}">
            <span class="fb-stat-icon">📊</span>
            <span class="fb-stat-label">Total</span>
            <span class="fb-stat-count">{{ $total }}</span>
            <span class="fb-stat-pct">All responses</span>
        </a>

        @foreach(array_slice($ratingMeta, 1) as $key => $meta)
            @php $pct = $total > 0 ? round(($meta['count'] / $total) * 100) : 0; @endphp
            <a href="{{ route('feedback.index', ['rating' => $key]) }}"
               class="fb-stat-card {{ $meta['class'] }} {{ $active == $key ? 'fb-active' : '' }}">
                <span class="fb-stat-icon">{{ $meta['icon'] }}</span>
                <span class="fb-stat-label">{{ $meta['label'] }}</span>
                <span class="fb-stat-count">{{ $meta['count'] }}</span>
                <span class="fb-stat-pct">{{ $pct }}% of total</span>
            </a>
        @endforeach
    </div>

    {{-- Distribution bar --}}
    @if($total > 0)
    <div class="fb-dist-card">
        <h6>Overall Distribution</h6>
        <div class="fb-dist-bar">
            @foreach([
                ['count' => $excellent, 'color' => '#16a34a'],
                ['count' => $good,      'color' => '#2563eb'],
                ['count' => $medium,    'color' => '#d97706'],
                ['count' => $poor,      'color' => '#dc2626'],
                ['count' => $veryBad,   'color' => '#7c3aed'],
                ['count' => $declined,  'color' => '#6b7280'],
            ] as $seg)
                @if($seg['count'] > 0)
                <div class="fb-dist-bar-seg"
                     style="flex: {{ $seg['count'] }}; background: {{ $seg['color'] }};"
                     title="{{ round(($seg['count']/$total)*100) }}%"></div>
                @endif
            @endforeach
        </div>
        <div class="fb-dist-legend">
            @foreach([
                ['label'=>'Excellent','color'=>'#16a34a','count'=>$excellent],
                ['label'=>'Good',     'color'=>'#2563eb','count'=>$good],
                ['label'=>'Medium',   'color'=>'#d97706','count'=>$medium],
                ['label'=>'Poor',     'color'=>'#dc2626','count'=>$poor],
                ['label'=>'Very Bad', 'color'=>'#7c3aed','count'=>$veryBad],
                ['label'=>'Declined', 'color'=>'#6b7280','count'=>$declined],
            ] as $leg)
                @if($leg['count'] > 0)
                <span class="fb-dist-legend-item">
                    <span class="fb-dist-legend-dot" style="background: {{ $leg['color'] }};"></span>
                    {{ $leg['label'] }} — {{ $leg['count'] }} ({{ round(($leg['count']/$total)*100) }}%)
                </span>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Responses table --}}
    <div class="fb-table-card">
        <div class="fb-table-header">
            <h6>
                @if($active)
                    {{ $ratingIcons[$active] ?? '' }}
                    {{ ucwords(str_replace('_', ' ', $active)) }} Responses
                @else
                    All Responses
                @endif
            </h6>
            @if($active)
                <a href="{{ route('feedback.index') }}" class="btn btn-outline-secondary btn-sm" style="font-size:0.78rem; padding: 0.25rem 0.6rem;">
                    ✕ Clear filter
                </a>
            @endif
        </div>

        <div class="table-responsive site-table-wrap">
            <table class="table table-hover align-middle mb-0 site-table">
                <thead>
                    <tr>
                        <th style="width:3rem">#</th>
                        <th>Student</th>
                        <th>Rating</th>
                        <th>Declined</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbacks as $index => $feedback)
                        <tr>
                            <td class="text-muted" style="font-size:0.8rem">{{ $index + 1 }}</td>
                            <td>
                                <span style="font-weight:600; color:#111827">
                                    {{ optional($feedback->student)->lastname ?? '—' }}{{ optional($feedback->student)->lastname && optional($feedback->student)->firstname ? ', ' : '' }}{{ optional($feedback->student)->firstname ?? '' }}
                                </span>
                            </td>
                            <td>
                                @if($feedback->rating)
                                    <span class="fb-rating-badge {{ $feedback->rating }}">
                                        {{ $ratingIcons[$feedback->rating] ?? '' }}
                                        {{ ucwords(str_replace('_', ' ', $feedback->rating)) }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($feedback->declined)
                                    <span style="color:#dc2626; font-weight:600; font-size:0.82rem">Yes</span>
                                @else
                                    <span style="color:#6b7280; font-size:0.82rem">No</span>
                                @endif
                            </td>
                            <td style="font-size:0.82rem; color:#6b7280; white-space:nowrap">
                                {{ $feedback->created_at?->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div style="font-size:2rem; margin-bottom:0.5rem">💬</div>
                                No feedback found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
