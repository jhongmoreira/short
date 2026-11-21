<?php

namespace Database\Factories;

use App\Models\ShortUrl;
use App\Models\Visualization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visualization>
 */
class VisualizationFactory extends Factory
{
    protected $model = Visualization::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url_id' => ShortUrl::factory(),
            'access_date_hour' => now(),
            'device_info' => [
                'os' => fake()->randomElement(['Android', 'iOS', 'Windows', 'macOS', 'Linux']),
                'browser' => fake()->randomElement(['Chrome', 'Safari', 'Firefox', 'Edge']),
                'device' => fake()->randomElement(['mobile', 'desktop', 'tablet']),
            ],
        ];
    }
}
