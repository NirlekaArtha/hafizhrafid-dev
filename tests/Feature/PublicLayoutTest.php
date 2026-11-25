<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

test('the public layout exposes landmarks, a skip link and one h1', function () {
    $html = $this->get(route('home'))->assertOk()->getContent();

    expect($html)
        ->toContain('<html lang="id"')
        ->toContain('<header')
        ->toContain('<nav aria-label="'.__('layout.nav.label').'"')
        ->toContain('<main id="main"')
        ->toContain('<footer')
        ->toContain('href="#main"')
        ->toContain(__('layout.skip_to_content'))
        ->and(substr_count($html, '<h1'))->toBe(1);
});

test('the theme toggle is a button with an accessible name', function () {
    $this->get(route('home'))
        ->assertSee('<button', false)
        ->assertSee('data-theme-toggle', false)
        ->assertSee('aria-label="'.__('layout.theme.system').'"', false);
});

test('navigation only links to routes that exist', function () {
    $html = $this->get(route('home'))->getContent();

    expect($html)
        ->toContain('href="'.route('home').'"')
        ->not->toContain(__('layout.nav.portfolio'))
        ->not->toContain(__('layout.nav.blog'));
});

test('the footer links to the profile and has no rss link', function () {
    $html = $this->get(route('home'))->getContent();

    expect($html)
        ->toContain(config('site.social.github'))
        ->toContain(config('site.social.linkedin'))
        ->toContain('mailto:'.config('site.social.email'))
        ->not->toContain('feed.xml')
        ->not->toContain('RSS');
});

test('the layout renders in english', function () {
    app()->setLocale('en');

    $this->get(route('home'))
        ->assertSee('<html lang="en"', false)
        ->assertSee(__('layout.skip_to_content', [], 'en'));
});

test('every translation file has the same keys in both languages', function () {
    foreach (File::files(lang_path('id')) as $file) {
        $name = $file->getFilenameWithoutExtension();

        expect(array_keys(Arr::dot(trans($name, [], 'en'))))
            ->toEqualCanonicalizing(array_keys(Arr::dot(trans($name, [], 'id'))));
    }
});
