<?php

declare(strict_types=1);

use App\Domain\Localization\Services\PlaceholderProtector;

it('protects and restores pre blocks, inline code, and urls verbatim', function (): void {
    $protector = new PlaceholderProtector;

    $html = '<h2>Judul Artikel</h2>'
        .'<p>Gunakan <code>Cache::lock()</code> dan lihat <a href="https://example.com/docs?a=1&amp;b=2">dokumentasi</a>.</p>'
        .'<pre><code class="language-php">$x = "hello";'
        ."\n".'echo $x;</code></pre>'
        .'<img src="https://example.com/img.png" alt="Diagram">';

    $protected = $protector->protect($html);

    expect($protected['html'])
        ->not->toContain('Cache::lock()')
        ->not->toContain('$x = "hello";')
        ->not->toContain('https://example.com/docs')
        ->not->toContain('https://example.com/img.png')
        ->toContain('[[X0]]')
        ->toContain('[[X1]]')
        ->toContain('[[X2]]')
        ->toContain('[[X3]]');

    // Simulate translation of surrounding prose only.
    $translated = str_replace(
        ['Judul Artikel', 'Gunakan', 'dan lihat', 'dokumentasi', 'alt="Diagram"'],
        ['Article Title', 'Use', 'and see', 'documentation', 'alt="Diagram EN"'],
        $protected['html']
    );

    $restored = $protector->restore($translated, $protected['placeholders']);

    expect($restored)
        ->toContain('<h2>Article Title</h2>')
        ->toContain('<code>Cache::lock()</code>')
        ->toContain('<pre><code class="language-php">$x = "hello";'."\n".'echo $x;</code></pre>')
        ->toContain('href="https://example.com/docs?a=1&amp;b=2"')
        ->toContain('src="https://example.com/img.png"')
        ->toContain('alt="Diagram EN"');
});

it('throws an exception when a provider drops or corrupts a placeholder', function (): void {
    $protector = new PlaceholderProtector;

    $protected = $protector->protect('<p>Run <code>composer test</code> now.</p>');
    $corruptedTranslation = '<p>Jalankan sekarang.</p>';

    expect(fn (): string => $protector->restore($corruptedTranslation, $protected['placeholders']))
        ->toThrow(RuntimeException::class, 'Missing or corrupted translation placeholders');
});
