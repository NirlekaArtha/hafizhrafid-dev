@props(['localeUrls' => []])

@php
    use App\Domain\Localization\Enums\Locale;
    use App\Domain\Localization\Services\LocaleUrlResolver;

    $activeLocale = Locale::tryFromInput(app()->getLocale()) ?? Locale::default();
    $urlResolver = app(LocaleUrlResolver::class);
    $request = request();
@endphp

<div
    role="group"
    aria-label="{{ __('layout.language.label') }}"
    class="inline-flex items-center divide-x divide-border rounded-md border border-border bg-surface text-sm"
>
    @foreach (Locale::cases() as $localeCase)
        @php
            $code = $localeCase->value;
            $label = __('layout.language.'.$code);
            $isActive = $activeLocale === $localeCase;
        @endphp

        @if ($isActive)
            <span
                aria-current="true"
                class="inline-flex min-h-11 min-w-11 items-center justify-center bg-bg px-3 font-semibold text-fg first:rounded-l-[5px] last:rounded-r-[5px]"
            >
                <span class="sr-only">{{ __('layout.language.current_'.$code) }}</span>
                <span aria-hidden="true">{{ $label }}</span>
            </span>
        @else
            @php
                $targetPath = $urlResolver->urlFor($request, $localeCase, $localeUrls);
            @endphp

            <a
                href="{{ route('locale.switch', ['locale' => $code, 'redirect' => $targetPath]) }}"
                hreflang="{{ $code }}"
                rel="nofollow"
                aria-label="{{ __('layout.language.switch_to_'.$code) }}"
                class="inline-flex min-h-11 min-w-11 items-center justify-center px-3 font-medium text-muted transition-colors duration-150 first:rounded-l-[5px] last:rounded-r-[5px] hover:bg-bg/60 hover:text-fg active:bg-border"
            >
                {{ $label }}
            </a>
        @endif
    @endforeach
</div>
