<?php

declare(strict_types=1);

use App\Domain\Localization\Services\LocaleResolver;

test('the privacy page renders in indonesian and english with seo metadata', function (string $locale) {
    $response = $this->get('/'.$locale.'/privacy')->assertOk();
    $html = $response->getContent();

    expect($html)
        ->toContain('<html lang="'.$locale.'"')
        ->toContain('<h1')
        ->toContain(__('privacy.title', [], $locale))
        ->toContain(__('privacy.draft_notice', [], $locale))
        ->toContain('locale')
        ->toContain('theme')
        ->toContain('mailto:'.config('site.social.email'))
        ->toContain('<link rel="canonical" href="'.url('/'.$locale.'/privacy').'"')
        ->toContain('hreflang="id" href="'.url('/id/privacy').'"')
        ->toContain('hreflang="en" href="'.url('/en/privacy').'"')
        ->toContain('hreflang="x-default" href="'.url('/en/privacy').'"')
        ->and(substr_count($html, '<h1'))->toBe(1);
})->with(['id', 'en']);

test('the footer links to the privacy page in the active locale', function (string $locale) {
    $html = $this->get(route('public.home', ['locale' => $locale]))->assertOk()->getContent();
    preg_match('#<footer\b[^>]*>.*?</footer>#s', $html, $matches);
    $footerHtml = $matches[0] ?? '';

    expect($footerHtml)
        ->toContain('href="'.route('public.privacy', ['locale' => $locale]).'"')
        ->toContain(__('layout.footer.privacy', [], $locale));
})->with(['id', 'en']);

test('the 404 page renders in indonesian for missing id paths with noindex and safe links', function () {
    $html = $this->get('/id/halaman-tidak-ada')->assertNotFound()->getContent();

    expect($html)
        ->toContain('<html lang="id"')
        ->toContain('<meta name="robots" content="noindex, follow"')
        ->not->toContain('rel="alternate" hreflang=')
        ->toContain(__('common.errors.not_found_title', [], 'id'))
        ->toContain(__('common.errors.not_found_body', [], 'id'))
        ->toContain('href="'.route('public.home', ['locale' => 'id']).'"')
        ->toContain(__('common.actions.back_to_home', [], 'id'))
        ->not->toContain(__('common.actions.back_to_portfolio', [], 'id'))
        ->not->toContain(__('common.actions.back_to_blog', [], 'id'))
        ->and(substr_count($html, '<h1'))->toBe(1);
});

test('the 404 page renders in english for missing en paths', function () {
    $html = $this->get('/en/missing-page')->assertNotFound()->getContent();

    expect($html)
        ->toContain('<html lang="en"')
        ->toContain('<meta name="robots" content="noindex, follow"')
        ->toContain(__('common.errors.not_found_title', [], 'en'))
        ->toContain('href="'.route('public.home', ['locale' => 'en']).'"')
        ->toContain(__('common.actions.back_to_home', [], 'en'));
});

test('the 404 page resolves locale from headers on non-prefixed missing paths and links language switcher to home', function () {
    $html = $this->withHeaders(['Accept-Language' => 'id-ID,id;q=0.9'])
        ->get('/unknown-route')
        ->assertNotFound()
        ->getContent();

    expect($html)
        ->toContain('<html lang="id"')
        ->toContain(__('common.errors.not_found_title', [], 'id'))
        ->toContain('href="'.route('public.home', ['locale' => 'id']).'"')
        ->toContain('redirect=%2Fen');
});

test('the 404 page honors the encrypted locale cookie on non-prefixed missing paths', function () {
    $response = $this->get(route('locale.switch', [
        'locale' => 'id',
        'redirect' => '/id',
    ]));

    $response->assertRedirect('/id');

    $cookie = collect($response->headers->getCookies())
        ->first(fn ($item): bool => $item->getName() === LocaleResolver::COOKIE_NAME);

    expect($cookie)->not->toBeNull();

    $html = $this->withUnencryptedCookie(LocaleResolver::COOKIE_NAME, (string) $cookie->getValue())
        ->withHeaders(['Accept-Language' => 'en-US,en;q=0.9'])
        ->get('/unknown-route')
        ->assertNotFound()
        ->getContent();

    expect($html)
        ->toContain('<html lang="id"')
        ->toContain(__('common.errors.not_found_title', [], 'id'))
        ->toContain('href="'.route('public.home', ['locale' => 'id']).'"');
});

test('missing paths still return json 404 when the client expects json', function () {
    $this->getJson('/id/halaman-tidak-ada')
        ->assertNotFound()
        ->assertJsonStructure(['message']);
});
