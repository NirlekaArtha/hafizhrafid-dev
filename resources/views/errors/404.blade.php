@php
    use Illuminate\Support\Facades\Route;

    $secondaryLinks = collect([
        ['route' => 'public.portfolio', 'label' => __('common.actions.back_to_portfolio')],
        ['route' => 'public.blog.index', 'label' => __('common.actions.back_to_blog')],
    ])->filter(fn (array $item): bool => Route::has($item['route']));
@endphp

<x-layouts::public
    :title="__('common.errors.not_found_title')"
    :description="__('common.errors.not_found_body')"
    robots="noindex, follow"
    :locale-urls="$localeUrls ?? []"
    :hreflang-urls="[]"
>
    <section class="page-container py-20 sm:py-28">
        <div class="max-w-prose">
            <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">
                {{ __('common.errors.not_found_title') }}
            </h1>
            <p class="mt-4 text-base leading-7 text-muted sm:text-lg">
                {{ __('common.errors.not_found_body') }}
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a
                    href="{{ route('public.home') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg bg-primary px-5 text-sm font-medium text-primary-contrast transition-opacity duration-150 hover:opacity-90 active:opacity-80"
                >
                    {{ __('common.actions.back_to_home') }}
                </a>

                @foreach ($secondaryLinks as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg border border-border bg-surface px-5 text-sm font-medium text-fg transition-colors duration-150 hover:border-primary/50 hover:text-primary"
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts::public>
