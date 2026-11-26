<?php

declare(strict_types=1);

namespace App\Domain\Localization\Services;

use App\Domain\Localization\Enums\Locale;
use Illuminate\Http\Request;

class HreflangResolver
{
    /**
     * Resolve the absolute canonical URL for the current request.
     */
    public function canonicalUrl(Request $request, ?string $explicitCanonical = null): string
    {
        if ($explicitCanonical !== null && trim($explicitCanonical) !== '') {
            return $this->toAbsoluteUrl($request, $explicitCanonical);
        }

        $path = $request->getPathInfo();

        if ($path === '/') {
            return $this->toAbsoluteUrl($request, '/'.Locale::default()->value);
        }

        $normalized = rtrim($path, '/');

        return $this->toAbsoluteUrl($request, $normalized === '' ? '/' : $normalized);
    }

    /**
     * Resolve absolute hreflang alternate URLs (including x-default).
     *
     * Emits alternates only when at least two supported locales are present
     * (published translation pairs). Passing an explicit array with fewer than
     * two valid locales (or an empty array) suppresses hreflang output.
     *
     * @param  array<string, string|null>|null  $hreflangUrls
     * @return array<string, string>
     */
    public function alternates(Request $request, ?array $hreflangUrls = null): array
    {
        $raw = $hreflangUrls === null
            ? $this->autoResolvePaths($request)
            : $this->filterExplicitUrls($hreflangUrls);

        if (count($raw) < 2) {
            return [];
        }

        $resolved = [];

        foreach ($raw as $localeCode => $url) {
            $resolved[$localeCode] = $this->toAbsoluteUrl($request, $url);
        }

        $defaultKey = Locale::default()->value;
        $resolved['x-default'] = $resolved[$defaultKey] ?? reset($resolved);

        return $resolved;
    }

    /**
     * Normalize a relative path or absolute URL into an absolute URL for the current host.
     */
    public function toAbsoluteUrl(Request $request, string $url): string
    {
        $url = trim($url);

        if (preg_match('#^https?://#i', $url) === 1) {
            return $url;
        }

        $base = $request->getSchemeAndHttpHost();

        return $base.'/'.ltrim($url, '/');
    }

    /**
     * @return array<string, string>
     */
    private function autoResolvePaths(Request $request): array
    {
        $path = $request->getPathInfo();

        if ($path === '/') {
            return $this->pathsForSuffix('');
        }

        $localesPattern = implode('|', array_map(static fn (string $value): string => preg_quote($value, '#'), Locale::values()));

        if (preg_match('#^/(?:'.$localesPattern.')(/.*)?$#i', $path, $matches) === 1) {
            $suffix = rtrim($matches[1] ?? '', '/');

            return $this->pathsForSuffix($suffix);
        }

        return [];
    }

    /**
     * @return array<string, string>
     */
    private function pathsForSuffix(string $suffix): array
    {
        $paths = [];

        foreach (Locale::cases() as $locale) {
            $paths[$locale->value] = '/'.$locale->value.$suffix;
        }

        return $paths;
    }

    /**
     * @param  array<string, string|null>  $hreflangUrls
     * @return array<string, string>
     */
    private function filterExplicitUrls(array $hreflangUrls): array
    {
        $normalized = [];

        foreach ($hreflangUrls as $key => $url) {
            $locale = Locale::tryFromInput((string) $key);

            if ($locale === null || ! is_string($url)) {
                continue;
            }

            $trimmed = trim($url);

            if ($trimmed === '') {
                continue;
            }

            $normalized[$locale->value] = $trimmed;
        }

        $ordered = [];

        foreach (Locale::cases() as $locale) {
            if (isset($normalized[$locale->value])) {
                $ordered[$locale->value] = $normalized[$locale->value];
            }
        }

        return $ordered;
    }
}
