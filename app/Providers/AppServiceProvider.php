<?php

namespace App\Providers;

use App\Domain\Localization\Enums\Locale;
use App\Domain\Localization\Services\LocaleResolver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View as RenderedView;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureErrorViews();
    }

    /**
     * Resolve locale and safe switcher URLs before rendering custom 404 pages.
     */
    protected function configureErrorViews(): void
    {
        View::composer('errors::404', function (RenderedView $view): void {
            $request = request();
            $firstSegment = explode('/', trim($request->getPathInfo(), '/'))[0];
            $resolvedLocale = app(LocaleResolver::class)->resolve($request, $firstSegment !== '' ? $firstSegment : null);

            app()->setLocale($resolvedLocale->value);
            Date::setLocale($resolvedLocale->value);
            URL::defaults(['locale' => $resolvedLocale->value]);

            // Point the language switcher to the localized home page rather than a missing URL in the other locale.
            $localeUrls = [];

            foreach (Locale::cases() as $localeCase) {
                $localeUrls[$localeCase->value] = '/'.$localeCase->value;
            }

            $view->with('localeUrls', $localeUrls);
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
