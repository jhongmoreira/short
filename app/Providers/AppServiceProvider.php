<?php

namespace App\Providers;

use App\Services\PinManager;
use App\Services\ShortCodeGenerator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PinManager::class, fn () => new PinManager(
            key: (string) config('app.key'),
            alphabet: (string) config('short.pin_alphabet'),
            length: (int) config('short.pin_length'),
        ));

        $this->app->singleton(ShortCodeGenerator::class, fn () => new ShortCodeGenerator(
            alphabet: (string) config('short.code_alphabet'),
            length: (int) config('short.code_length'),
            reserved: array_values((array) config('short.reserved_codes')),
        ));
    }

    public function boot(): void
    {
        RateLimiter::for('shorten', fn (Request $request) => Limit::perMinute((int) config('short.shorten_per_minute'))
            ->by((string) $request->ip()));

        RateLimiter::for('pin', fn (Request $request) => Limit::perMinute((int) config('short.pin_attempts_per_minute'))
            ->by((string) $request->ip()));
    }
}
