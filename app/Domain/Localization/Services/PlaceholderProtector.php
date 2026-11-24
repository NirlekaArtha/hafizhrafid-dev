<?php

declare(strict_types=1);

namespace App\Domain\Localization\Services;

use DOMDocument;
use DOMElement;
use DOMXPath;
use RuntimeException;

class PlaceholderProtector
{
    /**
     * Replace <pre>, <code>, and URL attributes with simple, LLM-safe tokens like [[X0]].
     *
     * @return array{html: string, placeholders: array<string, array{type: 'html'|'url', value: string}>}
     */
    public function protect(string $html): array
    {
        if (trim($html) === '') {
            return ['html' => $html, 'placeholders' => []];
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previousErrorSetting = libxml_use_internal_errors(true);

        $wrapped = '<?xml encoding="utf-8" ?><div id="ph-root">'.$html.'</div>';
        $dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorSetting);

        $xpath = new DOMXPath($dom);
        $placeholders = [];
        $counter = 0;

        // 1. Protect <pre> blocks first so nested <code> inside <pre> is captured as one unit.
        $preNodes = $xpath->query('//pre');
        if ($preNodes !== false) {
            foreach (iterator_to_array($preNodes) as $node) {
                if (! $node instanceof DOMElement) {
                    continue;
                }

                $id = 'X'.$counter++;
                $placeholders[$id] = [
                    'type' => 'html',
                    'value' => (string) $dom->saveHTML($node),
                ];

                $node->parentNode?->replaceChild($dom->createTextNode("[[{$id}]]"), $node);
            }
        }

        // 2. Protect remaining inline <code> elements.
        $codeNodes = $xpath->query('//code');
        if ($codeNodes !== false) {
            foreach (iterator_to_array($codeNodes) as $node) {
                if (! $node instanceof DOMElement) {
                    continue;
                }

                $id = 'X'.$counter++;
                $placeholders[$id] = [
                    'type' => 'html',
                    'value' => (string) $dom->saveHTML($node),
                ];

                $node->parentNode?->replaceChild($dom->createTextNode("[[{$id}]]"), $node);
            }
        }

        // 3. Protect href and src URLs.
        $urlNodes = $xpath->query('//*[@href or @src]');
        if ($urlNodes !== false) {
            foreach ($urlNodes as $node) {
                if (! $node instanceof DOMElement) {
                    continue;
                }

                foreach (['href', 'src'] as $attribute) {
                    if (! $node->hasAttribute($attribute)) {
                        continue;
                    }

                    $value = $node->getAttribute($attribute);
                    if ($value === '') {
                        continue;
                    }

                    $id = 'X'.$counter++;
                    $placeholders[$id] = [
                        'type' => 'url',
                        'value' => $value,
                    ];

                    $node->setAttribute($attribute, "[[{$id}]]");
                }
            }
        }

        $root = $dom->getElementById('ph-root');
        $protectedHtml = '';

        if ($root !== null) {
            foreach ($root->childNodes as $child) {
                $protectedHtml .= (string) $dom->saveHTML($child);
            }
        }

        return [
            'html' => $protectedHtml,
            'placeholders' => $placeholders,
        ];
    }

    /**
     * Restore protected <pre>, <code>, and URL placeholders into translated HTML.
     *
     * @param  array<string, array{type: 'html'|'url', value: string}>  $placeholders
     */
    public function restore(string $translatedHtml, array $placeholders): string
    {
        if ($placeholders === []) {
            return $translatedHtml;
        }

        $missing = [];
        $restored = $translatedHtml;

        foreach ($placeholders as $id => $item) {
            // Tolerate accidental spaces or lowercase from lightweight models, e.g. "[[ x0 ]]"
            $pattern = '/\[\[\s*'.preg_quote($id, '/').'\s*\]\]/i';

            $replacement = $item['type'] === 'url'
                ? htmlspecialchars($item['value'], ENT_QUOTES | ENT_HTML5, 'UTF-8')
                : $item['value'];

            $escapedReplacement = str_replace(['\\', '$'], ['\\\\', '\\$'], $replacement);
            $count = 0;
            $updated = preg_replace($pattern, $escapedReplacement, $restored, -1, $count);

            if ($updated === null || $count === 0) {
                $missing[] = "[[{$id}]]";

                continue;
            }

            $restored = $updated;
        }

        if ($missing !== []) {
            throw new RuntimeException('Missing or corrupted translation placeholders: '.implode(', ', $missing));
        }

        return $restored;
    }
}
