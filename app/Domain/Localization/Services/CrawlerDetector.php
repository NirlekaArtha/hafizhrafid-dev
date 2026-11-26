<?php

declare(strict_types=1);

namespace App\Domain\Localization\Services;

class CrawlerDetector
{
    /**
     * Known search engine, social preview, and indexing crawler tokens.
     *
     * @var list<string>
     */
    private const CRAWLER_TOKENS = [
        'googlebot',
        'bingbot',
        'slurp',
        'duckduckbot',
        'baiduspider',
        'yandex',
        'sogou',
        'exabot',
        'facebot',
        'facebookexternalhit',
        'ia_archiver',
        'applebot',
        'twitterbot',
        'linkedinbot',
        'slackbot',
        'whatsapp',
        'telegrambot',
        'discordbot',
        'pinterestbot',
        'ahrefsbot',
        'semrushbot',
        'mj12bot',
        'dotbot',
        'petalbot',
        'gptbot',
        'claudebot',
        'anthropic-ai',
        'perplexitybot',
        'oai-searchbot',
        'bytespider',
        'crawler',
        'spider',
    ];

    public function isCrawler(?string $userAgent): bool
    {
        if ($userAgent === null || trim($userAgent) === '') {
            return false;
        }

        $normalized = strtolower($userAgent);

        foreach (self::CRAWLER_TOKENS as $token) {
            if (str_contains($normalized, $token)) {
                return true;
            }
        }

        return false;
    }
}
