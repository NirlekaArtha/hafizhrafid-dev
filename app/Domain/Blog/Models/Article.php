<?php

declare(strict_types=1);

namespace App\Domain\Blog\Models;

use App\Domain\Blog\Enums\ArticleStatus;
use App\Domain\Localization\Enums\Locale;
use App\Domain\Portfolio\Models\Project;
use App\Models\User;
use App\Policies\ArticlePolicy;
use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string $translation_group_id
 * @property Locale $locale
 * @property string $slug
 * @property string $title
 * @property string|null $excerpt
 * @property string|null $body_html
 * @property string|null $body_rendered
 * @property array<int, array<string, mixed>>|null $toc
 * @property ArticleStatus $status
 * @property Carbon|null $published_at
 * @property int $reading_time_min
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property bool $is_machine_translated
 * @property Carbon|null $reviewed_at
 * @property int|null $translated_from_id
 * @property int $view_count
 * @property int $like_count
 * @property int $author_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'translation_group_id',
    'locale',
    'slug',
    'title',
    'excerpt',
    'body_html',
    'body_rendered',
    'toc',
    'status',
    'published_at',
    'reading_time_min',
    'meta_title',
    'meta_description',
    'is_machine_translated',
    'reviewed_at',
    'translated_from_id',
    'author_id',
])]
#[UseFactory(ArticleFactory::class)]
#[UsePolicy(ArticlePolicy::class)]
class Article extends Model implements HasMedia
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    use InteractsWithMedia;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => ArticleStatus::Draft->value,
        'reading_time_min' => 0,
        'is_machine_translated' => false,
        'view_count' => 0,
        'like_count' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'locale' => Locale::class,
            'toc' => 'array',
            'status' => ArticleStatus::class,
            'published_at' => 'datetime',
            'reading_time_min' => 'integer',
            'is_machine_translated' => 'boolean',
            'reviewed_at' => 'datetime',
            'view_count' => 'integer',
            'like_count' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return BelongsTo<self, $this>
     */
    public function translatedFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'translated_from_id');
    }

    /**
     * All articles sharing the same translation_group_id, including this article.
     *
     * @return HasMany<self, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(self::class, 'translation_group_id', 'translation_group_id');
    }

    /**
     * @return BelongsToMany<Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'article_category');
    }

    /**
     * @return BelongsToMany<Project, $this>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'article_project');
    }

    /**
     * @return HasMany<ArticleRevision, $this>
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(ArticleRevision::class);
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query
            ->where('status', ArticleStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function forLocale(Builder $query, Locale|string $locale): void
    {
        $localeValue = $locale instanceof Locale ? $locale->value : $locale;

        $query->where('locale', $localeValue);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->useDisk('public')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('content_images')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->performOnCollections('cover')
            ->fit(Fit::Crop, 1200, 675)
            ->format('webp');

        $this->addMediaConversion('thumb')
            ->performOnCollections('cover')
            ->fit(Fit::Crop, 400, 225)
            ->format('webp');
    }
}
