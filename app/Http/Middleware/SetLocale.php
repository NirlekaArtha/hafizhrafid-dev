<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Localization\Enums\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Apply the locale from the URL prefix and set route defaults for localized URL generation.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeLocale = $request->route('locale');
        $locale = is_string($routeLocale) ? Locale::tryFromInput($routeLocale) : null;

        abort_if($locale === null, 404);

        app()->setLocale($locale->value);
        Date::setLocale($locale->value);
        URL::defaults(['locale' => $locale->value]);

        return $next($request);
    }
}
