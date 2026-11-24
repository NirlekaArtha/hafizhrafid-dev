<?php

declare(strict_types=1);

namespace App\Domain\Blog\Contracts;

interface CodeHighlighterInterface
{
    /**
     * Render a code block into syntax-highlighted HTML.
     */
    public function highlight(string $code, ?string $language = null): string;
}
