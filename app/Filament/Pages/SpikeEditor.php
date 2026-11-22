<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Kahusoftware\FilamentCkeditorField\CKEditor;

/**
 * @property Schema $form
 */
class SpikeEditor extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static ?string $title = 'Uji Coba CKEditor';

    protected string $view = 'filament.pages.spike-editor';

    public ?array $data = [];

    public ?string $outputHtml = null;

    public function mount(): void
    {
        $this->form->fill([
            'content' => '<h2>Uji Coba CKEditor 5</h2><p>Gunakan toolbar di atas untuk mengetes semua fitur yang tersedia.</p>',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                CKEditor::make('content')
                    ->label('Konten Editor (Semua Fitur)')
                    // Opsional: aktifkan jika ingin mengetes upload gambar ke route custom
                    ->uploadUrl(route('ckeditor.upload'))
                    ->columnSpanFull()
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $state = $this->form->getState();
        $this->outputHtml = $state['content'];

        Notification::make()
            ->title('Konten berhasil dirender di bawah editor')
            ->success()
            ->send();
    }
}
