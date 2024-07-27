<?php

declare(strict_types=1);

namespace MoonShine\JWT\Testing;

use MoonShine\JWT\Http\Middleware\AuthenticateApi;
use MoonShine\JWT\JWTAuthPipe;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\Laravel\Providers\MoonShineApplicationServiceProvider;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;

final class TestingServiceProvider extends MoonShineApplicationServiceProvider
{
    protected function resources(): array
    {
        return [
            MoonShineUserResource::class,
            MoonShineUserRoleResource::class,
        ];
    }

    protected function configure(MoonShineConfigurator $config): MoonShineConfigurator
    {
        return $config
            ->middlewares([])
            ->authPipelines([
                JWTAuthPipe::class,
            ])
            ->authMiddleware(AuthenticateApi::class);
    }
}
