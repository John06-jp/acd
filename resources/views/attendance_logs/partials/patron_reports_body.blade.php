{{-- Expects: $programNameByCode, $topStudentsByIns, $topStudentsByDistinctInDays, $programAttendanceTotals, $weeklyInsTrend, $monthlyInsTrend, $busiestHours --}}

@php
    $kpiTotalIns   = $programAttendanceTotals->sum('ins_count');
    $kpiTopPatron  = $topStudentsByIns->first();
    $kpiTopProgram = $programAttendanceTotals->first();
    $kpiPeakHour   = $busiestHours->first();
@endphp

{{-- KPI strip --}}
@if(empty($only))
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100" style="border-left:4px solid #29abe2;">
            <div class="card-body py-3">
                <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Total IN scans</div>
                <div class="fw-bold" style="font-size:1.6rem;line-height:1.1;">{{ number_format($kpiTotalIns) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100" style="border-left:4px solid #10b981;">
            <div class="card-body py-3">
                <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Top patron</div>
                @if($kpiTopPatron)
                    <div class="fw-semibold" style="font-size:.9rem;line-height:1.3;">{{ $kpiTopPatron->lastname }}, {{ $kpiTopPatron->firstname }}</div>
                    <span class="badge mt-1" style="background:#10b981;">{{ number_format($kpiTopPatron->ins_count) }} INs</span>
                @else
                    <div class="text-muted">—</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100" style="border-left:4px solid #6366f1;">
            <div class="card-body py-3">
                <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Most active program</div>
                @if($kpiTopProgram && $kpiTopProgram->course)
                    <div class="fw-semibold" style="font-size:.9rem;line-height:1.3;">{{ $programNameByCode->get($kpiTopProgram->course, $kpiTopProgram->course) }}</div>
                    <span class="badge mt-1" style="background:#6366f1;">{{ number_format($kpiTopProgram->ins_count) }} INs</span>
                @else
                    <div class="text-muted">—</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100" style="border-left:4px solid #8b5cf6;">
            <div class="card-body py-3">
                <div class="text-muted mb-1" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Peak hour</div>
                @if($kpiPeakHour && $kpiPeakHour->count > 0)
                    <div class="fw-bold" style="font-size:1rem;">{{ $kpiPeakHour->label }}</div>
                    <span class="badge mt-1" style="background:#8b5cf6;">{{ number_format($kpiPeakHour->count) }} INs</span>
                @else
                    <div class="text-muted">—</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

{{-- Report cards --}}
<div class="row g-3">

    @if(empty($only) || $only === 'top-ins')
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm" style="border-left:4px solid #29abe2;">
            <div class="card-header py-2 d-flex align-items-center gap-2" id="report-top-ins"
                 style="background:#f0f9ff;border-bottom:1px solid #e0f2fe;">
                <span class="rounded-2 d-inline-flex align-items-center justify-content-center flex-shrink-0"
                      style="width:28px;height:28px;background:#dbeafe;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                         stroke="#29abe2" stroke-width="2.2" viewBox="0 0 24 24">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                </span>
                <span class="fw-semibold small">Top 10 — most IN scans</span>
            </div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height:280px;"><canvas id="chartTopIns"></canvas></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead style="background:#e0f2fe;">
                            <tr>
                                <th style="width:2.5rem;">#</th>
                                <th>Patron</th>
                                <th>Program</th>
                                <th class="text-end">INs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topStudentsByIns as $i => $row)
                                <tr>
                                    <td>
                                        @if($i === 0)<span class="badge" style="background:#f59e0b;">1</span>
                                        @elseif($i === 1)<span class="badge" style="background:#9ca3af;">2</span>
                                        @elseif($i === 2)<span class="badge" style="background:#b45309;">3</span>
                                        @else <span class="text-muted">{{ $i + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $row->lastname }}, {{ $row->firstname }}</td>
                                    <td class="small text-muted">{{ $row->course ? $programNameByCode->get($row->course, $row->course) : '—' }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($row->ins_count) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" class="d-block mx-auto mb-1" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        No IN scans yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(empty($only) || $only === 'distinct-days')
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm" style="border-left:4px solid #10b981;">
            <div class="card-header py-2 d-flex align-items-center gap-2" id="report-distinct-days"
                 style="background:#f0fdf4;border-bottom:1px solid #d1fae5;">
                <span class="rounded-2 d-inline-flex align-items-center justify-content-center flex-shrink-0"
                      style="width:28px;height:28px;background:#d1fae5;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                         stroke="#10b981" stroke-width="2.2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </span>
                <span class="fw-semibold small">Top 10 — most distinct days with an IN</span>
            </div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height:280px;"><canvas id="chartDistinctDays"></canvas></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead style="background:#d1fae5;">
                            <tr>
                                <th style="width:2.5rem;">#</th>
                                <th>Patron</th>
                                <th>Program</th>
                                <th class="text-end">Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topStudentsByDistinctInDays as $i => $row)
                                <tr>
                                    <td>
                                        @if($i === 0)<span class="badge" style="background:#f59e0b;">1</span>
                                        @elseif($i === 1)<span class="badge" style="background:#9ca3af;">2</span>
                                        @elseif($i === 2)<span class="badge" style="background:#b45309;">3</span>
                                        @else <span class="text-muted">{{ $i + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $row->lastname }}, {{ $row->firstname }}</td>
                                    <td class="small text-muted">{{ $row->course ? $programNameByCode->get($row->course, $row->course) : '—' }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($row->distinct_in_days) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" class="d-block mx-auto mb-1" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        No IN scans yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(empty($only) || $only === 'program-totals')
    <div class="col-12">
        <div class="card shadow-sm" style="border-left:4px solid #6366f1;">
            <div class="card-header py-2 d-flex align-items-center gap-2" id="report-program-totals"
                 style="background:#f5f3ff;border-bottom:1px solid #ede9fe;">
                <span class="rounded-2 d-inline-flex align-items-center justify-content-center flex-shrink-0"
                      style="width:28px;height:28px;background:#ede9fe;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                         stroke="#6366f1" stroke-width="2.2" viewBox="0 0 24 24">
                        <rect x="2" y="3" width="20" height="14" rx="2"/>
                        <line x1="8" y1="21" x2="16" y2="21"/>
                        <line x1="12" y1="17" x2="12" y2="21"/>
                    </svg>
                </span>
                <span class="fw-semibold small">Totals by program / course (registered patrons &amp; IN scans)</span>
            </div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom" style="overflow-x:auto;">
                    <div id="chartProgramTotalsWrapper" style="height:300px; min-width:700px;">
                        <canvas id="chartProgramTotals"></canvas>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead style="background:#ede9fe;">
                            <tr>
                                <th>Program / course</th>
                                <th class="text-end">Registered</th>
                                <th class="text-end">IN scans</th>
                                <th class="text-end">Avg INs / patron</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programAttendanceTotals as $row)
                                <tr>
                                    <td>{{ $row->course ? $programNameByCode->get($row->course, $row->course) : '—' }}</td>
                                    <td class="text-end">{{ number_format($row->student_count) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($row->ins_count) }}</td>
                                    <td class="text-end">{{ number_format($row->avg_ins_per_student ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">No program/course data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(empty($only) || $only === 'weekly')
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm" style="border-left:4px solid #6366f1;">
            <div class="card-header py-2 d-flex align-items-center gap-2" id="report-weekly"
                 style="background:#eef2ff;border-bottom:1px solid #e0e7ff;">
                <span class="rounded-2 d-inline-flex align-items-center justify-content-center flex-shrink-0"
                      style="width:28px;height:28px;background:#e0e7ff;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                         stroke="#6366f1" stroke-width="2.2" viewBox="0 0 24 24">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                </span>
                <span class="fw-semibold small">IN scans by week (last 12 weeks, Asia/Manila)</span>
            </div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height:280px;"><canvas id="chartWeeklyTrend"></canvas></div>
                </div>
                <div class="table-responsive" style="max-height:260px;">
                    <table class="table table-sm mb-0 align-middle">
                        <thead style="background:#e0e7ff;" class="sticky-top">
                            <tr><th>Week</th><th class="text-end">INs</th></tr>
                        </thead>
                        <tbody>
                            @forelse($weeklyInsTrend as $row)
                                <tr>
                                    <td class="small">{{ $row->label }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($row->count) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center py-4 text-muted">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(empty($only) || $only === 'monthly')
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm" style="border-left:4px solid #ef4444;">
            <div class="card-header py-2 d-flex align-items-center gap-2" id="report-monthly"
                 style="background:#fff1f2;border-bottom:1px solid #fee2e2;">
                <span class="rounded-2 d-inline-flex align-items-center justify-content-center flex-shrink-0"
                      style="width:28px;height:28px;background:#fee2e2;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                         stroke="#ef4444" stroke-width="2.2" viewBox="0 0 24 24">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                </span>
                <span class="fw-semibold small">IN scans by month (last 12 months, Asia/Manila)</span>
            </div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height:280px;"><canvas id="chartMonthlyTrend"></canvas></div>
                </div>
                <div class="table-responsive" style="max-height:260px;">
                    <table class="table table-sm mb-0 align-middle">
                        <thead style="background:#fee2e2;" class="sticky-top">
                            <tr><th>Month</th><th class="text-end">INs</th></tr>
                        </thead>
                        <tbody>
                            @forelse($monthlyInsTrend as $row)
                                <tr>
                                    <td class="small">{{ $row->label }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($row->count) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center py-4 text-muted">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(empty($only) || $only === 'busiest-hour')
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm" style="border-left:4px solid #8b5cf6;">
            <div class="card-header py-2 d-flex align-items-center gap-2" id="report-busiest-hour"
                 style="background:#faf5ff;border-bottom:1px solid #ede9fe;">
                <span class="rounded-2 d-inline-flex align-items-center justify-content-center flex-shrink-0"
                      style="width:28px;height:28px;background:#ede9fe;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                         stroke="#8b5cf6" stroke-width="2.2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </span>
                <span class="fw-semibold small">Busiest hour of day (IN scans, all time)</span>
            </div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height:280px;"><canvas id="chartBusiestHour"></canvas></div>
                </div>
                <div class="table-responsive" style="max-height:260px;">
                    <table class="table table-sm mb-0 align-middle">
                        <thead style="background:#ede9fe;" class="sticky-top">
                            <tr><th>Hour</th><th class="text-end">INs</th></tr>
                        </thead>
                        <tbody>
                            @forelse($busiestHours->take(12) as $row)
                                <tr>
                                    <td>{{ $row->label }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($row->count) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center py-4 text-muted">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
        <script>
        (function () {
            if (!window.Chart) return;
            if (window.ChartDataLabels) Chart.register(ChartDataLabels);

            /* ── Global defaults ─────────────────────────────────────────── */
            Chart.defaults.font.family = "'Inter','Segoe UI',sans-serif";
            Chart.defaults.font.size   = 12;
            Chart.defaults.color       = '#374151';

            /* ── Raw data from Blade ─────────────────────────────────────── */
            const topInsLabels   = @json(collect($topStudentsByIns)->map(fn($r) => trim(($r->lastname ?? '').', '.($r->firstname ?? '')))->values());
            const topInsCounts   = @json(collect($topStudentsByIns)->map(fn($r) => (int)($r->ins_count ?? 0))->values());

            const distinctLabels = @json(collect($topStudentsByDistinctInDays)->map(fn($r) => trim(($r->lastname ?? '').', '.($r->firstname ?? '')))->values());
            const distinctCounts = @json(collect($topStudentsByDistinctInDays)->map(fn($r) => (int)($r->distinct_in_days ?? 0))->values());

            const progLabels     = @json(collect($programAttendanceTotals)->take(12)->map(fn($r) => $r->course ? ($programNameByCode->get($r->course, $r->course)) : '—')->values());
            const progIns        = @json(collect($programAttendanceTotals)->take(12)->map(fn($r) => (int)($r->ins_count ?? 0))->values());

            const weeklyLabels   = @json(collect($weeklyInsTrend)->map(fn($r) => (string)($r->label ?? ''))->values());
            const weeklyCounts   = @json(collect($weeklyInsTrend)->map(fn($r) => (int)($r->count ?? 0))->values());

            const monthlyLabels  = @json(collect($monthlyInsTrend)->map(fn($r) => (string)($r->label ?? ''))->values());
            const monthlyCounts  = @json(collect($monthlyInsTrend)->map(fn($r) => (int)($r->count ?? 0))->values());

            const hourLabels     = @json(collect($busiestHours)->take(12)->map(fn($r) => (string)($r->label ?? ''))->values());
            const hourCounts     = @json(collect($busiestHours)->take(12)->map(fn($r) => (int)($r->count ?? 0))->values());

            /* ── Helpers ─────────────────────────────────────────────────── */
            const gridColor = 'rgba(0,0,0,0.06)';

            function areaGradient(canvasEl, rgbTop, rgbBottom) {
                const ctx = canvasEl.getContext('2d');
                const g   = ctx.createLinearGradient(0, 0, 0, canvasEl.offsetHeight || 280);
                g.addColorStop(0, rgbTop);
                g.addColorStop(1, rgbBottom);
                return g;
            }

            function multiColor(palette, count) {
                return Array.from({length: count}, (_, i) => palette[i % palette.length]);
            }

            function tooltip(isHoriz = false) {
                return {
                    backgroundColor : 'rgba(15,23,42,0.9)',
                    padding         : 10,
                    cornerRadius    : 8,
                    titleFont       : {weight:'700', size:12},
                    bodyFont        : {size:12},
                    callbacks: {
                        label: ctx => '  ' + (isHoriz ? ctx.parsed.x : ctx.parsed.y).toLocaleString()
                    }
                };
            }

            function arrAvg(arr) {
                if (!arr.length) return 0;
                return arr.reduce((a, b) => a + b, 0) / arr.length;
            }

            function avgDataset(counts, color) {
                const a = arrAvg(counts);
                return {
                    type             : 'line',
                    label            : 'Average',
                    data             : counts.map(() => a),
                    borderColor      : color,
                    borderWidth      : 1.5,
                    borderDash       : [6, 4],
                    pointRadius      : 0,
                    fill             : false,
                    tension          : 0,
                    datalabels       : {display: false},
                };
            }

            function datalabels(isHoriz = false, color = '#374151') {
                if (!window.ChartDataLabels) return {};
                return {
                    datalabels: {
                        display    : ctx => ctx.dataset.data.length <= 10,
                        anchor     : isHoriz ? 'end' : 'end',
                        align      : isHoriz ? 'right' : 'top',
                        color,
                        font       : {size: 10, weight: '600'},
                        formatter  : v => v > 0 ? v.toLocaleString() : '',
                        clip       : false,
                    }
                };
            }

            function makeChart(id, config) {
                const el = document.getElementById(id);
                if (!el) return;
                new Chart(el, config);
            }

            /* ── 1. Top INs — horizontal bar ────────────────────────────── */
            makeChart('chartTopIns', {
                type: 'bar',
                data: {
                    labels: topInsLabels,
                    datasets: [{
                        label          : 'IN scans',
                        data           : topInsCounts,
                        backgroundColor: 'rgba(41,171,226,0.75)',
                        borderColor    : '#29abe2',
                        borderWidth    : 1.5,
                        borderRadius   : 6,
                        borderSkipped  : false,
                    }]
                },
                options: {
                    indexAxis          : 'y',
                    responsive         : true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {beginAtZero:true, grid:{color:gridColor}, ticks:{callback: v => v.toLocaleString()}},
                        y: {grid:{display:false}, ticks:{font:{size:11}}}
                    },
                    plugins: {legend:{display:false}, tooltip:tooltip(true), ...datalabels(true, '#1e6fa0')}
                }
            });

            /* ── 2. Distinct IN days — horizontal bar ───────────────────── */
            makeChart('chartDistinctDays', {
                type: 'bar',
                data: {
                    labels: distinctLabels,
                    datasets: [{
                        label          : 'Days with IN',
                        data           : distinctCounts,
                        backgroundColor: 'rgba(16,185,129,0.75)',
                        borderColor    : '#10b981',
                        borderWidth    : 1.5,
                        borderRadius   : 6,
                        borderSkipped  : false,
                    }]
                },
                options: {
                    indexAxis          : 'y',
                    responsive         : true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {beginAtZero:true, grid:{color:gridColor}, ticks:{callback: v => v.toLocaleString()}},
                        y: {grid:{display:false}, ticks:{font:{size:11}}}
                    },
                    plugins: {legend:{display:false}, tooltip:tooltip(true), ...datalabels(true, '#047857')}
                }
            });

            /* ── 3. Program totals — multi-colour vertical bar ──────────── */
            const progWrapper = document.getElementById('chartProgramTotalsWrapper');
            if (progWrapper && progLabels.length > 0) {
                progWrapper.style.minWidth = Math.max(700, progLabels.length * 90) + 'px';
            }
            const progPalette = [
                'rgba(41,171,226,0.8)','rgba(16,185,129,0.8)','rgba(245,158,11,0.8)',
                'rgba(239,68,68,0.8)', 'rgba(99,102,241,0.8)','rgba(236,72,153,0.8)',
                'rgba(20,184,166,0.8)','rgba(249,115,22,0.8)','rgba(139,92,246,0.8)',
                'rgba(6,182,212,0.8)', 'rgba(132,204,22,0.8)','rgba(234,179,8,0.8)',
            ];
            makeChart('chartProgramTotals', {
                type: 'bar',
                data: {
                    labels: progLabels,
                    datasets: [{
                        label          : 'IN scans',
                        data           : progIns,
                        backgroundColor: multiColor(progPalette, progLabels.length),
                        borderColor    : multiColor(progPalette, progLabels.length).map(c => c.replace('0.8)', '1)')),
                        borderWidth    : 1.5,
                        borderRadius   : 6,
                        borderSkipped  : false,
                    }]
                },
                options: {
                    responsive         : true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {grid:{display:false}, ticks:{maxRotation:45, minRotation:30, font:{size:11}}},
                        y: {beginAtZero:true, grid:{color:gridColor}, ticks:{callback: v => v.toLocaleString()}}
                    },
                    plugins: {legend:{display:false}, tooltip:tooltip(false), ...datalabels(false, '#374151')}
                }
            });

            /* ── 4. Weekly trend — smooth area + avg dashed line ────────── */
            const weeklyEl = document.getElementById('chartWeeklyTrend');
            if (weeklyEl) {
                makeChart('chartWeeklyTrend', {
                    type: 'line',
                    data: {
                        labels: weeklyLabels,
                        datasets: [
                            {
                                label               : 'IN scans',
                                data                : weeklyCounts,
                                borderColor         : '#6366f1',
                                backgroundColor     : areaGradient(weeklyEl,'rgba(99,102,241,0.35)','rgba(99,102,241,0.02)'),
                                borderWidth         : 2.5,
                                tension             : 0.4,
                                fill                : true,
                                pointRadius         : 4,
                                pointBackgroundColor: '#6366f1',
                                pointHoverRadius    : 6,
                                datalabels          : {display: false},
                            },
                            avgDataset(weeklyCounts, 'rgba(99,102,241,0.5)'),
                        ]
                    },
                    options: {
                        responsive         : true,
                        maintainAspectRatio: false,
                        interaction        : {mode:'index', intersect:false},
                        scales: {
                            x: {grid:{color:gridColor}, ticks:{maxRotation:40, font:{size:11}}},
                            y: {beginAtZero:true, grid:{color:gridColor}, ticks:{callback: v => v.toLocaleString()}}
                        },
                        plugins: {legend:{display:false}, tooltip:tooltip(false)}
                    }
                });
            }

            /* ── 5. Monthly trend — smooth area + avg dashed line ───────── */
            const monthlyEl = document.getElementById('chartMonthlyTrend');
            if (monthlyEl) {
                makeChart('chartMonthlyTrend', {
                    type: 'line',
                    data: {
                        labels: monthlyLabels,
                        datasets: [
                            {
                                label               : 'IN scans',
                                data                : monthlyCounts,
                                borderColor         : '#ef4444',
                                backgroundColor     : areaGradient(monthlyEl,'rgba(239,68,68,0.3)','rgba(239,68,68,0.02)'),
                                borderWidth         : 2.5,
                                tension             : 0.4,
                                fill                : true,
                                pointRadius         : 4,
                                pointBackgroundColor: '#ef4444',
                                pointHoverRadius    : 6,
                                datalabels          : {display: false},
                            },
                            avgDataset(monthlyCounts, 'rgba(239,68,68,0.5)'),
                        ]
                    },
                    options: {
                        responsive         : true,
                        maintainAspectRatio: false,
                        interaction        : {mode:'index', intersect:false},
                        scales: {
                            x: {grid:{color:gridColor}, ticks:{maxRotation:40, font:{size:11}}},
                            y: {beginAtZero:true, grid:{color:gridColor}, ticks:{callback: v => v.toLocaleString()}}
                        },
                        plugins: {legend:{display:false}, tooltip:tooltip(false)}
                    }
                });
            }

            /* ── 6. Busiest hour — vertical bar, peak highlighted ───────── */
            const hourEl = document.getElementById('chartBusiestHour');
            if (hourEl) {
                const maxVal     = Math.max(...hourCounts);
                const hourColors = hourCounts.map(v => v === maxVal && maxVal > 0 ? 'rgba(139,92,246,1)' : 'rgba(139,92,246,0.5)');
                const hourBorder = hourCounts.map(v => v === maxVal && maxVal > 0 ? '#5b21b6' : '#7c3aed');
                makeChart('chartBusiestHour', {
                    type: 'bar',
                    data: {
                        labels: hourLabels,
                        datasets: [{
                            label          : 'IN scans',
                            data           : hourCounts,
                            backgroundColor: hourColors,
                            borderColor    : hourBorder,
                            borderWidth    : 1.5,
                            borderRadius   : 6,
                            borderSkipped  : false,
                        }]
                    },
                    options: {
                        responsive         : true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {grid:{display:false}, ticks:{font:{size:11}}},
                            y: {beginAtZero:true, grid:{color:gridColor}, ticks:{callback: v => v.toLocaleString()}}
                        },
                        plugins: {legend:{display:false}, tooltip:tooltip(false), ...datalabels(false, '#5b21b6')}
                    }
                });
            }
        })();
        </script>
    @endpush
@endonce
