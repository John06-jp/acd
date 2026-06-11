@extends('layouts.sec')

@push('page-styles')
<link rel="stylesheet" href="{{ asset('css/sms-blast.css') }}">
@endpush

@section('content')
<div class="sms-blast-page">

    <h2 class="sms-blast-title">
        <i class="bi bi-send-fill"></i> SMS blast
    </h2>

    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('sms.send') }}" id="smsBlastForm">
        @csrf

        {{-- Filters --}}
        <div class="sms-filters">
            <div class="sms-field">
                <label>Filter by year</label>
                <select name="year" id="yearFilter">
                    <option value="">All years</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>

                
            </div>
            <div class="sms-field">
                <label>Filter by course</label>
                <select name="course" id="courseFilter">
                    <option value="">All courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course }}">{{ $course }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Recipients bar --}}
        <div class="sms-recipients">
            <div class="sms-recipients-left">
                <i class="bi bi-people"></i>
                <span class="sms-recipient-label">Recipients</span>
                <span class="sms-recipient-count" id="recipientCount">…</span>
                <span style="color:#9ca3af; font-size:0.85rem;">students</span>
            </div>
            <span class="sms-groups-badge" id="groupsBadge">All groups</span>
        </div>

<<<<<<< HEAD

        {{-- MESSAGE --}}
        <div class="mb-3">

            <label>Message</label>

            <textarea 
                name="message" 
                class="form-control" 
                rows="5"
                placeholder="Example: Hello (name), please visit the library today."
                required
            ></textarea>

            <small class="text-muted">
                Available variables:
                <br><b>(name)</b> = Student full name
            </small>

=======
        {{-- Message --}}
        <div class="mb-2">
            <div class="sms-message-header">
                <label for="smsMessage">Message</label>
                <span class="sms-char-counter" id="charCounter">0 / 160</span>
            </div>
            <textarea
                id="smsMessage"
                name="message"
                class="sms-textarea"
                rows="4"
                placeholder="Hello {name}, please visit the library today."
                maxlength="160"
                required
            ></textarea>

            {{-- Variables --}}
            <div class="sms-variables">
                <span class="sms-variables-label">Variables</span>
                <span class="sms-var-chip" data-var="{name}">{name}</span>
                <span class="sms-var-tap-hint">— tap to insert</span>
            </div>
>>>>>>> 5303854c859ee39cdf626b3fe5dfdf35811108ae
        </div>

        {{-- Actions --}}
        <div class="sms-actions">
            <button type="submit" class="sms-btn">
                <i class="bi bi-send"></i> Send SMS
            </button>
            <button type="button" class="sms-btn" id="previewBtn">
                <i class="bi bi-eye"></i> Preview
            </button>
        </div>

    </form>

    {{-- Preview modal --}}
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px; border:1.5px solid #e5e7eb;">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold" id="previewModalLabel">Message preview</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-2">
                    <div style="background:#f3f4f6; border-radius:10px; padding:1rem; font-size:0.9rem; color:#111; min-height:60px; white-space:pre-wrap;" id="previewText"></div>
                    <p class="text-muted mt-2 mb-0" style="font-size:0.78rem;">
                        <i class="bi bi-info-circle"></i> <b>{name}</b> will be replaced with each student's full name when sent.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function updateRecipientCount() {
    const year   = document.getElementById('yearFilter').value;
    const course = document.getElementById('courseFilter').value;

    fetch("{{ route('sms.count') }}?year=" + year + "&course=" + course)
        .then(res => res.json())
        .then(data => {
            document.getElementById('recipientCount').innerText = data.count;

            const badge = document.getElementById('groupsBadge');
            if (year && course)      badge.textContent = year + '♦' + course;
            else if (year)           badge.textContent = 'Year ' + year;
            else if (course)         badge.textContent = course;
            else                     badge.textContent = 'All groups';
        });
}

document.getElementById('yearFilter').addEventListener('change', updateRecipientCount);
document.getElementById('courseFilter').addEventListener('change', updateRecipientCount);
window.addEventListener('load', updateRecipientCount);

// Character counter
const textarea = document.getElementById('smsMessage');
const counter  = document.getElementById('charCounter');
textarea.addEventListener('input', () => {
    const len = textarea.value.length;
    counter.textContent = len + ' / 160';
    counter.className = 'sms-char-counter' +
        (len >= 160 ? ' at-limit' : len >= 130 ? ' near-limit' : '');
});

// Variable chip insert
document.querySelectorAll('.sms-var-chip').forEach(chip => {
    chip.addEventListener('click', () => {
        const v     = chip.dataset.var;
        const start = textarea.selectionStart;
        const end   = textarea.selectionEnd;
        textarea.value = textarea.value.slice(0, start) + v + textarea.value.slice(end);
        textarea.selectionStart = textarea.selectionEnd = start + v.length;
        textarea.focus();
        textarea.dispatchEvent(new Event('input'));
    });
});

// Preview modal
document.getElementById('previewBtn').addEventListener('click', () => {
    document.getElementById('previewText').textContent =
        textarea.value || '(no message yet)';
    new bootstrap.Modal(document.getElementById('previewModal')).show();
});
</script>
@endsection
