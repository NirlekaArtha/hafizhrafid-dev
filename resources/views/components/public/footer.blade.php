@php
    $social = config('site.social');
    $linkClass = 'inline-flex min-h-11 items-center rounded-md px-1 text-muted underline-offset-4 transition-colors duration-150 hover:text-fg hover:underline';
@endphp

<footer class="mt-16 border-t border-border">
    <div class="page-container flex flex-col gap-x-8 gap-y-2 py-6 sm:flex-row sm:items-center sm:justify-between">
        <nav aria-label="{{ __('layout.footer.label') }}" class="-mx-1 flex flex-wrap gap-x-4">
            <a href="{{ $social['github'] }}" rel="me noopener noreferrer" class="{{ $linkClass }}">{{ __('layout.footer.github') }}</a>
            <a href="{{ $social['linkedin'] }}" rel="me noopener noreferrer" class="{{ $linkClass }}">{{ __('layout.footer.linkedin') }}</a>
            <a href="mailto:{{ $social['email'] }}" class="{{ $linkClass }}">{{ __('layout.footer.email') }}</a>
            @if (Route::has('public.privacy'))
                <a href="{{ route('public.privacy') }}" class="{{ $linkClass }}">{{ __('layout.footer.privacy') }}</a>
            @endif
        </nav>

        <p class="font-mono text-sm text-muted">{{ __('layout.footer.copyright', ['year' => now()->year]) }}</p>
    </div>
</footer>
