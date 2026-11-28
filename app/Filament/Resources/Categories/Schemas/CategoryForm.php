<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories\Schemas;

use App\Domain\Blog\Models\Category;
use App\Domain\Localization\Enums\Locale;
use Closure;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make(__('admin.categories.tabs.translations'))
                    ->tabs([
                        self::localeTab(Locale::Id, __('admin.locales.id')),
                        self::localeTab(Locale::En, __('admin.locales.en')),
                    ])
                    ->columnSpanFull(),
                Section::make(__('admin.categories.sections.settings'))
                    ->schema([
                        TextInput::make('sort_order')
                            ->label(__('admin.categories.fields.sort_order'))
                            ->helperText(__('admin.categories.fields.sort_order_helper'))
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->maxValue(2147483647)
                            ->default(0)
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function localeTab(Locale $locale, string $label): Tab
    {
        $code = $locale->value;
        $upperCode = strtoupper($code);

        return Tab::make($label)
            ->schema([
                TextInput::make("name.{$code}")
                    ->label(__('admin.categories.fields.name', ['locale' => $upperCode]))
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(static function (?string $state, Get $get, Set $set) use ($code): void {
                        if (filled($get("slug.{$code}")) || blank($state)) {
                            return;
                        }

                        $set("slug.{$code}", Str::slug($state));
                    }),
                TextInput::make("slug.{$code}")
                    ->label(__('admin.categories.fields.slug', ['locale' => $upperCode]))
                    ->helperText(__('admin.categories.fields.slug_helper'))
                    ->required()
                    ->maxLength(120)
                    ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                    ->rule(static function (?Category $record) use ($code, $upperCode): Closure {
                        return static function (string $attribute, mixed $value, Closure $fail) use ($record, $code, $upperCode): void {
                            if (! is_string($value) || $value === '') {
                                return;
                            }

                            $exists = Category::query()
                                ->where("slug->{$code}", $value)
                                ->when(
                                    $record !== null,
                                    static fn (Builder $query): Builder => $query->whereKeyNot($record->getKey()),
                                )
                                ->exists();

                            if ($exists) {
                                $fail(__('admin.categories.validation.slug_unique', ['locale' => $upperCode]));
                            }
                        };
                    }),
                Textarea::make("description.{$code}")
                    ->label(__('admin.categories.fields.description', ['locale' => $upperCode]))
                    ->rows(3)
                    ->maxLength(500)
                    ->nullable(),
            ]);
    }
}
