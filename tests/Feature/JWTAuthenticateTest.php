<?php

declare(strict_types=1);

namespace MoonShine\JWT\Tests\Feature;

use Illuminate\Testing\TestResponse;
use MoonShine\JWT\Tests\TestCase;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use PHPUnit\Framework\Attributes\Test;

final class JWTAuthenticateTest extends TestCase
{
    private function tokenRequest(bool $invalid = false): TestResponse
    {
        return $this->postJson(route('moonshine.authenticate'), [
            'username' => $this->adminUser->email,
            'password' => $invalid ? '' : $this->superAdminPassword(),
        ]);
    }

    #[Test]
    public function it_success_token_generation(): void
    {
        $request = $this->tokenRequest();
        $request->assertOk();

        $token = $request->json('token');

        $this->assertStringContainsString('.', $token);
    }

    #[Test]
    public function it_failed_token_generation(): void
    {
        $request = $this->tokenRequest(invalid: true);

        $request->assertJsonValidationErrors('password');
    }

    #[Test]
    public function it_success_admin_access(): void
    {
        $request = $this->tokenRequest();
        $token = $request->json('token');

        $this->withToken($token)->getJson(
            app(MoonShineUserResource::class)->getRoute('crud.index'),
        )->assertOk();
    }

    #[Test]
    public function it_failed_admin_access(): void
    {
        $this->withToken('failed')->getJson(
            app(MoonShineUserResource::class)->getRoute('crud.index')
        )->assertUnauthorized();

        $this->getJson(
            app(MoonShineUserResource::class)->getRoute('crud.index')
        )->assertUnauthorized();
    }
}
