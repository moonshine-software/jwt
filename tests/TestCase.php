<?php

namespace MoonShine\JWT\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MoonShine\JWT\Providers\JWTServiceProvider;
use MoonShine\JWT\Testing\TestingServiceProvider;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Models\MoonshineUserRole;
use MoonShine\Laravel\Providers\MoonShineServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected MoonshineUser $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('optimize:clear');

        $this->adminUser = MoonshineUser::factory()
            ->create($this->superAdminAttributes())
            ->load('moonshineUserRole');
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.debug', 'true');
        $app['config']->set('moonshine.cache', 'array');
        $app['config']->set('moonshine-jwt.secret', 'ESq0N8DIYKeAULqWaT4c4XiHJvxnum08MjeG4jELdGI=');
    }

    protected function getPackageProviders($app): array
    {
        return [
            MoonShineServiceProvider::class,
            JWTServiceProvider::class,
            TestingServiceProvider::class,
        ];
    }

    protected function superAdminAttributes(): array
    {
        return [
            'id' => 1,
            'moonshine_user_role_id' => MoonshineUserRole::DEFAULT_ROLE_ID,
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => bcrypt($this->superAdminPassword()),
        ];
    }

    protected function superAdminPassword(): string
    {
        return 'test';
    }
}