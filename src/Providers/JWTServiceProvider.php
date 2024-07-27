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

        $this->app->instance(JWTContract::class, new JWT(
            config('moonshine-jwt.secret')
        ));
    }
}
