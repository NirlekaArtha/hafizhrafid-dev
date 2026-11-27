<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Blog\Enums\ArticleStatus;
use App\Domain\Blog\Models\Article;
use App\Domain\Localization\Enums\Locale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6);
        $body = '<p>'.fake()->paragraphs(3, true).'</p>';

        return [
            'translation_group_id' => (string) Str::uuid(),
            'locale' => Locale::En->value,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 9999),
            'title' => $title,
            'excerpt' => fake()->sentence(16),
            'body_html' => $body,
            'body_rendered' => $body,
            'toc' => [],
            'status' => ArticleStatus::Draft->value,
            'published_at' => null,
            'reading_time_min' => fake()->numberBetween(2, 12),
            'meta_title' => $title,
            'meta_description' => fake()->sentence(16),
            'is_machine_translated' => false,
            'reviewed_at' => null,
            'translated_from_id' => null,
            'view_count' => 0,
            'like_count' => 0,
            'author_id' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ArticleStatus::Published->value,
            'published_at' => now()->subHour(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ArticleStatus::Scheduled->value,
            'published_at' => now()->addDay(),
        ]);
    }

    public function forLocale(Locale $locale): static
    {
        return $this->state(fn (array $attributes): array => [
            'locale' => $locale->value,
        ]);
    }
}
