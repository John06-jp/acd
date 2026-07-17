@extends('layouts.app')

@section('title', 'Site Customization')

@push('styles')
<style>
    .customization-shell { max-width: 1200px; margin: 0 auto; }
    .customization-header, .customization-panel, .revision-panel { background:#fff; border:1px solid #e5e7eb; border-radius:14px; box-shadow:0 2px 12px rgba(15,23,42,.05); }
    .customization-header { padding:1.25rem; margin-bottom:1rem; display:flex; gap:1rem; align-items:center; justify-content:space-between; flex-wrap:wrap; }
    .customization-header h1 { font-size:1.45rem; margin:0; font-weight:700; }
    .customization-tabs { display:flex; overflow-x:auto; gap:.35rem; padding:.65rem; border-bottom:1px solid #e5e7eb; }
    .customization-tabs button { white-space:nowrap; border:0; border-radius:8px; padding:.65rem .9rem; background:transparent; font-weight:600; color:#475569; }
    .customization-tabs button.active { color:#125a82; background:#e3f4fc; }
    .customization-section { display:none; padding:1.25rem; }
    .customization-section.active { display:block; }
    .settings-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:1rem; }
    .setting-field { border:1px solid #e5e7eb; border-radius:10px; padding:.85rem; min-width:0; }
    .setting-field label { display:block; font-size:.84rem; font-weight:700; margin-bottom:.4rem; }
    .setting-field textarea { min-height:90px; }
    .setting-color-row { display:grid; grid-template-columns:52px 1fr; gap:.5rem; }
    .setting-color-row input[type=color] { width:52px; height:40px; padding:2px; }
    .image-setting img { display:block; max-width:220px; max-height:100px; object-fit:contain; margin-bottom:.65rem; background:#f8fafc; border:1px solid #e5e7eb; border-radius:8px; }
    .section-actions { display:flex; gap:.5rem; justify-content:flex-end; flex-wrap:wrap; margin-top:1.25rem; }
    .revision-panel { margin-top:1rem; padding:1rem; }
    .revision-row { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:.75rem 0; border-top:1px solid #edf2f7; }
    .preview-frame { border:1px solid #cbd5e1; border-radius:12px; padding:1rem; background:#f8fafc; margin-top:1rem; overflow:hidden; }
    .preview-card { max-width:680px; margin:auto; padding:1.25rem; border-radius:12px; background:var(--preview-card,#fff); transition:.2s; }
    @media (max-width:767px) { .settings-grid { grid-template-columns:1fr; } .customization-header { align-items:flex-start; } }
</style>
@endpush

@section('content')
@php $settingsByGroup = collect($definitions)->groupBy('group', true); @endphp
<div class="customization-shell" data-customization-editor>
    <header class="customization-header">
        <div>
            <h1>Site Customization</h1>
            <p class="text-muted mb-0">Manage approved content, images, and shared theme tokens.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-primary" type="button" data-save-all>Save All</button>
            <form method="POST" action="{{ route('site-customization.reset-all') }}" data-confirm="Reset every customization setting to its factory default?">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger" type="submit">Reset All</button>
            </form>
        </div>
    </header>

    @if(session('status')) <div class="alert alert-success" role="status">{{ session('status') }}</div> @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert"><strong>Please correct the highlighted settings.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="customization-panel">
        <div class="customization-tabs" role="tablist" aria-label="Customization sections">
            @foreach($groups as $group => $label)
                <button type="button" role="tab" class="{{ $loop->first ? 'active' : '' }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" data-tab="{{ $group }}">{{ $label }}</button>
            @endforeach
        </div>

        @foreach($groups as $group => $groupLabel)
            <section class="customization-section {{ $loop->first ? 'active' : '' }}" data-section="{{ $group }}">
                <h2 class="h5 mb-3">{{ $groupLabel }}</h2>
                <form method="POST" action="{{ route('site-customization.update-section', $group) }}" data-settings-form data-group="{{ $group }}">
                    @csrf @method('PUT')
                    <div class="settings-grid">
                        @foreach($settingsByGroup->get($group, collect())->where('type', '!=', 'image') as $key => $definition)
                            @php $fieldId = 'setting-'.str_replace('.', '-', $key); $value = old('settings', [])[$key] ?? $values[$key]; @endphp
                            <div class="setting-field">
                                <label for="{{ $fieldId }}">{{ $definition['label'] }}</label>
                                @if($definition['control'] === 'textarea')
                                    <textarea class="form-control @error("settings.$key") is-invalid @enderror" id="{{ $fieldId }}" name="settings[{{ $key }}]" data-setting="{{ $key }}">{{ $value }}</textarea>
                                @elseif($definition['control'] === 'toggle')
                                    <input type="hidden" name="settings[{{ $key }}]" value="0">
                                    <div class="form-check form-switch"><input class="form-check-input" id="{{ $fieldId }}" type="checkbox" name="settings[{{ $key }}]" value="1" data-setting="{{ $key }}" @checked((bool)$value)></div>
                                @elseif($definition['control'] === 'color')
                                    <div class="setting-color-row"><input class="form-control form-control-color" type="color" value="{{ $value }}" aria-label="{{ $definition['label'] }} color picker" data-color-picker><input class="form-control @error("settings.$key") is-invalid @enderror" id="{{ $fieldId }}" name="settings[{{ $key }}]" value="{{ $value }}" pattern="#[0-9A-Fa-f]{6}" data-setting="{{ $key }}" data-color-text></div>
                                @elseif($definition['control'] === 'select')
                                    <select class="form-select" id="{{ $fieldId }}" name="settings[{{ $key }}]" data-setting="{{ $key }}">@foreach($definition['options'] as $option)<option value="{{ $option }}" @selected($value === $option)>{{ ucfirst($option) }}</option>@endforeach</select>
                                @else
                                    <input class="form-control @error("settings.$key") is-invalid @enderror" id="{{ $fieldId }}" type="{{ $definition['control'] === 'number' ? 'number' : ($definition['control'] === 'url' ? 'text' : 'text') }}" name="settings[{{ $key }}]" value="{{ $value }}" data-setting="{{ $key }}">
                                @endif
                                @error("settings.$key") <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        @endforeach
                    </div>
                    <div class="section-actions">
                        <button class="btn btn-primary" type="submit">Save Section</button>
                    </div>
                </form>

                @if($settingsByGroup->get($group, collect())->where('type', 'image')->isNotEmpty())
                    <h3 class="h6 mt-4">Images</h3>
                    <div class="settings-grid">
                    @foreach($settingsByGroup->get($group)->where('type', 'image') as $key => $definition)
                        <div class="setting-field image-setting">
                            <label>{{ $definition['label'] }}</label>
                            <img src="{{ app(\App\Services\SiteSettingsService::class)->publicUrl($key) }}" alt="Current {{ strtolower($definition['label']) }}" data-image-preview>
                            <form method="POST" enctype="multipart/form-data" action="{{ route('site-customization.images.upload', $key) }}">
                                @csrf
                                <input class="form-control form-control-sm mb-2" type="file" name="image" accept="image/png,image/jpeg,image/webp" data-image-input required>
                                <button class="btn btn-sm btn-primary" type="submit">Upload / Replace</button>
                            </form>
                            @if(str_starts_with($values[$key], config('site-customization.image_directory').'/'))
                            <form class="mt-2" method="POST" action="{{ route('site-customization.images.remove', $key) }}" data-confirm="Restore this image to its factory default?">
                                @csrf @method('DELETE') <button class="btn btn-sm btn-outline-danger" type="submit">Remove override</button>
                            </form>
                            @endif
                        </div>
                    @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('site-customization.reset-section', $group) }}" class="section-actions" data-confirm="Reset {{ $groupLabel }} to factory defaults?">
                    @csrf @method('DELETE') <button class="btn btn-outline-danger" type="submit">Reset Section</button>
                </form>
            </section>
        @endforeach
    </div>

    <div class="preview-frame" aria-live="polite">
        <div class="d-flex justify-content-between align-items-center mb-2"><strong>Live Preview</strong><div><button type="button" class="btn btn-sm btn-outline-secondary" data-preview-width="680px">Desktop</button> <button type="button" class="btn btn-sm btn-outline-secondary" data-preview-width="360px">Mobile</button></div></div>
        <div class="preview-card" data-preview-card><h3 data-preview-title>{{ $values['landing.hero_heading'] }}</h3><p data-preview-description>{{ $values['landing.hero_description'] }}</p><button type="button" class="btn btn-primary" data-preview-button>{{ $values['landing.primary_button_label'] }}</button></div>
    </div>

    <section class="revision-panel">
        <h2 class="h5">Recent Changes</h2>
        @forelse($revisionBatches as $batch => $revisions)
            @php $first = $revisions->first(); @endphp
            <div class="revision-row"><div><strong>{{ ucfirst(str_replace('_',' ', $first->action)) }}</strong><div class="small text-muted">{{ $first->user?->email ?? 'System' }} · {{ $first->created_at->diffForHumans() }} · {{ $revisions->count() }} setting(s)</div></div><form method="POST" action="{{ route('site-customization.restore', $batch) }}" data-confirm="Restore the values from before this change batch?">@csrf<button class="btn btn-sm btn-outline-secondary" type="submit">Restore previous</button></form></div>
        @empty <p class="text-muted mb-0">No customization changes have been published yet.</p> @endforelse
    </section>

    <form method="POST" action="{{ route('site-customization.update-all') }}" class="d-none" data-all-form>@csrf @method('PUT')</form>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const editor = document.querySelector('[data-customization-editor]'); if (!editor) return;
    let dirty = false;
    const tabs = editor.querySelectorAll('[data-tab]');
    tabs.forEach(tab => tab.addEventListener('click', () => { tabs.forEach(t => { t.classList.toggle('active', t === tab); t.setAttribute('aria-selected', t === tab ? 'true' : 'false'); }); editor.querySelectorAll('[data-section]').forEach(s => s.classList.toggle('active', s.dataset.section === tab.dataset.tab)); }));
    editor.querySelectorAll('[data-color-picker]').forEach(picker => { const text = picker.parentElement.querySelector('[data-color-text]'); picker.addEventListener('input', () => { text.value = picker.value; text.dispatchEvent(new Event('input', {bubbles:true})); }); text.addEventListener('input', () => { if (/^#[0-9a-f]{6}$/i.test(text.value)) picker.value = text.value; }); });
    editor.querySelectorAll('[data-settings-form] input, [data-settings-form] textarea, [data-settings-form] select').forEach(input => input.addEventListener('input', () => { dirty = true; const key=input.dataset.setting; if(key==='landing.hero_heading') editor.querySelector('[data-preview-title]').textContent=input.value; if(key==='landing.hero_description') editor.querySelector('[data-preview-description]').textContent=input.value; if(key==='landing.primary_button_label') editor.querySelector('[data-preview-button]').textContent=input.value; if(key==='buttons.primary_background') editor.querySelector('[data-preview-button]').style.backgroundColor=input.value; }));
    editor.querySelectorAll('form').forEach(form => form.addEventListener('submit', e => { if (form.dataset.confirm && !confirm(form.dataset.confirm)) { e.preventDefault(); return; } dirty=false; }));
    editor.querySelectorAll('[data-image-input]').forEach(input => input.addEventListener('change', () => { const file=input.files[0]; if(file) input.closest('.image-setting').querySelector('[data-image-preview]').src=URL.createObjectURL(file); dirty=true; }));
    editor.querySelectorAll('[data-preview-width]').forEach(button => button.addEventListener('click', () => editor.querySelector('[data-preview-card]').style.maxWidth=button.dataset.previewWidth));
    editor.querySelector('[data-save-all]').addEventListener('click', () => { const form=editor.querySelector('[data-all-form]'); form.querySelectorAll('[data-generated]').forEach(n=>n.remove()); editor.querySelectorAll('[data-settings-form]').forEach(sectionForm => new FormData(sectionForm).forEach((value,key) => { if(key==='_token'||key==='_method') return; const input=document.createElement('input'); input.type='hidden'; input.name=key; input.value=value; input.dataset.generated='1'; form.appendChild(input); })); dirty=false; form.submit(); });
    window.addEventListener('beforeunload', event => { if(dirty) { event.preventDefault(); event.returnValue=''; } });
})();
</script>
@endpush
