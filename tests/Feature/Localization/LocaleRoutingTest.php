<?php

declare(strict_types=1);

test('locale-prefixed home renders in indonesian and links to id home', function (): void {
    $this->get('/id')
        ->assertOk()
        ->assertSee('<html lang="id"', false)
        ->assertSee(__('layout.skip_to_content', [], 'id'))
        ->assertSee('href="'.route('public.home', ['locale' => 'id']).'"', false);
});

test('locale-prefixed home renders in english and links to en home', function (): void {
    $this->get('/en')
        ->assertOk()
        ->assertSee('<html lang="en"', false)
        ->assertSee(__('layout.skip_to_content', [], 'en'))
        ->assertSee('href="'.route('public.home', ['locale' => 'en']).'"', false);
});

test('url locale prefix overrides cookie and headers', function (): void {
    $this->withCookie('locale', 'en')
        ->withHeaders([
            'Accept-Language' => 'en-US,en;q=0.9',
            'CF-IPCountry' => 'US',
        ])
        ->get('/id')
        ->assertOk()
        ->assertSee('<html lang="id"', false);
});

test('unsupported locale prefix returns not found', function (): void {
    $this->get('/fr')->assertNotFound();
});

test('root redirects regular visitors to english by default', function (): void {
    $response = $this->withHeaders([
        'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/131.0.0.0 Safari/537.36',
    ])->get('/');

    $response->assertRedirect('/en');
    expect($response->headers->get('Vary'))->toContain('Accept-Language');
});

test('root redirects regular visitors according to locale cookie', function (): void {
    $this->withCookie('locale', 'id')
        ->withHeaders([
            'Accept-Language' => 'en-US,en;q=0.9',
            'CF-IPCountry' => 'US',
        ])
        ->get('/')
        ->assertRedirect('/id');
});

test('root redirects regular visitors according to accept-language header', function (): void {
    $this->withHeaders([
        'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8',
    ])->get('/')->assertRedirect('/id');

    $this->withHeaders([
        'Accept-Language' => 'en-US,en;q=0.9,id;q=0.5',
    ])->get('/')->assertRedirect('/en');
});

test('root redirects regular visitors according to cf-ipcountry header as fallback', function (): void {
    $this->withHeaders([
        'Accept-Language' => 'fr-FR,fr;q=0.9',
        'CF-IPCountry' => 'ID',
    ])->get('/')->assertRedirect('/id');

    $this->withHeaders([
        'Accept-Language' => 'fr-FR,fr;q=0.9',
        'CF-IPCountry' => 'SG',
    ])->get('/')->assertRedirect('/en');
});

test('root does not redirect crawlers and renders the default english version', function (): void {
    $this->withHeaders([
        'User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        'Accept-Language' => 'id-ID,id;q=0.9',
        'CF-IPCountry' => 'ID',
    ])
        ->get('/')
        ->assertOk()
        ->assertSee('<html lang="en"', false)
        ->assertSee('href="'.route('public.home', ['locale' => 'en']).'"', false);
});

test('non-locale system routes are isolated from locale redirects', function (): void {
    $this->withCookie('locale', 'id')
        ->withHeaders(['Accept-Language' => 'id-ID,id;q=0.9'])
        ->get('/up')
        ->assertOk();

    $this->withCookie('locale', 'id')
        ->withHeaders(['Accept-Language' => 'id-ID,id;q=0.9'])
        ->get('/login')
        ->assertOk();

    $this->withCookie('locale', 'id')
        ->withHeaders(['Accept-Language' => 'id-ID,id;q=0.9'])
        ->get('/admin/login')
        ->assertOk();
});
