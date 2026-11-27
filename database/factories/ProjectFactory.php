<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Portfolio\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titleEn = fake()->unique()->words(3, true);
        $titleId = 'Proyek '.fake()->unique()->words(2, true);

        return [
            'title' => [
                'id' => Str::title($titleId),
                'en' => Str::title($titleEn),
            ],
            'slug' => [
                'id' => Str::slug($titleId),
                'en' => Str::slug($titleEn),
            ],
            'summary' => [
                'id' => fake()->sentence(),
                'en' => fake()->sentence(),
            ],
            'problem' => [
                'id' => fake()->paragraph(),
                'en' => fake()->paragraph(),
            ],
            'solution' => [
                'id' => fake()->paragraph(),
                'en' => fake()->paragraph(),
            ],
            'role' => [
                'id' => 'Full-stack Developer',
                'en' => 'Full-stack Developer',
            ],
            'technical_decisions' => [
                'id' => fake()->paragraph(),
                'en' => fake()->paragraph(),
            ],
            'result' => [
                'id' => fake()->sentence(),
                'en' => fake()->sentence(),
            ],
            'tech_stack' => ['Laravel', 'PostgreSQL', 'Tailwind CSS'],
            'demo_url' => fake()->url(),
            'repo_url' => 'https://github.com/NirlekaArtha/'.Str::slug($titleEn),
            'is_featured' => false,
            'sort_order' => fake()->numberBetween(0, 50),
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes): array => [
            'published_at' => now()->subDay(),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_featured' => true,
        ]);
    }
}
