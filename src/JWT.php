<?php

declare(strict_types=1);

namespace MoonShine\JWT;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoder;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use MoonShine\JWT\Exceptions\JWTExpiredException;
use MoonShine\JWT\Exceptions\JWTValidatorException;

final readonly class JWT implements JWTContract
{
    public function __construct(
        private string $secret,
        private Encoder $encoder = new JoseEncoder(),
    ) {
    }

    /**
     * @throws JWTValidatorException
     * @throws JWTExpiredException
     */
    public function parse(string $token): string
    {
        $parser = new Parser(
            $this->encoder
        );

        $parsedToken = $parser->parse($token);
        $key = InMemory::base64Encoded($this->secret);

        $configuration = Configuration::forSymmetricSigner(
            new Sha256(),
            $key
        );

        $configuration->setValidationConstraints(
            new SignedWith(
                new Sha256(),
                $key
            )
        );

        if (! $configuration->validator()->validate($parsedToken, ...$configuration->validationConstraints())) {
            throw new JWTValidatorException('Validation failed');
        }

        if ($parsedToken->isExpired(now())) {
            throw new JWTExpiredException('Token is expired');
        }

        return $parsedToken
            ->claims()
            ->get('sub');
    }

    public function create(string $id): string
    {
        $builder = new Builder(
            $this->encoder,
            ChainedFormatter::default()
        );

        return $builder
            ->issuedAt(now()->toImmutable())
            ->expiresAt(now()->toImmutable()->addHour())
            ->relatedTo($id)
            ->getToken(new Sha256(), InMemory::base64Encoded($this->secret))
            ->toString();
    }
}
