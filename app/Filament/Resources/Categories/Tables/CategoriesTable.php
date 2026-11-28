<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories\Tables;

use App\Domain\Blog\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('name_id')
                    ->label(__('admin.categories.columns.name_id'))
                    ->state(static fn (Category $record): string => (string) $record->getTranslation('name', 'id'))
                    ->description(static fn (Category $record): string => (string) $record->getTranslation('slug', 'id'))
                    ->searchable(query: static function (Builder $query, string $search): Builder {
                        return $query->where(static function (Builder $nested) use ($search): void {
                            $nested
                                ->whereLike('name->id', "%{$search}%")
                                ->orWhereLike('slug->id', "%{$search}%");
                        });
                    }),
                TextColumn::make('name_en')
                    ->label(__('admin.categories.columns.name_en'))
                    ->state(static fn (Category $record): string => (string) $record->getTranslation('name', 'en'))
                    ->description(static fn (Category $record): string => (string) $record->getTranslation('slug', 'en'))
                    ->searchable(query: static function (Builder $query, string $search): Builder {
                        return $query->where(static function (Builder $nested) use ($search): void {
                            $nested
                                ->whereLike('name->en', "%{$search}%")
                                ->orWhereLike('slug->en', "%{$search}%");
                        });
                    }),
                TextColumn::make('articles_count')
                    ->label(__('admin.categories.columns.articles_count'))
                    ->counts('articles')
                    ->sortable()
                    ->badge()
                    ->alignCenter(),
                TextColumn::make('projects_count')
                    ->label(__('admin.categories.columns.projects_count'))
                    ->counts('projects')
                    ->sortable()
                    ->badge()
                    ->alignCenter(),
                TextColumn::make('sort_order')
                    ->label(__('admin.categories.columns.sort_order'))
                    ->numeric()
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('updated_at')
                    ->label(__('admin.categories.columns.updated_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
