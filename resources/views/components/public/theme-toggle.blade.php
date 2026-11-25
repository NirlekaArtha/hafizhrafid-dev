{{-- Cycles system, light, dark. The icon and label follow html[data-theme], see resources/js/app.js. --}}
<button
    type="button"
    data-theme-toggle
    data-label-system="{{ __('layout.theme.system') }}"
    data-label-light="{{ __('layout.theme.light') }}"
    data-label-dark="{{ __('layout.theme.dark') }}"
    aria-label="{{ __('layout.theme.system') }}"
    class="inline-flex size-11 items-center justify-center rounded-md text-muted transition-colors duration-150 hover:bg-surface hover:text-fg active:bg-border"
>
    <svg data-theme-icon="system" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <rect x="3" y="4" width="18" height="12" rx="2" />
        <path d="M8 20h8M12 16v4" />
    </svg>
    <svg data-theme-icon="light" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="4" />
        <path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6l1.4 1.4M17 17l1.4 1.4M5.6 18.4 7 17M17 7l1.4-1.4" />
    </svg>
    <svg data-theme-icon="dark" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M20 14.5A8 8 0 0 1 9.5 4 8 8 0 1 0 20 14.5Z" />
    </svg>
</button>
