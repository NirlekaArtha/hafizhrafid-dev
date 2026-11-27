<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Portfolio\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    protected $model = Setting::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => 'profile.'.fake()->unique()->slug(2),
            'value' => [
                'id' => fake()->sentence(),
                'en' => fake()->sentence(),
            ],
        ];
    }
}
