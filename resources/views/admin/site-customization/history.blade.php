@extends('layouts.app')

@section('title', 'Customization Change History')

@push('styles')
<style>
    .history-shell { max-width:1200px; margin:0 auto; }
    .history-panel { background:#fff; border:1px solid #e5e7eb; border-radius:14px; box-shadow:0 2px 12px rgba(15,23,42,.05); padding:1.25rem; }
    .history-row { padding:1rem 0; border-top:1px solid #edf2f7; }
    .history-summary { display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    .history-details { margin-top:.75rem; border:1px solid #e5e7eb; border-radius:10px; overflow-x:auto; }
    .history-details table { width:100%; margin:0; font-size:.85rem; }
    .history-details th, .history-details td { padding:.65rem .75rem; border-bottom:1px solid #edf2f7; vertical-align:top; }
    .history-details tr:last-child td { border-bottom:0; }
    .history-value { display:block; max-width:380px; max-height:7rem; overflow:auto; white-space:pre-wrap; overflow-wrap:anywhere; }
    .history-default { color:#64748b; font-style:italic; }
    @media (max-width:767px) { .history-summary { align-items:flex-start; flex-direction:column; } }
</style>
@endpush

@section('content')
@php
    $historyValue = static function (?string $value, string $key) use ($definitions): array {
        if ($value === null) return ['Factory default', true];
        $type = $definitions[$key]['type'] ?? 'string';
        if ($type === 'boolean') return [$value === '1' ? 'Enabled' : 'Disabled', false];
        if ($type === 'json') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) return [json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), false];
        }
        return [$value === '' ? '(empty)' : $value, false];
    };
@endphp
<div class="history-shell">
    <header class="d-flex align-items-start justify-content-between gap-3 flex-wrap mb-3">
        <div>
            <h1 class="h3 mb-1">Change History</h1>
            <p class="text-muted mb-0">Review customization changes and restore the values that existed before a batch.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('site-customization.index') }}">Back to customization</a>
    </header>

    @if(session('status')) <div class="alert alert-success" role="status">{{ session('status') }}</div> @endif

    <section class="history-panel">
        @forelse($revisionBatches as $batch => $revisions)
            @php $first = $revisions->first(); @endphp
            <article class="history-row">
                <div class="history-summary">
                    <div>
                        <strong>{{ ucfirst(str_replace('_', ' ', $first->action)) }}</strong>
                        <div class="small text-muted">{{ $first->user?->email ?? 'System' }} &middot; {{ $first->created_at->format('M j, Y g:i A') }} &middot; {{ $revisions->count() }} {{ Str::plural('setting', $revisions->count()) }}</div>
                    </div>
                    <form method="POST" action="{{ route('site-customization.restore', $batch) }}" onsubmit="return confirm('Restore the values from before this change batch?')">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger" type="submit">Restore previous</button>
                    </form>
                </div>
                <details class="history-details">
                    <summary class="px-3 py-2">View field-by-field changes</summary>
                    <table>
                        <thead><tr><th scope="col">Setting</th><th scope="col">Previous value</th><th scope="col">New value</th></tr></thead>
                        <tbody>
                        @foreach($revisions as $revision)
                            @php
                                [$previousValue, $previousDefault] = $historyValue($revision->previous_value, $revision->setting_key);
                                [$newValue, $newDefault] = $historyValue($revision->new_value, $revision->setting_key);
                            @endphp
                            <tr>
                                <td><strong>{{ $definitions[$revision->setting_key]['label'] ?? Str::headline($revision->setting_key) }}</strong><div class="text-muted">{{ $revision->setting_key }}</div></td>
                                <td><span class="history-value {{ $previousDefault ? 'history-default' : '' }}">{{ $previousValue }}</span></td>
                                <td><span class="history-value {{ $newDefault ? 'history-default' : '' }}">{{ $newValue }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </details>
            </article>
        @empty
            <p class="text-muted mb-0">No customization changes have been published yet.</p>
        @endforelse

        @if($historyPage->hasPages())
            <div class="mt-3">{{ $historyPage->links() }}</div>
        @endif
    </section>
</div>
@endsection
