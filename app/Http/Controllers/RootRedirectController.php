<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Localization\Enums\Locale;
use App\Domain\Localization\Services\CrawlerDetector;
use App\Domain\Localization\Services\LocaleResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\URL;

class RootRedirectController extends Controller
{
    public function __construct(
        private readonly LocaleResolver $localeResolver,
        private readonly CrawlerDetector $crawlerDetector,
    ) {}

    public function __invoke(Request $request): View|RedirectResponse
    {
        // Never force-redirect search/social crawlers based on headers or IP (TDD §5.1).
        if ($this->crawlerDetector->isCrawler($request->userAgent())) {
            $defaultLocale = Locale::default();

            app()->setLocale($defaultLocale->value);
            Date::setLocale($defaultLocale->value);
            URL::defaults(['locale' => $defaultLocale->value]);

            return view('welcome');
        }

        $locale = $this->localeResolver->resolve($request);

        return redirect()
            ->route('public.home', ['locale' => $locale->value], 302)
            ->header('Vary', 'Accept-Language, Cookie');
    }
}
