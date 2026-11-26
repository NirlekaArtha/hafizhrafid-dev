<?php

declare(strict_types=1);

use App\Domain\Localization\Services\HreflangResolver;
use Illuminate\Http\Request;

test('it resolves clean canonical urls without query strings', function (): void {
    $resolver = new HreflangResolver;

    expect($resolver->canonicalUrl(Request::create('https://hafizhrafid.dev/id/blog?utm_source=twitter&page=2')))
        ->toBe('https://hafizhrafid.dev/id/blog')
        ->and($resolver->canonicalUrl(Request::create('https://hafizhrafid.dev/en/')))
        ->toBe('https://hafizhrafid.dev/en');
});

test('it points root canonical url to the default locale home', function (): void {
    $resolver = new HreflangResolver;

    expect($resolver->canonicalUrl(Request::create('https://hafizhrafid.dev/')))
        ->toBe('https://hafizhrafid.dev/en');
});

test('it respects explicit canonical overrides for relative and absolute urls', function (): void {
    $resolver = new HreflangResolver;
    $request = Request::create('https://hafizhrafid.dev/id/blog?page=2');

    expect($resolver->canonicalUrl($request, '/id/blog?page=2'))
        ->toBe('https://hafizhrafid.dev/id/blog?page=2')
        ->and($resolver->canonicalUrl($request, 'https://hafizhrafid.dev/id/blog/custom'))
        ->toBe('https://hafizhrafid.dev/id/blog/custom');
});

test('it auto-resolves bilingual hreflang alternates and x-default for localized paths', function (): void {
    $resolver = new HreflangResolver;

    expect($resolver->alternates(Request::create('https://hafizhrafid.dev/id/portfolio?ref=cv')))
        ->toBe([
            'id' => 'https://hafizhrafid.dev/id/portfolio',
            'en' => 'https://hafizhrafid.dev/en/portfolio',
            'x-default' => 'https://hafizhrafid.dev/en/portfolio',
        ]);
});

test('it auto-resolves hreflang alternates on root for crawlers', function (): void {
    $resolver = new HreflangResolver;

    expect($resolver->alternates(Request::create('https://hafizhrafid.dev/')))
        ->toBe([
            'id' => 'https://hafizhrafid.dev/id',
            'en' => 'https://hafizhrafid.dev/en',
            'x-default' => 'https://hafizhrafid.dev/en',
        ]);
});

test('it returns empty alternates for non-locale paths', function (): void {
    $resolver = new HreflangResolver;

    expect($resolver->alternates(Request::create('https://hafizhrafid.dev/admin/articles')))
        ->toBe([]);
});

test('it emits hreflang alternates when both published translations are explicitly provided', function (): void {
    $resolver = new HreflangResolver;
    $request = Request::create('https://hafizhrafid.dev/id/blog/arsitektur-web');

    expect($resolver->alternates($request, [
        'id' => '/id/blog/arsitektur-web',
        'en' => '/en/blog/web-architecture',
    ]))->toBe([
        'id' => 'https://hafizhrafid.dev/id/blog/arsitektur-web',
        'en' => 'https://hafizhrafid.dev/en/blog/web-architecture',
        'x-default' => 'https://hafizhrafid.dev/en/blog/web-architecture',
    ]);
});

test('it omits hreflang when only one locale is published or alternates are disabled', function (array $explicit): void {
    $resolver = new HreflangResolver;
    $request = Request::create('https://hafizhrafid.dev/id/blog/hanya-indonesia');

    expect($resolver->alternates($request, $explicit))->toBe([]);
})->with([
    'empty array (disabled)' => [[]],
    'single id locale only' => [['id' => '/id/blog/hanya-indonesia']],
    'single en locale only' => [['en' => '/en/blog/english-only']],
    'second locale is empty string' => [['id' => '/id/blog/hanya-indonesia', 'en' => '']],
    'second locale is null' => [['id' => '/id/blog/hanya-indonesia', 'en' => null]],
    'unsupported locale key ignored' => [['id' => '/id/blog/hanya-indonesia', 'fr' => '/fr/blog/only']],
]);
