<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Localization\Enums\Locale;
use App\Domain\Localization\Services\LocaleResolver;
use App\Domain\Localization\Services\LocaleUrlResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleSwitchController extends Controller
{
    public const COOKIE_MINUTES = 60 * 24 * 365;

    public function __construct(
        private readonly LocaleUrlResolver $localeUrlResolver,
    ) {}

    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        $targetLocale = Locale::tryFromInput($locale);

        abort_if($targetLocale === null, 404);

        $redirectParam = $request->query('redirect');
        $targetUrl = $this->localeUrlResolver->sanitizeTargetUrl(
            is_string($redirectParam) ? $redirectParam : null,
            $targetLocale,
        );

        $cookie = cookie(
            name: LocaleResolver::COOKIE_NAME,
            value: $targetLocale->value,
            minutes: self::COOKIE_MINUTES,
            path: '/',
            domain: null,
            secure: $request->isSecure(),
            httpOnly: true,
            raw: false,
            sameSite: 'lax',
        );

        return redirect()
            ->to($targetUrl, 302)
            ->withCookie($cookie);
    }
}
