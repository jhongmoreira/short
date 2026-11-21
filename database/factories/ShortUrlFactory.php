<?php

namespace Database\Factories;

use App\Models\ShortUrl;
use App\Services\PinManager;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ShortUrl>
 */
class ShortUrlFactory extends Factory
{
    protected $model = ShortUrl::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'original_link' => fake()->url(),
            'short_url' => Str::random(6),
            'pin_manager' => app(PinManager::class)->hash(Str::upper(Str::random(8))),
        ];
    }

    /**
     * Define um PIN conhecido (útil nos testes).
     */
    public function withPin(string $pin): static
    {
        return $this->state(fn () => [
            'pin_manager' => app(PinManager::class)->hash($pin),
        ]);
    }
}
