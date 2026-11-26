@php
    $storageItems = [
        'locale',
        'theme',
        'session',
    ];
    $contactEmail = (string) config('site.social.email');
@endphp

<x-layouts::public
    :title="__('privacy.title')"
    :description="__('privacy.meta_description')"
>
    <article class="page-container py-12 sm:py-16">
        <div class="max-w-prose">
            <header class="border-b border-border pb-6">
                <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">
                    {{ __('privacy.title') }}
                </h1>
                <p class="mt-3 font-mono text-sm text-muted">
                    {{ __('privacy.last_updated_label') }}: <time datetime="2026-11-26">{{ __('privacy.last_updated_date') }}</time>
                </p>
                <p class="mt-4 rounded-lg border border-border bg-surface px-4 py-3 text-sm leading-6 text-muted">
                    {{ __('privacy.draft_notice') }}
                </p>
            </header>

            <p class="mt-8 text-base leading-7 text-fg">
                {{ __('privacy.intro') }}
            </p>

            <section class="mt-10" aria-labelledby="privacy-principles">
                <h2 id="privacy-principles" class="text-xl font-semibold tracking-tight">
                    {{ __('privacy.sections.principles.heading') }}
                </h2>
                <p class="mt-3 text-base leading-7 text-muted">
                    {{ __('privacy.sections.principles.body') }}
                </p>
            </section>

            <section class="mt-10" aria-labelledby="privacy-storage">
                <h2 id="privacy-storage" class="text-xl font-semibold tracking-tight">
                    {{ __('privacy.sections.storage.heading') }}
                </h2>
                <p class="mt-3 text-base leading-7 text-muted">
                    {{ __('privacy.sections.storage.intro') }}
                </p>

                <dl class="mt-5 divide-y divide-border rounded-lg border border-border bg-surface">
                    @foreach ($storageItems as $item)
                        <div class="px-4 py-4">
                            <dt class="font-mono text-sm font-medium text-fg">
                                {{ __('privacy.sections.storage.items.'.$item.'.key') }}
                            </dt>
                            <dd class="mt-1 text-xs font-medium text-muted">
                                {{ __('privacy.sections.storage.items.'.$item.'.type') }}
                            </dd>
                            <dd class="mt-2 text-sm leading-6 text-muted">
                                {{ __('privacy.sections.storage.items.'.$item.'.description') }}
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            <section class="mt-10" aria-labelledby="privacy-contact-form">
                <h2 id="privacy-contact-form" class="text-xl font-semibold tracking-tight">
                    {{ __('privacy.sections.contact_form.heading') }}
                </h2>
                <p class="mt-3 text-base leading-7 text-muted">
                    {{ __('privacy.sections.contact_form.body') }}
                </p>
            </section>

            <section class="mt-10" aria-labelledby="privacy-future">
                <h2 id="privacy-future" class="text-xl font-semibold tracking-tight">
                    {{ __('privacy.sections.future.heading') }}
                </h2>
                <p class="mt-3 text-base leading-7 text-muted">
                    {{ __('privacy.sections.future.body') }}
                </p>
            </section>

            <section class="mt-10" aria-labelledby="privacy-contact">
                <h2 id="privacy-contact" class="text-xl font-semibold tracking-tight">
                    {{ __('privacy.sections.contact.heading') }}
                </h2>
                <p class="mt-3 text-base leading-7 text-muted">
                    {{ __('privacy.sections.contact.body') }}
                </p>
                <p class="mt-2">
                    <a
                        href="mailto:{{ $contactEmail }}"
                        class="inline-flex min-h-11 items-center rounded-md font-mono text-sm font-medium text-primary underline underline-offset-4 transition-opacity duration-150 hover:opacity-80"
                    >
                        {{ $contactEmail }}
                    </a>
                </p>
            </section>
        </div>
    </article>
</x-layouts::public>
