<?php

declare(strict_types=1);

namespace App\Domain\Localization\Services;

use App\Domain\Localization\Enums\Locale;
use Illuminate\Http\Request;

class LocaleResolver
{
    public const COOKIE_NAME = 'locale';

    /**
     * Resolve the active locale following TDD §5.1 priority:
     * 1. URL prefix
     * 2. `locale` cookie
     * 3. `Accept-Language` header
     * 4. `CF-IPCountry` header (ID -> id, otherwise -> en)
     * 5. Default (`en`)
     */
    public function resolve(Request $request, ?string $routeLocale = null): Locale
    {
        $urlLocale = $routeLocale ?? $this->extractRouteLocale($request);

        if (($resolved = Locale::tryFromInput($urlLocale)) !== null) {
            return $resolved;
        }

        $cookieValue = $request->cookie(self::COOKIE_NAME);

        if (is_string($cookieValue) && ($resolved = Locale::tryFromInput($cookieValue)) !== null) {
            return $resolved;
        }

        if (($resolved = $this->fromAcceptLanguage($request->header('Accept-Language'))) !== null) {
            return $resolved;
        }

        if (($resolved = $this->fromCountryHeader($request->header('CF-IPCountry'))) !== null) {
            return $resolved;
        }

        return Locale::default();
    }

    private function extractRouteLocale(Request $request): ?string
    {
        $param = $request->route('locale');

        return is_string($param) ? $param : null;
    }

    private function fromAcceptLanguage(?string $header): ?Locale
    {
        if ($header === null || trim($header) === '') {
            return null;
        }

        $candidates = [];

        foreach (explode(',', $header) as $index => $part) {
            $segments = array_map('trim', explode(';', trim($part)));
            $tag = $segments[0];

            if ($tag === '' || $tag === '*') {
                continue;
            }

            $quality = 1.0;

            foreach (array_slice($segments, 1) as $param) {
                if (str_starts_with(strtolower($param), 'q=')) {
                    $rawQ = substr($param, 2);

                    if (is_numeric($rawQ)) {
                        $quality = (float) $rawQ;
                    }
                }
            }

            if ($quality <= 0.0) {
                continue;
            }

            $primarySubtag = preg_split('/[-_]/', $tag)[0] ?? '';
            $locale = Locale::tryFromInput($primarySubtag);

            if ($locale !== null) {
                $candidates[] = [
                    'locale' => $locale,
                    'quality' => $quality,
                    'index' => $index,
                ];
            }
        }

        if ($candidates === []) {
            return null;
        }

        usort($candidates, static function (array $a, array $b): int {
            $qualityComparison = $b['quality'] <=> $a['quality'];

            return $qualityComparison !== 0 ? $qualityComparison : $a['index'] <=> $b['index'];
        });

        return $candidates[0]['locale'];
    }

    private function fromCountryHeader(?string $country): ?Locale
    {
        if ($country === null || trim($country) === '') {
            return null;
        }

        return strtoupper(trim($country)) === 'ID' ? Locale::Id : Locale::En;
    }
}
