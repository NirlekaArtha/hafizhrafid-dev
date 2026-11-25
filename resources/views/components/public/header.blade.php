@php
    // Items appear only once their route exists, so the nav never links to a missing page.
    // Route names are placeholders until F1-02 introduces the locale-prefixed routes.
    $items = collect([
        ['route' => 'home', 'label' => __('layout.nav.home')],
        ['route' => 'portfolio', 'label' => __('layout.nav.portfolio')],
        ['route' => 'blog.index', 'label' => __('layout.nav.blog')],
    ])->filter(fn (array $item): bool => Route::has($item['route']));
@endphp

<header class="border-b border-border">
    <div class="page-container flex flex-wrap items-center justify-between gap-x-6 gap-y-1 py-2 sm:flex-nowrap sm:py-0 sm:h-16">
        <a href="{{ route('home') }}" class="inline-flex min-h-11 items-center rounded-md text-lg font-semibold tracking-tight">
            {{ __('layout.brand') }}
        </a>

        <div class="flex items-center gap-2 sm:order-last">
            <x-public.theme-toggle />
        </div>

        <nav aria-label="{{ __('layout.nav.label') }}" class="-mx-2 flex w-full gap-1 overflow-x-auto sm:mx-0 sm:w-auto sm:flex-1 sm:justify-end">
            @foreach ($items as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @if (request()->routeIs($item['route'])) aria-current="page" @endif
                    class="inline-flex min-h-11 items-center rounded-md px-3 text-[0.9375rem] font-medium text-muted transition-colors duration-150 hover:text-fg aria-[current=page]:text-fg aria-[current=page]:underline aria-[current=page]:decoration-primary aria-[current=page]:decoration-2 aria-[current=page]:underline-offset-8"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</header>
