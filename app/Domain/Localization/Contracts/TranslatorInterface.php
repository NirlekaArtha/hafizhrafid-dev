<?php

declare(strict_types=1);

namespace App\Domain\Localization\Contracts;

interface TranslatorInterface
{
    /**
     * Translate an HTML fragment from the source locale to the target locale.
     */
    public function translateHtml(string $html, string $sourceLocale, string $targetLocale): string;
}
