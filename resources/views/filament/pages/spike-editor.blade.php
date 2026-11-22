<x-filament-panels::page>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}

        <div>
            <x-filament::button type="submit">
                Lihat Hasil Output HTML
            </x-filament::button>
        </div>
    </form>

    @if ($outputHtml)
        <x-filament::section heading="Hasil Raw HTML">
            <pre class="p-4 overflow-x-auto text-sm bg-gray-900 text-gray-100 rounded-lg">{{ $outputHtml }}</pre>
        </x-filament::section>

        <x-filament::section heading="Preview Render">
            <div class="prose dark:prose-invert max-w-none">
                {!! $outputHtml !!}
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
