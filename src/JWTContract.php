<?php

declare(strict_types=1);

namespace MoonShine\JWT;

interface JWTContract
{
    public function parse(string $token): string;

    public function create(string $id): string;
}
