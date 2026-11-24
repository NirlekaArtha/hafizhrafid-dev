<?php

declare(strict_types=1);

namespace App\Domain\Localization\Services;

use App\Domain\Localization\Contracts\TranslatorInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiTranslator implements TranslatorInterface
{
    /** @var array{prompt_tokens: int, candidate_tokens: int, total_tokens: int}|null */
    public ?array $lastUsage = null;

    public function __construct(
        private readonly string $apiKey,
        private readonly string $model = 'gemini-3.5-flash-lite',
    ) {}

    public function translateHtml(string $html, string $sourceLocale, string $targetLocale): string
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $sourceName = $this->localeDisplayName($sourceLocale);
        $targetName = $this->localeDisplayName($targetLocale);

        $endpoint = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent',
            $this->model
        );

        $systemInstruction = <<<PROMPT
You are a senior software engineer translating a technical engineering blog article from {$sourceName} to {$targetName}.
Rules:
1. Preserve the exact HTML structure and all HTML tags.
2. NEVER translate, modify, split, or remove any placeholder token matching `[[X0]]`, `[[X1]]`, `[[X2]]`, etc. Keep them verbatim in their exact position (including inside `href` or `src` attributes).
3. Keep standard software engineering terms natural for {$targetName} readers (e.g., keep identifiers/API names intact).
4. Output ONLY the translated HTML fragment. Do not wrap in markdown code fences.
PROMPT;

        $response = Http::withHeaders([
            'x-goog-api-key' => $this->apiKey,
        ])
            ->timeout(45)
            ->post($endpoint, [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $html]],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Gemini API error ('.$response->status().'): '.$response->body());
        }

        $usage = $response->json('usageMetadata');
        if (is_array($usage)) {
            $this->lastUsage = [
                'prompt_tokens' => (int) ($usage['promptTokenCount'] ?? 0),
                'candidate_tokens' => (int) ($usage['candidatesTokenCount'] ?? 0),
                'total_tokens' => (int) ($usage['totalTokenCount'] ?? 0),
            ];
        }

        $text = $response->json('candidates.0.content.parts.0.text');
        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Gemini API returned an empty translation.');
        }

        return trim($text);
    }

    private function localeDisplayName(string $locale): string
    {
        return match (strtolower($locale)) {
            'id' => 'Indonesian',
            'en' => 'English',
            default => $locale,
        };
    }
}
