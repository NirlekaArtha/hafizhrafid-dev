<?php

declare(strict_types=1);

namespace App\Domain\Localization\Services;

use App\Domain\Localization\Enums\Locale;
use Illuminate\Http\Request;

class LocaleUrlResolver
{
    /**
     * Resolve the internal target path when switching the current request to another locale.
     *
     * @param  array<string, string>  $localeUrls  Optional per-page explicit paths keyed by locale value.
     */
    public function urlFor(Request $request, Locale $targetLocale, array $localeUrls = []): string
    {
        if (isset($localeUrls[$targetLocale->value]) && $localeUrls[$targetLocale->value] !== '') {
            return $this->sanitizeTargetUrl($localeUrls[$targetLocale->value], $targetLocale);
        }

        $path = $request->getPathInfo();
        $localesPattern = implode('|', array_map(static fn (string $value): string => preg_quote($value, '#'), Locale::values()));

        if (preg_match('#^/(?:'.$localesPattern.')(/.*)?$#i', $path, $matches) === 1) {
            $suffix = $matches[1] ?? '';
            $target = '/'.$targetLocale->value.$suffix;
            $query = $request->getQueryString();

            if ($query !== null && $query !== '') {
                $target .= '?'.$query;
            }

            return $this->sanitizeTargetUrl($target, $targetLocale);
        }

        return $this->defaultPath($targetLocale);
    }

    /**
     * Ensure a redirect target is a safe internal path matching the requested locale prefix.
     */
    public function sanitizeTargetUrl(?string $target, Locale $targetLocale): string
    {
        $fallback = $this->defaultPath($targetLocale);

        if ($target === null) {
            return $fallback;
        }

        $target = trim($target);

        if (
            $target === ''
            || ! str_starts_with($target, '/')
            || str_starts_with($target, '//')
            || str_starts_with($target, '/\\')
            || preg_match('/[\x00-\x1F\x7F]/', $target) === 1
        ) {
            return $fallback;
        }

        $pathOnly = explode('#', explode('?', $target, 2)[0], 2)[0];

        if (str_contains($pathOnly, '\\') || in_array('..', explode('/', $pathOnly), true)) {
            return $fallback;
        }

        $expectedPrefix = '/'.$targetLocale->value;

        if ($pathOnly !== $expectedPrefix && ! str_starts_with($pathOnly, $expectedPrefix.'/')) {
            return $fallback;
        }

        return $target;
    }

    private function defaultPath(Locale $locale): string
    {
        return '/'.$locale->value;
    }
}
