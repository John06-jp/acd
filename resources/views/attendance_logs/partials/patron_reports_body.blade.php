{{-- Expects: $programNameByCode, $topStudentsByIns, $topStudentsByDistinctInDays, $programAttendanceTotals, $weeklyInsTrend, $monthlyInsTrend, $busiestHours --}}
<div class="row g-3">
    @if(empty($only) || $only === 'top-ins')
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm">
            <div class="card-header py-2 fw-semibold small" id="report-top-ins">Top 10 — most IN scans</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartTopIns"></canvas>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Patron</th>
                                <th>Program / course</th>
                                <th class="text-end">INs</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topStudentsByIns as $i => $row)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="text-start">{{ $row->lastname }}, {{ $row->firstname }}</td>
                                    <td class="text-start small">{{ $row->course ? $programNameByCode->get($row->course, $row->course) : '—' }}</td>
                                    <td class="text-end">{{ number_format($row->ins_count) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center py-3">No IN scans yet.</td></tr>
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
        <div class="card h-100 shadow-sm">
            <div class="card-header py-2 fw-semibold small" id="report-distinct-days">Top 10 — most distinct days with an IN</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartDistinctDays"></canvas>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Patron</th>
                                <th>Program / course</th>
                                <th class="text-end">Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topStudentsByDistinctInDays as $i => $row)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="text-start">{{ $row->lastname }}, {{ $row->firstname }}</td>
                                    <td class="text-start small">{{ $row->course ? $programNameByCode->get($row->course, $row->course) : '—' }}</td>
                                    <td class="text-end">{{ number_format($row->distinct_in_days) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center py-3">No IN scans yet.</td></tr>
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
        <div class="card shadow-sm">
            <div class="card-header py-2 fw-semibold small" id="report-program-totals">Totals by program / course (registered patrons &amp; IN scans)</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 280px;">
                        <canvas id="chartProgramTotals"></canvas>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Program / course</th>
                                <th class="text-end">Registered patrons</th>
                                <th class="text-end">IN scans (all time)</th>
                                <th class="text-end">Avg INs / patron</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programAttendanceTotals as $row)
                                <tr>
                                    <td class="text-start">{{ $row->course ? $programNameByCode->get($row->course, $row->course) : '—' }}</td>
                                    <td class="text-end">{{ number_format($row->student_count) }}</td>
                                    <td class="text-end">{{ number_format($row->ins_count) }}</td>
                                    <td class="text-end">{{ number_format($row->avg_ins_per_student ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center py-3">No program/course data yet.</td></tr>
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
        <div class="card h-100 shadow-sm">
            <div class="card-header py-2 fw-semibold small" id="report-weekly">IN scans by week (last 12 weeks, Asia/Manila)</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartWeeklyTrend"></canvas>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 280px;">
                    <table class="table table-sm table-striped mb-0 align-middle">
                        <thead class="table-light sticky-top"><tr><th>Week</th><th class="text-end">INs</th></tr></thead>
                        <tbody>
                            @forelse($weeklyInsTrend as $row)
                                <tr><td class="text-start small">{{ $row->label }}</td><td class="text-end">{{ number_format($row->count) }}</td></tr>
                            @empty
                                <tr><td colspan="2" class="text-muted text-center py-3">No data.</td></tr>
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
        <div class="card h-100 shadow-sm">
            <div class="card-header py-2 fw-semibold small" id="report-monthly">IN scans by month (last 12 months, Asia/Manila)</div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartMonthlyTrend"></canvas>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 280px;">
                    <table class="table table-sm table-striped mb-0 align-middle">
                        <thead class="table-light sticky-top"><tr><th>Month</th><th class="text-end">INs</th></tr></thead>
                        <tbody>
                            @forelse($monthlyInsTrend as $row)
                                <tr><td class="text-start small">{{ $row->label }}</td><td class="text-end">{{ number_format($row->count) }}</td></tr>
                            @empty
                                <tr><td colspan="2" class="text-muted text-center py-3">No data.</td></tr>
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
        <div class="card h-100 shadow-sm">
            <div class="card-header py-2 fw-semibold small" id="report-busiest-hour">Busiest hour (IN scans, all time)</div>
            <div class="card-body p-0">
                <p class="text-muted small px-3 pt-2 mb-0">Uses <code>HOUR(scanned_at)</code> as stored in the database (app timezone {{ config('app.timezone') }}).</p>
                <div class="p-3 border-bottom">
                    <div style="height: 240px;">
                        <canvas id="chartBusiestHour"></canvas>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 260px;">
                    <table class="table table-sm table-striped mb-0 align-middle">
                        <thead class="table-light sticky-top"><tr><th>Hour</th><th class="text-end">INs</th></tr></thead>
                        <tbody>
                            @forelse($busiestHours->take(12) as $row)
                                <tr><td class="text-start">{{ $row->label }}</td><td class="text-end">{{ number_format($row->count) }}</td></tr>
                            @empty
                                <tr><td colspan="2" class="text-muted text-center py-3">No data.</td></tr>
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
        <script>
        (function () {
            if (!window.Chart) return;

            /* ── Global defaults ──────────────────────────────────────── */
            Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
            Chart.defaults.font.size   = 12;
            Chart.defaults.color       = '#374151';

            /* ── Raw data from Blade ──────────────────────────────────── */
            const topInsLabels    = @json(collect($topStudentsByIns)->map(fn($r) => trim(($r->lastname ?? '').', '.($r->firstname ?? '')))->values());
            const topInsCounts    = @json(collect($topStudentsByIns)->map(fn($r) => (int) ($r->ins_count ?? 0))->values());

            const distinctLabels  = @json(collect($topStudentsByDistinctInDays)->map(fn($r) => trim(($r->lastname ?? '').', '.($r->firstname ?? '')))->values());
            const distinctCounts  = @json(collect($topStudentsByDistinctInDays)->map(fn($r) => (int) ($r->distinct_in_days ?? 0))->values());

            const progLabels      = @json(collect($programAttendanceTotals)->take(12)->map(fn($r) => $r->course ? ($programNameByCode->get($r->course, $r->course)) : '—')->values());
            const progIns         = @json(collect($programAttendanceTotals)->take(12)->map(fn($r) => (int) ($r->ins_count ?? 0))->values());

            const weeklyLabels    = @json(collect($weeklyInsTrend)->map(fn($r) => (string) ($r->label ?? ''))->values());
            const weeklyCounts    = @json(collect($weeklyInsTrend)->map(fn($r) => (int) ($r->count ?? 0))->values());

            const monthlyLabels   = @json(collect($monthlyInsTrend)->map(fn($r) => (string) ($r->label ?? ''))->values());
            const monthlyCounts   = @json(collect($monthlyInsTrend)->map(fn($r) => (int) ($r->count ?? 0))->values());

            const hourLabels      = @json(collect($busiestHours)->take(12)->map(fn($r) => (string) ($r->label ?? ''))->values());
            const hourCounts      = @json(collect($busiestHours)->take(12)->map(fn($r) => (int) ($r->count ?? 0))->values());

            /* ── Helpers ──────────────────────────────────────────────── */
            const gridColor = 'rgba(0,0,0,0.06)';

            function areaGradient(canvasEl, rgbTop, rgbBottom) {
                const ctx = canvasEl.getContext('2d');
                const g   = ctx.createLinearGradient(0, 0, 0, canvasEl.offsetHeight || 240);
                g.addColorStop(0, rgbTop);
                g.addColorStop(1, rgbBottom);
                return g;
            }

            function multiColor(palette, count) {
                return Array.from({ length: count }, (_, i) => palette[i % palette.length]);
            }

            function tooltip(isHoriz = false) {
                return {
                    backgroundColor : 'rgba(15,23,42,0.9)',
                    padding         : 10,
                    cornerRadius    : 8,
                    titleFont       : { weight: '700', size: 12 },
                    bodyFont        : { size: 12 },
                    callbacks: {
                        label: ctx => '  ' + (isHoriz ? ctx.parsed.x : ctx.parsed.y).toLocaleString()
                    }
                };
            }

            function makeChart(id, config) {
                const el = document.getElementById(id);
                if (!el) return;
                new Chart(el, config);
            }

            /* ── 1. Top INs — horizontal bar ──────────────────────────── */
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
                    indexAxis        : 'y',
                    responsive       : true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true, grid: { color: gridColor }, ticks: { callback: v => v.toLocaleString() } },
                        y: { grid: { display: false }, ticks: { font: { size: 11 } } }
                    },
                    plugins: { legend: { display: false }, tooltip: tooltip(true) }
                }
            });

            /* ── 2. Distinct IN days — horizontal bar ─────────────────── */
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
                    indexAxis        : 'y',
                    responsive       : true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true, grid: { color: gridColor }, ticks: { callback: v => v.toLocaleString() } },
                        y: { grid: { display: false }, ticks: { font: { size: 11 } } }
                    },
                    plugins: { legend: { display: false }, tooltip: tooltip(true) }
                }
            });

            /* ── 3. Program totals — multi-colour vertical bar ────────── */
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
                    responsive       : true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { grid: { display: false }, ticks: { maxRotation: 40, font: { size: 11 } } },
                        y: { beginAtZero: true, grid: { color: gridColor }, ticks: { callback: v => v.toLocaleString() } }
                    },
                    plugins: { legend: { display: false }, tooltip: tooltip(false) }
                }
            });

            /* ── 4. Weekly trend — smooth area line ───────────────────── */
            const weeklyEl = document.getElementById('chartWeeklyTrend');
            if (weeklyEl) {
                makeChart('chartWeeklyTrend', {
                    type: 'line',
                    data: {
                        labels: weeklyLabels,
                        datasets: [{
                            label          : 'IN scans',
                            data           : weeklyCounts,
                            borderColor    : '#6366f1',
                            backgroundColor: areaGradient(weeklyEl, 'rgba(99,102,241,0.35)', 'rgba(99,102,241,0.02)'),
                            borderWidth    : 2.5,
                            tension        : 0.4,
                            fill           : true,
                            pointRadius    : 4,
                            pointBackgroundColor: '#6366f1',
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive       : true,
                        maintainAspectRatio: false,
                        interaction      : { mode: 'index', intersect: false },
                        scales: {
                            x: { grid: { color: gridColor }, ticks: { maxRotation: 40, font: { size: 11 } } },
                            y: { beginAtZero: true, grid: { color: gridColor }, ticks: { callback: v => v.toLocaleString() } }
                        },
                        plugins: { legend: { display: false }, tooltip: tooltip(false) }
                    }
                });
            }

            /* ── 5. Monthly trend — smooth area line ──────────────────── */
            const monthlyEl = document.getElementById('chartMonthlyTrend');
            if (monthlyEl) {
                makeChart('chartMonthlyTrend', {
                    type: 'line',
                    data: {
                        labels: monthlyLabels,
                        datasets: [{
                            label          : 'IN scans',
                            data           : monthlyCounts,
                            borderColor    : '#ef4444',
                            backgroundColor: areaGradient(monthlyEl, 'rgba(239,68,68,0.3)', 'rgba(239,68,68,0.02)'),
                            borderWidth    : 2.5,
                            tension        : 0.4,
                            fill           : true,
                            pointRadius    : 4,
                            pointBackgroundColor: '#ef4444',
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive       : true,
                        maintainAspectRatio: false,
                        interaction      : { mode: 'index', intersect: false },
                        scales: {
                            x: { grid: { color: gridColor }, ticks: { maxRotation: 40, font: { size: 11 } } },
                            y: { beginAtZero: true, grid: { color: gridColor }, ticks: { callback: v => v.toLocaleString() } }
                        },
                        plugins: { legend: { display: false }, tooltip: tooltip(false) }
                    }
                });
            }

            /* ── 6. Busiest hour — vertical bar with purple gradient ──── */
            const hourEl = document.getElementById('chartBusiestHour');
            if (hourEl) {
                const hourGrad = areaGradient(hourEl, 'rgba(139,92,246,0.85)', 'rgba(192,132,252,0.55)');
                makeChart('chartBusiestHour', {
                    type: 'bar',
                    data: {
                        labels: hourLabels,
                        datasets: [{
                            label          : 'IN scans',
                            data           : hourCounts,
                            backgroundColor: hourGrad,
                            borderColor    : '#7c3aed',
                            borderWidth    : 1.5,
                            borderRadius   : 6,
                            borderSkipped  : false,
                        }]
                    },
                    options: {
                        responsive       : true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                            y: { beginAtZero: true, grid: { color: gridColor }, ticks: { callback: v => v.toLocaleString() } }
                        },
                        plugins: { legend: { display: false }, tooltip: tooltip(false) }
                    }
                });
            }
        })();
        </script>
    @endpush
@endonce
