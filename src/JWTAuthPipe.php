<?php

declare(strict_types=1);

namespace MoonShine\JWT;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
use MoonShine\Laravel\MoonShineAuth;
use Random\RandomException;

final class JWTAuthPipe
{
    /**
     * @throws RandomException
     */
    public function handle(Request $request, $next)
    {
        if ($request->wantsJson()) {
            $token = $this->getAuthToken($request);

            return MoonShineJsonResponse::make([
                'token' => $token,
            ]);
        }

        return $next($request);
    }

    /**
     * @throws RandomException
     */
    public function getAuthToken(Request $request): string
    {
        $this->ensureIsNotRateLimited($request);

        $user = MoonShineAuth::getModel()
            ?->newQuery()
            ->where(
                moonshineConfig()->getUserField('username', 'email'),
                request()->input('username')
            )
            ->first();

        if (is_null($user) || ! Hash::check(
            request()->input('password'),
            $user->{moonshineConfig()->getUserField('password')}
        )) {
            RateLimiter::hit(
                $this->getThrottleKey($request)
            );

            $this->validationException();
        }

        $token = app(JWTContract::class)->create((string) $user->getKey());

        RateLimiter::clear(
            $this->getThrottleKey($request)
        );

        return $token;
    }

    private function validationException(): never
    {
        throw ValidationException::withMessages([
            'username' => __('moonshine::auth.failed'),
        ]);
    }

    public function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->getThrottleKey($request), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn(
            $this->getThrottleKey($request)
        );

        throw ValidationException::withMessages([
            'username' => __('moonshine::auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    private function getThrottleKey(Request $request): string
    {
        return Str::transliterate(
            str($request->input('username') . '|' . $request->ip())
                ->lower()
        );
    }
}
