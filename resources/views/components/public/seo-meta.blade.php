@props([
    'title' => null,
    'description' => null,
    'canonical' => null,
    'ogType' => 'website',
    'ogImage' => null,
    'ogImageAlt' => null,
    'robots' => null,
    'publishedTime' => null,
    'modifiedTime' => null,
    'hreflangUrls' => null,
])

@php
    use App\Domain\Localization\Enums\Locale;
    use App\Domain\Localization\Services\HreflangResolver;

    $siteName = (string) config('app.name');
    $fullTitle = filled($title) ? $title.' | '.$siteName : $siteName;
    $metaDescription = filled($description) ? (string) $description : __('layout.meta.default_description');
    $activeLocale = Locale::tryFromInput(app()->getLocale()) ?? Locale::default();
    $resolver = app(HreflangResolver::class);
    $request = request();

    $canonicalUrl = $resolver->canonicalUrl($request, $canonical);
    $alternates = $resolver->alternates($request, $hreflangUrls);
    $resolvedOgImage = filled($ogImage) ? $resolver->toAbsoluteUrl($request, (string) $ogImage) : null;
    $formattedPublishedTime = $publishedTime instanceof \DateTimeInterface
        ? $publishedTime->format(\DateTimeInterface::ATOM)
        : (filled($publishedTime) ? (string) $publishedTime : null);
    $formattedModifiedTime = $modifiedTime instanceof \DateTimeInterface
        ? $modifiedTime->format(\DateTimeInterface::ATOM)
        : (filled($modifiedTime) ? (string) $modifiedTime : null);

    $alternateOgLocales = [];

    foreach (Locale::cases() as $localeCase) {
        if ($localeCase !== $activeLocale && isset($alternates[$localeCase->value])) {
            $alternateOgLocales[] = $localeCase->ogLocale();
        }
    }
@endphp

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
@if (filled($robots))
    <meta name="robots" content="{{ $robots }}">
@endif
<link rel="canonical" href="{{ $canonicalUrl }}">
<x-public.hreflang :alternates="$alternates" />

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:locale" content="{{ $activeLocale->ogLocale() }}">
@foreach ($alternateOgLocales as $alternateOgLocale)
    <meta property="og:locale:alternate" content="{{ $alternateOgLocale }}">
@endforeach
@if ($resolvedOgImage !== null)
    <meta property="og:image" content="{{ $resolvedOgImage }}">
    @if (filled($ogImageAlt))
        <meta property="og:image:alt" content="{{ $ogImageAlt }}">
    @endif
@endif
@if ($ogType === 'article')
    @if ($formattedPublishedTime !== null)
        <meta property="article:published_time" content="{{ $formattedPublishedTime }}">
    @endif
    @if ($formattedModifiedTime !== null)
        <meta property="article:modified_time" content="{{ $formattedModifiedTime }}">
    @endif
@endif

<meta name="twitter:card" content="{{ $resolvedOgImage !== null ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@if ($resolvedOgImage !== null)
    <meta name="twitter:image" content="{{ $resolvedOgImage }}">
    @if (filled($ogImageAlt))
        <meta name="twitter:image:alt" content="{{ $ogImageAlt }}">
    @endif
@endif
