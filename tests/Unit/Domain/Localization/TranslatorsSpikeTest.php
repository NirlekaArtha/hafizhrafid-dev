<?php

declare(strict_types=1);

use App\Domain\Localization\Services\DeepLTranslator;
use App\Domain\Localization\Services\GeminiTranslator;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class);

it('sends html tag_handling payload to DeepL free endpoint when key ends with :fx', function (): void {
    Http::fake([
        'https://api-free.deepl.com/v2/translate' => Http::response([
            'translations' => [
                ['text' => '<p>Hello [[X0]]</p>'],
            ],
        ], 200),
    ]);

    $translator = new DeepLTranslator('fake-key:fx');
    $result = $translator->translateHtml('<p>Halo [[X0]]</p>', 'id', 'en');

    expect($result)->toBe('<p>Hello [[X0]]</p>');

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://api-free.deepl.com/v2/translate'
            && $request['source_lang'] === 'ID'
            && $request['target_lang'] === 'EN-US'
            && $request['tag_handling'] === 'html';
    });
});

it('sends structured instruction and captures token usage from Gemini API', function (): void {
    Http::fake([
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => '<p>Hello [[X0]]</p>'],
                        ],
                    ],
                ],
            ],
            'usageMetadata' => [
                'promptTokenCount' => 120,
                'candidatesTokenCount' => 30,
                'totalTokenCount' => 150,
            ],
        ], 200),
    ]);

    $translator = new GeminiTranslator('fake-gemini-key', 'gemini-3.5-flash-lite');
    $result = $translator->translateHtml('<p>Halo [[X0]]</p>', 'id', 'en');

    expect($result)->toBe('<p>Hello [[X0]]</p>')
        ->and($translator->lastUsage)->toBe([
            'prompt_tokens' => 120,
            'candidate_tokens' => 30,
            'total_tokens' => 150,
        ]);
});
