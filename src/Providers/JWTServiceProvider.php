<?php

declare(strict_types=1);

namespace MoonShine\JWT\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\JWT\JWT;
use MoonShine\JWT\JWTContract;

final class JWTServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/moonshine-jwt.php' => config_path('moonshine-jwt.php'),
        ]);

        $this->app->bind(JWTContract::class, fn() => new JWT(
            config('moonshine-jwt.secret')
        ));
    }
}
