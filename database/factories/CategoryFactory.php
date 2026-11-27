<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Blog\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nameEn = fake()->unique()->words(2, true);
        $nameId = 'Kategori '.fake()->unique()->word();

        return [
            'name' => [
                'id' => Str::title($nameId),
                'en' => Str::title($nameEn),
            ],
            'slug' => [
                'id' => Str::slug($nameId),
                'en' => Str::slug($nameEn),
            ],
            'description' => [
                'id' => fake()->sentence(),
                'en' => fake()->sentence(),
            ],
            'sort_order' => fake()->numberBetween(0, 50),
        ];
    }
}
