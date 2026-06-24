<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-5 py-2.5 bg-app-card border border-app-border rounded-lg font-semibold text-xs text-app-dark uppercase tracking-widest shadow-sm hover:bg-app-background focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
