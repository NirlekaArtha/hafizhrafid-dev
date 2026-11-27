<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Blog\Models\Article;
use App\Domain\Blog\Models\ArticleRevision;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ArticleRevision>
 */
class ArticleRevisionFactory extends Factory
{
    protected $model = ArticleRevision::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'article_id' => Article::factory(),
            'title' => fake()->sentence(6),
            'body_html' => '<p>'.fake()->paragraphs(2, true).'</p>',
            'created_at' => now(),
        ];
    }
}
