<?php

declare(strict_types=1);

namespace App\Filament\Resources\Articles\Tables;

use App\Domain\Blog\Enums\ArticleStatus;
use App\Domain\Blog\Models\Article;
use App\Domain\Blog\Models\Category;
use App\Domain\Localization\Enums\Locale;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')
                    ->label(__('admin.articles.columns.cover'))
                    ->collection('cover')
                    ->conversion('thumb')
                    ->disk('public')
                    ->imageWidth(64)
                    ->imageHeight(36),
                TextColumn::make('title')
                    ->label(__('admin.articles.columns.title'))
                    ->description(static fn (Article $record): string => $record->slug)
                    ->limit(60)
                    ->searchable(['title', 'slug'])
                    ->sortable(),
                TextColumn::make('locale')
                    ->label(__('admin.articles.columns.locale'))
                    ->badge()
                    ->formatStateUsing(static fn (Locale|string $state): string => strtoupper($state instanceof Locale ? $state->value : $state))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('admin.articles.columns.status'))
                    ->badge()
                    ->formatStateUsing(static function (ArticleStatus|string $state): string {
                        $value = $state instanceof ArticleStatus ? $state->value : $state;

                        return __("admin.articles.statuses.{$value}");
                    })
                    ->color(static function (ArticleStatus|string $state): string {
                        $value = $state instanceof ArticleStatus ? $state->value : $state;

                        return match ($value) {
                            ArticleStatus::Published->value => 'success',
                            ArticleStatus::Scheduled->value => 'warning',
                            default => 'gray',
                        };
                    })
                    ->sortable(),
                TextColumn::make('categories_list')
                    ->label(__('admin.articles.columns.categories'))
                    ->state(static function (Article $record): array {
                        $locale = $record->locale->value;

                        return $record->categories
                            ->map(static fn (Category $category): string => (string) $category->getTranslation('name', $locale))
                            ->all();
                    })
                    ->badge(),
                TextColumn::make('published_at')
                    ->label(__('admin.articles.columns.published_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('updated_at')
                    ->label(__('admin.articles.columns.updated_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('locale')
                    ->label(__('admin.articles.filters.locale'))
                    ->options([
                        Locale::Id->value => __('admin.locales.id'),
                        Locale::En->value => __('admin.locales.en'),
                    ]),
                SelectFilter::make('status')
                    ->label(__('admin.articles.filters.status'))
                    ->options([
                        ArticleStatus::Draft->value => __('admin.articles.statuses.draft'),
                        ArticleStatus::Scheduled->value => __('admin.articles.statuses.scheduled'),
                        ArticleStatus::Published->value => __('admin.articles.statuses.published'),
                    ]),
                SelectFilter::make('categories')
                    ->label(__('admin.articles.filters.categories'))
                    ->relationship(
                        name: 'categories',
                        titleAttribute: 'name',
                        modifyQueryUsing: static function (Builder $query): Builder {
                            if ($query->getQuery()->distinct) {
                                // PostgreSQL cannot SELECT DISTINCT on json columns; deduplicate by primary key instead.
                                $query->distinct(false)->groupBy('categories.id');
                            }

                            return $query->orderBy('categories.sort_order');
                        },
                    )
                    ->getOptionLabelFromRecordUsing(static function (Category $record): string {
                        $locale = (Locale::tryFrom(app()->getLocale()) ?? Locale::Id)->value;

                        return (string) $record->getTranslation('name', $locale);
                    })
                    ->multiple()
                    ->preload(),
            ])
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
