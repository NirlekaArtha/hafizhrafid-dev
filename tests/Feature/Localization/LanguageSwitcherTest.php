<?php

declare(strict_types=1);

use App\Domain\Localization\Services\LocaleResolver;

test('the public header renders an accessible language switcher', function (): void {
    $html = $this->get('/id')->assertOk()->getContent();

    expect($html)
        ->toContain('aria-label="'.__('layout.language.label', [], 'id').'"')
        ->toContain('aria-current="true"')
        ->toContain('<span class="sr-only">'.__('layout.language.current_id', [], 'id').'</span>')
        ->toContain('hreflang="en"')
        ->toContain('rel="nofollow"')
        ->toContain(route('locale.switch', ['locale' => 'en', 'redirect' => '/en']));
});

test('the language switcher highlights english when viewing english pages', function (): void {
    $html = $this->get('/en')->assertOk()->getContent();

    expect($html)
        ->toContain('aria-label="'.__('layout.language.label', [], 'en').'"')
        ->toContain('aria-current="true"')
        ->toContain('<span class="sr-only">'.__('layout.language.current_en', [], 'en').'</span>')
        ->toContain('hreflang="id"')
        ->toContain(route('locale.switch', ['locale' => 'id', 'redirect' => '/id']));
});

test('switching locale sets the locale cookie for one year and redirects to target url', function (): void {
    $response = $this->get(route('locale.switch', [
        'locale' => 'id',
        'redirect' => '/id',
    ]));

    $response->assertRedirect('/id');
    $response->assertCookie(LocaleResolver::COOKIE_NAME, 'id');

    $cookie = collect($response->headers->getCookies())
        ->first(fn ($item): bool => $item->getName() === LocaleResolver::COOKIE_NAME);

    expect($cookie)->not->toBeNull()
        ->and($cookie->isHttpOnly())->toBeTrue()
        ->and(strtolower((string) $cookie->getSameSite()))->toBe('lax')
        ->and($cookie->getExpiresTime())->toBeGreaterThan(time() + (60 * 60 * 24 * 360));
});

test('switching locale without redirect query falls back to localized home', function (): void {
    $this->get(route('locale.switch', ['locale' => 'id']))
        ->assertRedirect('/id')
        ->assertCookie(LocaleResolver::COOKIE_NAME, 'id');
});

test('switching locale persists preference for subsequent visits to root', function (): void {
    $response = $this->get(route('locale.switch', [
        'locale' => 'id',
        'redirect' => '/id',
    ]));

    $response->assertRedirect('/id');

    $cookie = collect($response->headers->getCookies())
        ->first(fn ($item): bool => $item->getName() === LocaleResolver::COOKIE_NAME);

    expect($cookie)->not->toBeNull();

    $this->withUnencryptedCookie(LocaleResolver::COOKIE_NAME, (string) $cookie->getValue())
        ->withHeaders(['Accept-Language' => 'en-US,en;q=0.9'])
        ->get('/')
        ->assertRedirect('/id');
});

test('switching to an unsupported locale returns 404', function (): void {
    $this->get('/locale/fr')->assertNotFound();
});

test('switching locale rejects open redirect attempts and falls back to localized home', function (string $unsafe): void {
    $this->get('/locale/en?redirect='.urlencode($unsafe))
        ->assertRedirect('/en')
        ->assertCookie(LocaleResolver::COOKIE_NAME, 'en');
})->with([
    'external url' => 'https://evil.test/phish',
    'protocol-relative' => '//evil.test/phish',
    'path traversal' => '/en/../admin',
    'different locale path' => '/id',
    'admin path' => '/admin',
]);
