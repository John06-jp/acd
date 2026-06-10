@php
    $index = $index ?? null;
    $absentDates = $absentDates ?? [];
    $tardyDates = $tardyDates ?? [];
    if (is_string($absentDates)) {
        $absentDates = array_filter(preg_split('/[\s,;]+/', $absentDates) ?: []);
    }
    if (is_string($tardyDates)) {
        $tardyDates = array_filter(preg_split('/[\s,;]+/', $tardyDates) ?: []);
    }
    $namePrefix = $index !== null ? "students[{$index}]" : null;
@endphp
<div class="col-12">
    <label class="form-label small mb-1">Attendance — click school days (<span class="sf2-cal-month-label text-muted">set month above</span>)</label>
    <div class="sf2-attendance-cal border rounded p-2 bg-light"
         data-absent-initial="{{ json_encode(array_values($absentDates)) }}"
         data-tardy-initial="{{ json_encode(array_values($tardyDates)) }}">
        <div class="sf2-cal-toolbar">
            <span class="small text-muted">Mode:</span>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-danger sf2-cal-mode active" data-mode="absent">Absent</button>
                <button type="button" class="btn btn-outline-warning sf2-cal-mode" data-mode="tardy">Tardy</button>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary sf2-cal-clear">Clear days</button>
        </div>
        <div class="sf2-cal-grid"></div>
        <div class="sf2-cal-legend">
            <span><i class="sf2-cal-swatch present"></i> Present (not clicked)</span>
            <span><i class="sf2-cal-swatch absent"></i> Absent</span>
            <span><i class="sf2-cal-swatch tardy"></i> Tardy</span>
            <span class="text-muted">Weekends are not school days</span>
        </div>
        @if($namePrefix)
            <input type="hidden" name="{{ $namePrefix }}[absent_dates]" class="sf2-absent-input" value="">
            <input type="hidden" name="{{ $namePrefix }}[tardy_dates]" class="sf2-tardy-input" value="">
        @else
            <input type="hidden" class="sf2-absent-input" data-field="absent_dates" value="">
            <input type="hidden" class="sf2-tardy-input" data-field="tardy_dates" value="">
        @endif
    </div>
</div>
