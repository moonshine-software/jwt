<?php

declare(strict_types=1);

namespace MoonShine\JWT\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use MoonShine\JWT\JWTContract;
use MoonShine\Laravel\MoonShineAuth;

class AuthenticateApi extends Middleware
{
    protected function authenticate($request, array $guards): void
    {
        if (! moonshineConfig()->isAuthEnabled()) {
            return;
        }

        $guard = MoonShineAuth::getGuard();

        if(empty($request->bearerToken())) {
            $this->unauthenticated($request, [$guard, ...$guards]);
        }

        try {
            $identity = app(JWTContract::class)->parse($request->bearerToken());
        } catch (InvalidTokenStructure) {
            $this->unauthenticated($request, [$guard, ...$guards]);
        }

        if ($identity === false) {
            $this->unauthenticated($request, [$guard, ...$guards]);
        }

        $guard->loginUsingId($identity);

        $this->auth->shouldUse(MoonShineAuth::getGuardName());
    }

    protected function unauthenticated($request, array $guards): void
    {
        $request->headers->set('accept', 'application/json');

        throw new AuthenticationException(
            'Unauthenticated.',
            $guards,
        );
    }
}
