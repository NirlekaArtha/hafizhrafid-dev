<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

test('the public layout exposes landmarks, a skip link and one h1', function () {
    $html = $this->get(route('public.home', ['locale' => 'id']))->assertOk()->getContent();

    expect($html)
        ->toContain('<html lang="id"')
        ->toContain('<header')
        ->toContain('<nav aria-label="'.__('layout.nav.label', [], 'id').'"')
        ->toContain('<main id="main"')
        ->toContain('<footer')
        ->toContain('href="#main"')
        ->toContain(__('layout.skip_to_content', [], 'id'))
        ->and(substr_count($html, '<h1'))->toBe(1);
});

test('the theme toggle is a button with an accessible name', function () {
    $this->get(route('public.home', ['locale' => 'id']))
        ->assertSee('<button', false)
        ->assertSee('data-theme-toggle', false)
        ->assertSee('aria-label="'.__('layout.theme.system', [], 'id').'"', false);
});

test('navigation only links to routes that exist', function () {
    $html = $this->get(route('public.home', ['locale' => 'id']))->getContent();

    expect($html)
        ->toContain('href="'.route('public.home', ['locale' => 'id']).'"')
        ->not->toContain(__('layout.nav.portfolio', [], 'id'))
        ->not->toContain(__('layout.nav.blog', [], 'id'));
});

test('the footer links to the profile and has no rss link', function () {
    $html = $this->get(route('public.home', ['locale' => 'id']))->getContent();

    expect($html)
        ->toContain(config('site.social.github'))
        ->toContain(config('site.social.linkedin'))
        ->toContain('mailto:'.config('site.social.email'))
        ->not->toContain('feed.xml')
        ->not->toContain('RSS');
});

test('the layout renders in english', function () {
    $this->get(route('public.home', ['locale' => 'en']))
        ->assertOk()
        ->assertSee('<html lang="en"', false)
        ->assertSee(__('layout.skip_to_content', [], 'en'));
});

test('every translation file has the same keys and non-empty values in both languages', function () {
    $idFiles = collect(File::files(lang_path('id')))
        ->map(fn ($file): string => $file->getFilenameWithoutExtension())
        ->sort()
        ->values()
        ->all();

    $enFiles = collect(File::files(lang_path('en')))
        ->map(fn ($file): string => $file->getFilenameWithoutExtension())
        ->sort()
        ->values()
        ->all();

    expect($enFiles)->toBe($idFiles);

    foreach ($idFiles as $name) {
        $idTranslations = Arr::dot(trans($name, [], 'id'));
        $enTranslations = Arr::dot(trans($name, [], 'en'));

        expect(array_keys($enTranslations))
            ->toEqualCanonicalizing(array_keys($idTranslations));

        foreach ($idTranslations as $key => $value) {
            expect($value)->toBeString()->not->toBe('', "Empty id translation for {$name}.{$key}");
        }

        foreach ($enTranslations as $key => $value) {
            expect($value)->toBeString()->not->toBe('', "Empty en translation for {$name}.{$key}");
        }
    }
});
