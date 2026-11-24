<?php

declare(strict_types=1);

namespace App\Domain\Localization\Services;

use App\Domain\Localization\Contracts\TranslatorInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DeepLTranslator implements TranslatorInterface
{
    public function __construct(
        private readonly string $apiKey,
    ) {}

    public function translateHtml(string $html, string $sourceLocale, string $targetLocale): string
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('DEEPL_API_KEY is not configured.');
        }

        $endpoint = str_ends_with($this->apiKey, ':fx')
            ? 'https://api-free.deepl.com/v2/translate'
            : 'https://api.deepl.com/v2/translate';

        $response = Http::withHeaders([
            'Authorization' => 'DeepL-Auth-Key '.$this->apiKey,
        ])
            ->timeout(30)
            ->post($endpoint, [
                'text' => [$html],
                'source_lang' => $this->mapSourceLocale($sourceLocale),
                'target_lang' => $this->mapTargetLocale($targetLocale),
                'tag_handling' => 'html',
            ]);

        if ($response->failed()) {
            throw new RuntimeException('DeepL API error ('.$response->status().'): '.$response->body());
        }

        $translated = $response->json('translations.0.text');

        if (! is_string($translated) || $translated === '') {
            throw new RuntimeException('DeepL API returned an empty translation.');
        }

        return $translated;
    }

    private function mapSourceLocale(string $locale): string
    {
        return match (strtolower($locale)) {
            'id' => 'ID',
            'en' => 'EN',
            default => strtoupper($locale),
        };
    }

    private function mapTargetLocale(string $locale): string
    {
        return match (strtolower($locale)) {
            'id' => 'ID',
            'en' => 'EN-US',
            default => strtoupper($locale),
        };
    }
}
