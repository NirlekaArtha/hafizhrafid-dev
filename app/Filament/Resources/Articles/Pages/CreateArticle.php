<?php

declare(strict_types=1);

namespace App\Filament\Resources\Articles\Pages;

use App\Domain\Blog\Enums\ArticleStatus;
use App\Filament\Resources\Articles\ArticleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['translation_group_id'] ??= (string) Str::uuid();
        $data['author_id'] = Auth::id();

        $status = $data['status'] ?? null;
        $statusValue = $status instanceof ArticleStatus ? $status->value : (string) $status;

        if ($statusValue === ArticleStatus::Published->value && blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
