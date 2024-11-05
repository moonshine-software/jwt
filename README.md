### JWT Authentication for MoonShine

A simple way to switch your admin panel to API mode and interact via tokens

[Documentation](https:://moonshine-laravel.com/docs/3.x/frontend/api#jwt)

## Requirements

- MoonShine 3+
- Laravel 10+
- PHP 8.2+

#### Installation

```shell
composer require moonshine/jwt
```

```shell
php artisan vendor:publish --provider="MoonShine\JWT\Providers\JWTServiceProvider"
```

Add the base64 encoded secret key to the JWT_SECRET variable in the .env file

```dotenv
JWT_SECRET=YOUR_BASE64_SECRET_HERE
```

#### Usage

#### MoonShineServiceProvider

```php
$config
    ->middlewares([])
    ->authPipelines([
        JWTAuthPipe::class,
    ])
    ->authMiddleware(AuthenticateApi::class);
```

