@props([
    'title' => null,
    'description' => null,
    'canonical' => null,
    'ogType' => 'website',
    'ogImage' => null,
    'ogImageAlt' => null,
    'robots' => null,
    'publishedTime' => null,
    'modifiedTime' => null,
    'localeUrls' => [],
    'hreflangUrls' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="system">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <x-public.seo-meta
            :title="$title"
            :description="$description"
            :canonical="$canonical"
            :og-type="$ogType"
            :og-image="$ogImage"
            :og-image-alt="$ogImageAlt"
            :robots="$robots"
            :published-time="$publishedTime"
            :modified-time="$modifiedTime"
            :hreflang-urls="$hreflangUrls"
        />

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        {{-- Runs before first paint so the stored theme is applied without a flash. --}}
        <script>
            (function () {
                try {
                    var stored = localStorage.getItem('theme');
                    var theme = stored === 'light' || stored === 'dark' ? stored : 'system';
                    var dark = theme === 'dark' || (theme === 'system' && matchMedia('(prefers-color-scheme: dark)').matches);
                    document.documentElement.dataset.theme = theme;
                    document.documentElement.classList.toggle('dark', dark);
                } catch (e) {}
            })();
        </script>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-dvh flex-col bg-bg font-sans text-fg antialiased">
        <a href="#main" class="sr-only rounded-md bg-primary px-4 py-2 font-medium text-primary-contrast focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50">
            {{ __('layout.skip_to_content') }}
        </a>

        <x-public.header :locale-urls="$localeUrls" />

        <main id="main" tabindex="-1" class="w-full flex-1 focus:outline-none">
            {{ $slot }}
        </main>

        <x-public.footer />
    </body>
</html>
