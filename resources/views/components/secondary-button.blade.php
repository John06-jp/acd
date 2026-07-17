<button {{ $attributes->merge(['type' => 'button', 'class' => 'site-btn site-btn-secondary inline-flex items-center px-4 py-2 font-semibold text-xs uppercase tracking-widest disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
