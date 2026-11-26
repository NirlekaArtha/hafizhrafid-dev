@props([
    'urls' => null,
    'alternates' => null,
])

@php
    use App\Domain\Localization\Services\HreflangResolver;

    $resolvedAlternates = is_array($alternates)
        ? $alternates
        : app(HreflangResolver::class)->alternates(request(), $urls);
@endphp

@foreach ($resolvedAlternates as $hreflang => $href)
    <link rel="alternate" hreflang="{{ $hreflang }}" href="{{ $href }}">
@endforeach
