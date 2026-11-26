<?php

declare(strict_types=1);

use App\Domain\Localization\Enums\Locale;
use App\Domain\Localization\Services\LocaleResolver;
use Illuminate\Http\Request;

it('prioritizes explicit url locale over cookie, accept-language, and cf-ipcountry', function (): void {
    $resolver = new LocaleResolver;

    $request = Request::create('/en', 'GET', [], ['locale' => 'id'], [], [
        'HTTP_ACCEPT_LANGUAGE' => 'id-ID,id;q=0.9',
        'HTTP_CF_IPCOUNTRY' => 'ID',
    ]);

    expect($resolver->resolve($request, 'en'))->toBe(Locale::En);
});

it('prioritizes locale cookie over accept-language and cf-ipcountry', function (): void {
    $resolver = new LocaleResolver;

    $request = Request::create('/', 'GET', [], ['locale' => 'id'], [], [
        'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.9',
        'HTTP_CF_IPCOUNTRY' => 'US',
    ]);

    expect($resolver->resolve($request))->toBe(Locale::Id);
});

it('ignores an unsupported locale cookie and falls back to accept-language', function (): void {
    $resolver = new LocaleResolver;

    $request = Request::create('/', 'GET', [], ['locale' => 'fr'], [], [
        'HTTP_ACCEPT_LANGUAGE' => 'id-ID,id;q=0.9,en;q=0.5',
    ]);

    expect($resolver->resolve($request))->toBe(Locale::Id);
});

it('resolves locale from accept-language respecting quality weights and regional subtags', function (string $header, Locale $expected): void {
    $resolver = new LocaleResolver;

    $request = Request::create('/', 'GET', [], [], [], [
        'HTTP_ACCEPT_LANGUAGE' => $header,
    ]);

    expect($resolver->resolve($request))->toBe($expected);
})->with([
    'indonesian first' => ['id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7', Locale::Id],
    'english higher weight' => ['id;q=0.5,en-US;q=0.9', Locale::En],
    'indonesian higher weight listed second' => ['en;q=0.6,id-ID;q=0.8', Locale::Id],
    'unsupported primary falls to supported secondary' => ['fr-FR,fr;q=0.9,id;q=0.7', Locale::Id],
    'zero quality ignored' => ['id;q=0,en;q=0.8', Locale::En],
    'all unsupported defaults to en' => ['ja-JP,ja;q=0.9,de;q=0.8', Locale::En],
]);

it('falls back to cf-ipcountry when accept-language has no supported locale', function (string $country, Locale $expected): void {
    $resolver = new LocaleResolver;

    $request = Request::create('/', 'GET', [], [], [], [
        'HTTP_ACCEPT_LANGUAGE' => 'fr-FR,fr;q=0.9',
        'HTTP_CF_IPCOUNTRY' => $country,
    ]);

    expect($resolver->resolve($request))->toBe($expected);
})->with([
    'indonesia uppercase' => ['ID', Locale::Id],
    'indonesia lowercase' => ['id', Locale::Id],
    'singapore' => ['SG', Locale::En],
    'united states' => ['US', Locale::En],
]);

it('defaults to english when no locale signal is present', function (): void {
    $resolver = new LocaleResolver;

    $request = Request::create('/', 'GET');

    expect($resolver->resolve($request))->toBe(Locale::En);
});
