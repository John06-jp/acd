@extends('layouts.sec')

@push('page-styles')
<link rel="stylesheet" href="{{ asset('css/sms-blast.css') }}">
@endpush

@section('content')
<div class="scan-msg-page">

    {{-- Header --}}
    <div class="scan-msg-header">
        <h2 class="scan-msg-title">
            <i class="bi bi-qr-code"></i> Scan SMS message
        </h2>
        <a href="{{ route('sms.page') }}" class="sms-btn">
            <i class="bi bi-arrow-left"></i> Back to SMS blast
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('sms.scanMessage.update') }}" id="scanMsgForm">
        @csrf

        {{-- Textarea --}}
        <div>
            <div class="sms-message-header">
                <label for="scanMsgTextarea" style="font-size:0.9rem;font-weight:600;color:#111;font-family:'Segoe UI',sans-serif;">Message template</label>
                <span class="sms-char-counter" id="charCounter">0 / 160</span>
            </div>
            <textarea
                id="scanMsgTextarea"
                name="message"
                class="sms-textarea"
                rows="4"
                maxlength="160"
                required
            >{{ $message }}</textarea>
        </div>

        {{-- Available tags --}}
        <div class="sms-tags-card">
            <div class="sms-tags-heading">Available tags — tap to insert</div>
            <div class="sms-tag-row">
                <span class="sms-var-chip" data-var="{name}">{name}</span>
                <span>Student full name</span>
            </div>
            <div class="sms-tag-row">
                <span class="sms-var-chip" data-var="{status}">{status}</span>
                <span>Scan direction — <b>IN</b> or <b>OUT</b></span>
            </div>
            <div class="sms-tag-row">
                <span class="sms-var-chip" data-var="{time}">{time}</span>
                <span>Scan time (e.g. 10:42 AM)</span>
            </div>
        </div>

        {{-- Live preview --}}
        <div class="sms-preview-card">
            <div class="sms-preview-heading">
                <i class="bi bi-eye"></i> Live preview
            </div>
            <div class="sms-preview-toggles">
                <button type="button" class="sms-preview-toggle active" data-status="IN">Scan IN</button>
                <button type="button" class="sms-preview-toggle" data-status="OUT">Scan OUT</button>
            </div>
            <div class="sms-preview-bubble" id="previewBubble"></div>
        </div>

        {{-- Actions --}}
        <div class="sms-actions">
            <button type="submit" class="sms-btn">
                <i class="bi bi-floppy"></i> Save template
            </button>
            <button type="button" class="sms-btn" id="resetBtn">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>

    </form>

</div>

<script>
const textarea    = document.getElementById('scanMsgTextarea');
const counter     = document.getElementById('charCounter');
const preview     = document.getElementById('previewBubble');
const toggles     = document.querySelectorAll('.sms-preview-toggle');
const defaultMsg  = {{ Js::from($message) }};

let previewStatus = 'IN';

function updateCounter() {
    const len = textarea.value.length;
    counter.textContent = len + ' / 160';
    counter.className = 'sms-char-counter' +
        (len >= 160 ? ' at-limit' : len >= 130 ? ' near-limit' : '');
}

function updatePreview() {
    const now = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    preview.textContent = textarea.value
        .replace(/\{name\}/g,   'Maria Santos')
        .replace(/\{status\}/g, previewStatus)
        .replace(/\{time\}/g,   time);
}

textarea.addEventListener('input', () => { updateCounter(); updatePreview(); });

toggles.forEach(btn => {
    btn.addEventListener('click', () => {
        toggles.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        previewStatus = btn.dataset.status;
        updatePreview();
    });
});

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

document.getElementById('resetBtn').addEventListener('click', () => {
    textarea.value = defaultMsg;
    textarea.dispatchEvent(new Event('input'));
});

// Init
updateCounter();
updatePreview();
</script>
@endsection
