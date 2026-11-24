<?php

declare(strict_types=1);

use App\Domain\Blog\Services\PhikiCodeHighlighter;

it('highlights supported languages with light and dark theme support', function (string $language, string $code): void {
    $highlighter = new PhikiCodeHighlighter;

    $html = $highlighter->highlight($code, $language);

    expect($html)
        ->toContain('<pre')
        ->toContain('<code')
        ->toContain('phiki')
        ->toContain('--phiki-dark-color')
        ->toContain('<span');
})->with([
    'php' => ['php', "<?php\n\necho 'Hello, world!';"],
    'js' => ['js', 'const greet = (name) => `Hello, ${name}`;'],
    'ts' => ['ts', 'interface User { id: number; name: string; }'],
    'bash' => ['bash', "#!/usr/bin/env bash\nset -euo pipefail\necho 'ok'"],
    'python' => ['python', "def greet(name: str) -> str:\n    return f'Hello, {name}'"],
    'sql' => ['sql', 'SELECT id, slug FROM articles WHERE status = \'published\';'],
    'yaml' => ['yaml', "services:\n  app:\n    image: php:8.5-fpm-alpine"],
    'dockerfile' => ['dockerfile', "FROM php:8.5-fpm-alpine\nWORKDIR /var/www/html"],
]);

it('falls back safely to escaped pre code for unknown or empty language', function (?string $language): void {
    $highlighter = new PhikiCodeHighlighter;

    $html = $highlighter->highlight('<script>alert("xss")</script>', $language);

    expect($html)
        ->toContain('<pre')
        ->toContain('<code>')
        ->toContain('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;')
        ->not->toContain('<script>');
})->with([
    'null' => [null],
    'empty' => [''],
    'unknown' => ['nonexistent-lang-xyz'],
]);

it('escapes html characters inside highlighted code to prevent xss', function (): void {
    $highlighter = new PhikiCodeHighlighter;

    $html = $highlighter->highlight('<?php $tag = "<script>alert(1)</script>";', 'php');

    expect($html)
        ->not->toContain('<script>')
        ->toContain('&lt;script&gt;');
});
