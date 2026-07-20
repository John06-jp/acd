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
    .revision-batch { border-top:1px solid #edf2f7; padding:.85rem 0; }
    .revision-row { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:.75rem 0; border-top:1px solid #edf2f7; }
    .revision-summary { display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    .revision-details { margin-top:.75rem; border:1px solid #e5e7eb; border-radius:10px; overflow-x:auto; }
    .revision-details table { width:100%; margin:0; font-size:.85rem; }
    .revision-details th, .revision-details td { padding:.6rem .7rem; border-bottom:1px solid #edf2f7; vertical-align:top; }
    .revision-details tr:last-child td { border-bottom:0; }
    .revision-value { display:block; max-width:360px; max-height:6rem; overflow:auto; white-space:pre-wrap; overflow-wrap:anywhere; color:#334155; }
    .revision-default { color:#64748b; font-style:italic; }
    .preview-frame { border:1px solid #cbd5e1; border-radius:12px; padding:1rem; background:#f8fafc; margin-top:1rem; overflow:hidden; }
    .preview-stage { width:100%; max-width:900px; margin:auto; transition:max-width .2s ease; }
    .preview-stage iframe { display:block; width:100%; height:500px; border:1px solid #cbd5e1; border-radius:10px; background:#fff; }
    .preview-toolbar { display:flex; gap:.4rem; align-items:center; justify-content:space-between; flex-wrap:wrap; margin-bottom:.75rem; }
    .preview-scenes { display:flex; gap:.35rem; flex-wrap:wrap; }
    .preview-scenes button.active { background:#125a82; color:#fff; border-color:#125a82; }
    @media (max-width:767px) { .settings-grid { grid-template-columns:1fr; } .customization-header { align-items:flex-start; } .revision-summary { align-items:flex-start; flex-direction:column; } }
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
                                <input class="form-control form-control-sm mb-2" type="file" name="image" accept="image/png,image/jpeg,image/webp" data-image-input data-image-key="{{ $key }}" required>
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
        <div class="preview-toolbar">
            <div><strong>Exact Live Preview</strong><div class="small text-muted">Unsaved values render only inside this isolated preview.</div></div>
            <div><button type="button" class="btn btn-sm btn-outline-secondary active" data-preview-width="900px">Desktop</button> <button type="button" class="btn btn-sm btn-outline-secondary" data-preview-width="390px">Mobile</button></div>
        </div>
        <div class="preview-scenes mb-2" role="tablist" aria-label="Preview page">
            <button type="button" class="btn btn-sm btn-outline-secondary active" data-preview-scene="landing">Landing</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-preview-scene="login">Login</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-preview-scene="sidebar">Sidebar</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-preview-scene="theme">Buttons &amp; Tables</button>
        </div>
        <div class="preview-stage" data-preview-stage>
            <iframe title="Unsaved site customization preview" sandbox data-preview-frame></iframe>
        </div>
    </div>

    <section class="revision-panel" id="change-history">
        <h2 class="h5 mb-1">Change History</h2>
        <p class="small text-muted">Review published setting changes and restore the values that existed before any batch.</p>
        @forelse($revisionBatches as $batch => $revisions)
            @php
                $first = $revisions->first();
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
            <div class="revision-row"><div><strong>{{ ucfirst(str_replace('_',' ', $first->action)) }}</strong><div class="small text-muted">{{ $first->user?->email ?? 'System' }} · {{ $first->created_at->diffForHumans() }} · {{ $revisions->count() }} setting(s)</div></div><form method="POST" action="{{ route('site-customization.restore', $batch) }}" data-confirm="Restore the values from before this change batch?">@csrf<button class="btn btn-sm btn-outline-secondary" type="submit">Restore previous</button></form></div>
            <details class="revision-details">
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
                            <td><span class="revision-value {{ $previousDefault ? 'revision-default' : '' }}">{{ $previousValue }}</span></td>
                            <td><span class="revision-value {{ $newDefault ? 'revision-default' : '' }}">{{ $newValue }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </details>
        @empty <p class="text-muted mb-0">No customization changes have been published yet.</p> @endforelse
        @if($historyPage->hasPages())
            <div class="mt-3">{{ $historyPage->links() }}</div>
        @endif
    </section>

    <form method="POST" action="{{ route('site-customization.update-all') }}" class="d-none" data-all-form>@csrf @method('PUT')</form>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const editor = document.querySelector('[data-customization-editor]'); if (!editor) return;
    let dirty = false;
    let previewScene = 'landing';
    const initialValues = @json($values);
    const imageUrls = {
        'branding.landing_logo': @json(app(\App\Services\SiteSettingsService::class)->publicUrl('branding.landing_logo')),
        'branding.login_logo': @json(app(\App\Services\SiteSettingsService::class)->publicUrl('branding.login_logo')),
        'branding.sidebar_logo': @json(app(\App\Services\SiteSettingsService::class)->publicUrl('branding.sidebar_logo')),
        'branding.sidebar_compact_logo': @json(app(\App\Services\SiteSettingsService::class)->publicUrl('branding.sidebar_compact_logo')),
        'landing.hero_image': @json(app(\App\Services\SiteSettingsService::class)->publicUrl('landing.hero_image')),
    };
    const frame = editor.querySelector('[data-preview-frame]');
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[character]));
    const safeColor = (value, fallback) => /^#[0-9a-f]{6}$/i.test(value ?? '') ? value : fallback;
    const settingValues = () => {
        const values = {...initialValues};
        editor.querySelectorAll('[data-setting]').forEach(input => {
            values[input.dataset.setting] = input.type === 'checkbox' ? input.checked : input.value;
        });
        return values;
    };
    const previewDocument = (values, scene) => {
        const primary = safeColor(values['buttons.primary_background'], '#29abe2');
        const primaryText = safeColor(values['buttons.primary_text'], '#ffffff');
        const secondary = safeColor(values['buttons.secondary_background'], '#6c757d');
        const secondaryText = safeColor(values['buttons.secondary_text'], '#ffffff');
        const radius = Math.max(0, Math.min(32, Number(values['buttons.radius']) || 8));
        const font = escapeHtml(values['advanced_theme.font_family'] || 'Inter');
        const base = `<style>*{box-sizing:border-box}body{margin:0;font-family:${font},Arial,sans-serif;background:${safeColor(values['advanced_theme.page_background'],'#f4f9fc')};color:#1f2937}img{max-width:100%}.btn{display:inline-block;padding:10px 16px;border-radius:${radius}px;text-decoration:none;font-weight:700;border:1px solid transparent}.primary{background:${primary};color:${primaryText};border-color:${safeColor(values['buttons.primary_border'],primary)}}.secondary{background:${secondary};color:${secondaryText};border-color:${safeColor(values['buttons.secondary_border'],secondary)}}</style>`;
        if (scene === 'login') {
            return `<!doctype html><html><head>${base}<style>body{min-height:498px;display:grid;place-items:center;padding:18px;background:${safeColor(values['login.page_background'],'#f8f9fa')}}.card{width:min(420px,100%);padding:32px;border-radius:${Number(values['login.card_radius'])||20}px;background:${safeColor(values['login.card_background'],'#fff')};box-shadow:0 12px 32px #0002;text-align:center;color:${safeColor(values['login.text_color'],'#212529')}}.logo{max-width:170px;max-height:90px;object-fit:contain}.muted{color:${safeColor(values['login.muted_text_color'],'#6c757d')}}label{display:block;text-align:left;font-weight:600;margin:12px 0 5px}input{width:100%;padding:12px;border:1px solid #ced4da;border-radius:12px}.login-btn{width:100%;margin-top:18px;background:${safeColor(values['login.button_background'],'#0d6efd')};color:${safeColor(values['login.button_text_color'],'#fff')}} </style></head><body><main class="card"><img class="logo" src="${escapeHtml(imageUrls['branding.login_logo'])}" alt=""><h1>${escapeHtml(values['login.title'])}</h1><p class="muted">${escapeHtml(values['login.subtitle'])}</p><label>${escapeHtml(values['login.email_label'])}</label><input type="text"><label>${escapeHtml(values['login.password_label'])}</label><input type="password"><button class="btn login-btn">${escapeHtml(values['login.button_text'])}</button>${values['login.footer_message']?`<p class="muted">${escapeHtml(values['login.footer_message'])}</p>`:''}</main></body></html>`;
        }
        if (scene === 'sidebar') {
            return `<!doctype html><html><head>${base}<style>body{background:#eef2f7}.shell{display:flex;min-height:498px}.side{width:${Math.max(220,Math.min(360,Number(values['sidebar.expanded_width'])||260))}px;padding:14px 10px;background:${safeColor(values['sidebar.background'],'#0373e3')};color:${safeColor(values['sidebar.text'],'#fff')}}.brand{background:#fff;border-radius:10px;padding:10px;text-align:center}.brand img{max-height:58px}.role{font-size:11px;color:#125a82;font-weight:700}.link{display:block;padding:10px;margin-top:6px;border-radius:8px;color:inherit}.link.active{background:${safeColor(values['sidebar.active_background'],'#ffc233')};color:${safeColor(values['sidebar.active_text'],'#fff')}}.link.hover{background:${safeColor(values['sidebar.hover_background'],'#0373e3')};color:${safeColor(values['sidebar.hover_text'],'#fff')}}.content{padding:30px;flex:1}</style></head><body><div class="shell"><aside class="side"><div class="brand"><img src="${escapeHtml(imageUrls['branding.sidebar_logo'])}" alt=""><div class="role">ADMINDEVELOPER ${escapeHtml(values['sidebar.dashboard_label'])}</div></div><div class="link active">${escapeHtml(values['sidebar.home_label'])}</div>${values['sidebar.show_attendance']?`<div class="link">${escapeHtml(values['sidebar.attendance_label'])}</div>`:''}${values['sidebar.show_data']?`<div class="link hover">${escapeHtml(values['sidebar.data_label'])}</div>`:''}${values['sidebar.show_communication']?`<div class="link">${escapeHtml(values['sidebar.communication_label'])}</div>`:''}<div class="link">Site Customization</div></aside><main class="content"><h2>Sidebar Preview</h2><p>Expanded width, colors, labels, hover, active state, and visibility update here.</p></main></div></body></html>`;
        }
        if (scene === 'theme') {
            const variants = ['primary','secondary','success','warning','danger','neutral'];
            const buttons = variants.map(variant => `<button class="sample" style="background:${safeColor(values[`buttons.${variant}_background`],'#777')};color:${safeColor(values[`buttons.${variant}_text`],'#fff')};border-color:${safeColor(values[`buttons.${variant}_border`],'#777')}">${variant[0].toUpperCase()+variant.slice(1)}</button>`).join('');
            const padding = values['tables.spacing']==='compact' ? values['tables.compact_padding'] : values['tables.comfortable_padding'];
            return `<!doctype html><html><head>${base}<style>body{padding:24px}.samples{display:flex;gap:8px;flex-wrap:wrap}.sample{padding:10px 14px;border:1px solid;border-radius:${radius}px;font-weight:700}table{width:100%;margin-top:24px;border-collapse:separate;border-spacing:0;border-radius:${Number(values['tables.radius'])||8}px;overflow:hidden;background:${safeColor(values['tables.body_background'],'#fff')};color:${safeColor(values['tables.body_text'],'#212529')}}th,td{padding:${Number(padding)||12}px;border:1px solid ${safeColor(values['tables.border'],'#dee2e6')}}th{background:${safeColor(values['tables.header_background'],'#29abe2')};color:${safeColor(values['tables.header_text'],'#fff')}.stripe{background:${safeColor(values['tables.stripe_background'],'#f8f9fa')}.hover{background:${safeColor(values['tables.hover_background'],'#e3f4fc')}.selected{background:${safeColor(values['tables.selected_background'],'#cfefff')}}</style></head><body><h2>Button Variants</h2><div class="samples">${buttons}</div><table><thead><tr><th>Name</th><th>Status</th></tr></thead><tbody><tr><td>Normal row</td><td>Active</td></tr><tr class="stripe"><td>Striped row</td><td>Pending</td></tr><tr class="hover"><td>Hover row</td><td>Review</td></tr><tr class="selected"><td>Selected row</td><td>Selected</td></tr></tbody></table></body></html>`;
        }
        return `<!doctype html><html><head>${base}<style>.header{padding:10px 18px;background:#fff}.logo{max-height:55px}.hero{min-height:220px;padding:30px;display:grid;place-items:center;text-align:center;background:linear-gradient(#0003,#0003),url("${escapeHtml(imageUrls['landing.hero_image'])}") center/cover;color:#fff}.faq{margin:20px;padding:20px;background:#fff;border-radius:12px}.footer{padding:14px;text-align:center;background:#125a82;color:#fff}</style></head><body><header class="header"><img class="logo" src="${escapeHtml(imageUrls['branding.landing_logo'])}" alt=""></header><section class="hero">${values['landing.show_hero_content']?`<div><h1>${escapeHtml(values['landing.hero_heading'])}</h1><p>${escapeHtml(values['landing.hero_description'])}</p><a class="btn primary">${escapeHtml(values['landing.primary_button_label'])}</a> <a class="btn secondary">${escapeHtml(values['landing.secondary_button_label'])}</a></div>`:''}</section>${values['landing.show_faq']?`<section class="faq"><h2>${escapeHtml(values['landing.section_heading'])}</h2><h3>${escapeHtml(values['landing.section_subheading'])}</h3><p>${escapeHtml(values['landing.section_description'])}</p></section>`:''}${values['landing.show_footer']?`<footer class="footer">${escapeHtml(values['landing.footer_text'])}</footer>`:''}</body></html>`;
    };
    const renderPreview = () => { frame.srcdoc = previewDocument(settingValues(), previewScene); };
    const tabs = editor.querySelectorAll('[data-tab]');
    tabs.forEach(tab => tab.addEventListener('click', () => { tabs.forEach(t => { t.classList.toggle('active', t === tab); t.setAttribute('aria-selected', t === tab ? 'true' : 'false'); }); editor.querySelectorAll('[data-section]').forEach(s => s.classList.toggle('active', s.dataset.section === tab.dataset.tab)); const sceneMap={branding:'landing',landing:'landing',login:'login',sidebar:'sidebar',buttons:'theme',tables:'theme',advanced_theme:'theme'}; previewScene=sceneMap[tab.dataset.tab]; editor.querySelectorAll('[data-preview-scene]').forEach(button=>button.classList.toggle('active',button.dataset.previewScene===previewScene)); renderPreview(); }));
    editor.querySelectorAll('[data-color-picker]').forEach(picker => { const text = picker.parentElement.querySelector('[data-color-text]'); picker.addEventListener('input', () => { text.value = picker.value; text.dispatchEvent(new Event('input', {bubbles:true})); }); text.addEventListener('input', () => { if (/^#[0-9a-f]{6}$/i.test(text.value)) picker.value = text.value; }); });
    editor.querySelectorAll('[data-settings-form] input, [data-settings-form] textarea, [data-settings-form] select').forEach(input => input.addEventListener('input', () => { dirty = true; renderPreview(); }));
    editor.querySelectorAll('form').forEach(form => form.addEventListener('submit', e => { if (form.dataset.confirm && !confirm(form.dataset.confirm)) { e.preventDefault(); return; } dirty=false; }));
    editor.querySelectorAll('[data-image-input]').forEach(input => input.addEventListener('change', () => { const file=input.files[0]; if(file) { const url=URL.createObjectURL(file); input.closest('.image-setting').querySelector('[data-image-preview]').src=url; imageUrls[input.dataset.imageKey]=url; renderPreview(); } dirty=true; }));
    editor.querySelectorAll('[data-preview-width]').forEach(button => button.addEventListener('click', () => { editor.querySelector('[data-preview-stage]').style.maxWidth=button.dataset.previewWidth; editor.querySelectorAll('[data-preview-width]').forEach(item=>item.classList.toggle('active',item===button)); }));
    editor.querySelectorAll('[data-preview-scene]').forEach(button => button.addEventListener('click', () => { previewScene=button.dataset.previewScene; editor.querySelectorAll('[data-preview-scene]').forEach(item=>item.classList.toggle('active',item===button)); renderPreview(); }));
    editor.querySelector('[data-save-all]').addEventListener('click', () => { const form=editor.querySelector('[data-all-form]'); form.querySelectorAll('[data-generated]').forEach(n=>n.remove()); editor.querySelectorAll('[data-settings-form]').forEach(sectionForm => new FormData(sectionForm).forEach((value,key) => { if(key==='_token'||key==='_method') return; const input=document.createElement('input'); input.type='hidden'; input.name=key; input.value=value; input.dataset.generated='1'; form.appendChild(input); })); dirty=false; form.submit(); });
    window.addEventListener('beforeunload', event => { if(dirty) { event.preventDefault(); event.returnValue=''; } });
    renderPreview();
})();
</script>
@endpush
