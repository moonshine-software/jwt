<?php

declare(strict_types=1);

namespace MoonShine\JWT\Testing;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\JWT\Http\Middleware\AuthenticateApi;
use MoonShine\JWT\JWTAuthPipe;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;

final class TestingServiceProvider extends ServiceProvider
{
    public function boot(CoreContract $core, MoonShineConfigurator $config): void
    {
        $config
            ->middleware([])
            ->authPipelines([
                JWTAuthPipe::class,
            ])
            ->authMiddleware(AuthenticateApi::class);

        $core->resources([
            MoonShineUserResource::class,
            MoonShineUserRoleResource::class,
        ]);
    }
}
