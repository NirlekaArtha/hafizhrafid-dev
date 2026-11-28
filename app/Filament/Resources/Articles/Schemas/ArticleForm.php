<?php

declare(strict_types=1);

namespace App\Filament\Resources\Articles\Schemas;

use App\Domain\Blog\Enums\ArticleStatus;
use App\Domain\Blog\Models\Article;
use App\Domain\Blog\Models\Category;
use App\Domain\Localization\Enums\Locale;
use Closure;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Throwable;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make(__('admin.articles.sections.content'))
                            ->schema([
                                Select::make('locale')
                                    ->label(__('admin.articles.fields.locale'))
                                    ->options([
                                        Locale::Id->value => __('admin.locales.id'),
                                        Locale::En->value => __('admin.locales.en'),
                                    ])
                                    ->default(Locale::Id->value)
                                    ->required()
                                    ->live()
                                    ->rule(static function (?Article $record): Closure {
                                        return static function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                                            if ($record === null) {
                                                return;
                                            }

                                            $localeValue = self::resolveLocaleValue($value);

                                            $exists = Article::query()
                                                ->where('translation_group_id', $record->translation_group_id)
                                                ->where('locale', $localeValue)
                                                ->whereKeyNot($record->getKey())
                                                ->exists();

                                            if ($exists) {
                                                $fail(__('admin.articles.validation.locale_group_unique'));
                                            }
                                        };
                                    }),
                                TextInput::make('title')
                                    ->label(__('admin.articles.fields.title'))
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(static function (?string $state, Get $get, Set $set): void {
                                        if (filled($get('slug')) || blank($state)) {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));
                                    }),
                                TextInput::make('slug')
                                    ->label(__('admin.articles.fields.slug'))
                                    ->helperText(__('admin.articles.fields.slug_helper'))
                                    ->required()
                                    ->maxLength(255)
                                    ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                                    ->rule(static function (Get $get, ?Article $record): Closure {
                                        return static function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                                            if (! is_string($value) || $value === '') {
                                                return;
                                            }

                                            $localeValue = self::resolveLocaleValue($get('locale'));

                                            $exists = Article::query()
                                                ->where('locale', $localeValue)
                                                ->where('slug', $value)
                                                ->when(
                                                    $record !== null,
                                                    static fn (Builder $query): Builder => $query->whereKeyNot($record->getKey()),
                                                )
                                                ->exists();

                                            if ($exists) {
                                                $fail(__('admin.articles.validation.slug_unique'));
                                            }
                                        };
                                    }),
                                Textarea::make('excerpt')
                                    ->label(__('admin.articles.fields.excerpt'))
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->nullable(),
                                Textarea::make('body_html')
                                    ->label(__('admin.articles.fields.body_html'))
                                    ->helperText(__('admin.articles.fields.body_html_helper'))
                                    ->rows(12)
                                    ->nullable(),
                            ]),
                        Section::make(__('admin.articles.sections.seo'))
                            ->collapsed()
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label(__('admin.articles.fields.meta_title'))
                                    ->maxLength(255)
                                    ->nullable(),
                                Textarea::make('meta_description')
                                    ->label(__('admin.articles.fields.meta_description'))
                                    ->rows(2)
                                    ->maxLength(255)
                                    ->nullable(),
                            ]),
                    ]),
                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make(__('admin.articles.sections.publishing'))
                            ->schema([
                                Select::make('status')
                                    ->label(__('admin.articles.fields.status'))
                                    ->options([
                                        ArticleStatus::Draft->value => __('admin.articles.statuses.draft'),
                                        ArticleStatus::Scheduled->value => __('admin.articles.statuses.scheduled'),
                                        ArticleStatus::Published->value => __('admin.articles.statuses.published'),
                                    ])
                                    ->default(ArticleStatus::Draft->value)
                                    ->required()
                                    ->live(),
                                DateTimePicker::make('published_at')
                                    ->label(__('admin.articles.fields.published_at'))
                                    ->helperText(__('admin.articles.fields.published_at_helper'))
                                    ->seconds(false)
                                    ->required(static fn (Get $get): bool => self::resolveStatusValue($get('status')) === ArticleStatus::Scheduled->value)
                                    ->rule(static function (Get $get): Closure {
                                        return static function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                            if (self::resolveStatusValue($get('status')) !== ArticleStatus::Scheduled->value) {
                                                return;
                                            }

                                            if (blank($value) || (! is_string($value) && ! $value instanceof Carbon)) {
                                                return;
                                            }

                                            try {
                                                $parsed = $value instanceof Carbon ? $value : Carbon::parse($value);
                                            } catch (Throwable) {
                                                return;
                                            }

                                            if (! $parsed->isFuture()) {
                                                $fail(__('admin.articles.validation.published_at_future'));
                                            }
                                        };
                                    }),
                            ]),
                        Section::make(__('admin.articles.sections.taxonomy'))
                            ->schema([
                                Select::make('categories')
                                    ->label(__('admin.articles.fields.categories'))
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
                                    ->getOptionLabelFromRecordUsing(static function (Category $record, Get $get): string {
                                        $locale = self::resolveLocaleValue($get('locale'));

                                        return (string) $record->getTranslation('name', $locale);
                                    })
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                            ]),
                        Section::make(__('admin.articles.sections.cover'))
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('cover')
                                    ->label(__('admin.articles.fields.cover'))
                                    ->helperText(__('admin.articles.fields.cover_helper'))
                                    ->collection('cover')
                                    ->disk('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(5120)
                                    ->nullable(),
                            ]),
                    ]),
            ]);
    }

    private static function resolveLocaleValue(mixed $value): string
    {
        if ($value instanceof Locale) {
            return $value->value;
        }

        return is_string($value) && $value !== '' ? $value : Locale::Id->value;
    }

    private static function resolveStatusValue(mixed $value): string
    {
        if ($value instanceof ArticleStatus) {
            return $value->value;
        }

        return is_string($value) && $value !== '' ? $value : ArticleStatus::Draft->value;
    }
}
