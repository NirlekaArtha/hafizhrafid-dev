<?php

declare(strict_types=1);

use App\Domain\Localization\Enums\Locale;
use App\Domain\Localization\Services\LocaleUrlResolver;
use Illuminate\Http\Request;

test('it replaces the leading locale prefix on the current path', function (): void {
    $resolver = new LocaleUrlResolver;

    expect($resolver->urlFor(Request::create('/id'), Locale::En))->toBe('/en')
        ->and($resolver->urlFor(Request::create('/en'), Locale::Id))->toBe('/id')
        ->and($resolver->urlFor(Request::create('/id/portfolio'), Locale::En))->toBe('/en/portfolio')
        ->and($resolver->urlFor(Request::create('/en/blog/category/ai'), Locale::Id))->toBe('/id/blog/category/ai');
});

test('it preserves the query string when switching locales', function (): void {
    $resolver = new LocaleUrlResolver;
    $request = Request::create('/id/blog?category=web&page=2');

    expect($resolver->urlFor($request, Locale::En))->toBe('/en/blog?category=web&page=2');
});

test('it falls back to localized home for root and non-locale paths', function (): void {
    $resolver = new LocaleUrlResolver;

    expect($resolver->urlFor(Request::create('/'), Locale::Id))->toBe('/id')
        ->and($resolver->urlFor(Request::create('/'), Locale::En))->toBe('/en')
        ->and($resolver->urlFor(Request::create('/up'), Locale::Id))->toBe('/id');
});

test('it prefers explicit per-page localeUrls when provided and valid', function (): void {
    $resolver = new LocaleUrlResolver;
    $request = Request::create('/id/blog/arsitektur-web');

    $override = [
        'id' => '/id/blog/arsitektur-web',
        'en' => '/en/blog/web-architecture',
    ];

    expect($resolver->urlFor($request, Locale::En, $override))->toBe('/en/blog/web-architecture')
        ->and($resolver->urlFor($request, Locale::Id, $override))->toBe('/id/blog/arsitektur-web');
});

test('it sanitizes invalid or cross-origin redirect targets to localized home', function (string $unsafe): void {
    $resolver = new LocaleUrlResolver;

    expect($resolver->sanitizeTargetUrl($unsafe, Locale::En))->toBe('/en');
})->with([
    'external https url' => 'https://evil.test/en',
    'protocol-relative url' => '//evil.test/en',
    'backslash path' => '/\\evil.test',
    'path traversal' => '/en/../admin',
    'mismatched locale prefix' => '/id/portfolio',
    'non-locale path' => '/admin',
    'control characters' => "/en\r\nSet-Cookie: bad=1",
    'empty string' => '',
]);

test('it accepts valid internal localized redirect targets', function (string $safe): void {
    $resolver = new LocaleUrlResolver;

    expect($resolver->sanitizeTargetUrl($safe, Locale::Id))->toBe($safe);
})->with([
    'localized home' => '/id',
    'localized subpath' => '/id/portfolio',
    'localized subpath with query' => '/id/blog?page=2',
    'localized subpath with fragment' => '/id/portfolio#contact',
]);
