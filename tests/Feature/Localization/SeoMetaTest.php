<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

test('indonesian public pages render localized seo meta, canonical, hreflang, open graph, and twitter tags', function (): void {
    $html = $this->get('/id?utm_source=test')->assertOk()->getContent();
    $siteName = config('app.name');
    $expectedDescription = __('layout.meta.default_description', [], 'id');
    $idUrl = url('/id');
    $enUrl = url('/en');

    expect($html)
        ->toContain('<title>'.$siteName.'</title>')
        ->toContain('<meta name="description" content="'.$expectedDescription.'">')
        ->toContain('<link rel="canonical" href="'.$idUrl.'">')
        ->toContain('<link rel="alternate" hreflang="id" href="'.$idUrl.'">')
        ->toContain('<link rel="alternate" hreflang="en" href="'.$enUrl.'">')
        ->toContain('<link rel="alternate" hreflang="x-default" href="'.$enUrl.'">')
        ->toContain('<meta property="og:site_name" content="'.$siteName.'">')
        ->toContain('<meta property="og:type" content="website">')
        ->toContain('<meta property="og:title" content="'.$siteName.'">')
        ->toContain('<meta property="og:description" content="'.$expectedDescription.'">')
        ->toContain('<meta property="og:url" content="'.$idUrl.'">')
        ->toContain('<meta property="og:locale" content="id_ID">')
        ->toContain('<meta property="og:locale:alternate" content="en_US">')
        ->toContain('<meta name="twitter:card" content="summary">')
        ->toContain('<meta name="twitter:title" content="'.$siteName.'">')
        ->toContain('<meta name="twitter:description" content="'.$expectedDescription.'">')
        ->not->toContain('og:image')
        ->not->toContain('twitter:image')
        ->not->toContain('name="robots"');
});

test('english public pages render english seo meta and alternate locale', function (): void {
    $html = $this->get('/en')->assertOk()->getContent();
    $expectedDescription = __('layout.meta.default_description', [], 'en');
    $enUrl = url('/en');

    expect($html)
        ->toContain('<meta name="description" content="'.$expectedDescription.'">')
        ->toContain('<link rel="canonical" href="'.$enUrl.'">')
        ->toContain('<meta property="og:locale" content="en_US">')
        ->toContain('<meta property="og:locale:alternate" content="id_ID">');
});

test('crawler requests to root canonicalize to default locale and include full hreflang', function (): void {
    $html = $this->withHeaders(['User-Agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)'])
        ->get('/')
        ->assertOk()
        ->getContent();

    $idUrl = url('/id');
    $enUrl = url('/en');

    expect($html)
        ->toContain('<link rel="canonical" href="'.$enUrl.'">')
        ->toContain('<link rel="alternate" hreflang="id" href="'.$idUrl.'">')
        ->toContain('<link rel="alternate" hreflang="en" href="'.$enUrl.'">')
        ->toContain('<link rel="alternate" hreflang="x-default" href="'.$enUrl.'">')
        ->toContain('<meta property="og:url" content="'.$enUrl.'">');
});

test('seo-meta component renders article metadata, image tags, and custom robots when provided', function (): void {
    app()->setLocale('id');

    $html = Blade::render(
        <<<'BLADE'
        <x-public.seo-meta
            title="Membangun Blog Dua Bahasa"
            description="Catatan arsitektur Laravel modular monolith."
            canonical="/id/blog/membangun-blog"
            og-type="article"
            og-image="/storage/cover.webp"
            og-image-alt="Diagram arsitektur"
            robots="noindex, follow"
            published-time="2026-11-26T08:00:00+00:00"
            modified-time="2026-11-26T09:00:00+00:00"
            :hreflang-urls="['id' => '/id/blog/membangun-blog', 'en' => '/en/blog/building-bilingual-blog']"
        />
        BLADE
    );

    $expectedTitle = 'Membangun Blog Dua Bahasa | '.config('app.name');

    expect($html)
        ->toContain('<title>'.$expectedTitle.'</title>')
        ->toContain('<meta name="description" content="Catatan arsitektur Laravel modular monolith.">')
        ->toContain('<meta name="robots" content="noindex, follow">')
        ->toContain('<link rel="canonical" href="'.url('/id/blog/membangun-blog').'">')
        ->toContain('<link rel="alternate" hreflang="id" href="'.url('/id/blog/membangun-blog').'">')
        ->toContain('<link rel="alternate" hreflang="en" href="'.url('/en/blog/building-bilingual-blog').'">')
        ->toContain('<link rel="alternate" hreflang="x-default" href="'.url('/en/blog/building-bilingual-blog').'">')
        ->toContain('<meta property="og:type" content="article">')
        ->toContain('<meta property="og:image" content="'.url('/storage/cover.webp').'">')
        ->toContain('<meta property="og:image:alt" content="Diagram arsitektur">')
        ->toContain('<meta property="article:published_time" content="2026-11-26T08:00:00+00:00">')
        ->toContain('<meta property="article:modified_time" content="2026-11-26T09:00:00+00:00">')
        ->toContain('<meta name="twitter:card" content="summary_large_image">')
        ->toContain('<meta name="twitter:image" content="'.url('/storage/cover.webp').'">')
        ->toContain('<meta name="twitter:image:alt" content="Diagram arsitektur">');
});

test('seo-meta formats DateTimeInterface instances as ISO-8601 for article timestamps', function (): void {
    app()->setLocale('en');

    $publishedAt = new DateTimeImmutable('2026-11-26 08:00:00', new DateTimeZone('UTC'));
    $modifiedAt = new DateTimeImmutable('2026-11-26 09:30:00', new DateTimeZone('UTC'));

    $html = Blade::render(
        <<<'BLADE'
        <x-public.seo-meta
            title="Modular Monolith Notes"
            og-type="article"
            :published-time="$publishedAt"
            :modified-time="$modifiedAt"
            :hreflang-urls="[]"
        />
        BLADE,
        [
            'publishedAt' => $publishedAt,
            'modifiedAt' => $modifiedAt,
        ],
    );

    expect($html)
        ->toContain('<meta property="article:published_time" content="2026-11-26T08:00:00+00:00">')
        ->toContain('<meta property="article:modified_time" content="2026-11-26T09:30:00+00:00">');
});

test('seo-meta omits hreflang and og:locale:alternate for single-locale pages', function (): void {
    app()->setLocale('id');

    $html = Blade::render(
        <<<'BLADE'
        <x-public.seo-meta
            title="Artikel Hanya Indonesia"
            canonical="/id/blog/artikel-saja"
            :hreflang-urls="['id' => '/id/blog/artikel-saja']"
        />
        BLADE
    );

    expect($html)
        ->toContain('<link rel="canonical" href="'.url('/id/blog/artikel-saja').'">')
        ->toContain('<meta property="og:locale" content="id_ID">')
        ->not->toContain('rel="alternate"')
        ->not->toContain('hreflang=')
        ->not->toContain('og:locale:alternate');
});
