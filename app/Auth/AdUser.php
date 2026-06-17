<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class AdUser implements Authenticatable
{
    public function __construct(protected array $attributes) {}

    public function getAuthIdentifierName(): string
    {
        return 'id_usuario';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->attributes['id_usuario'];
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getAuthPassword(): string
    {
        return '';
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void {}

    public function getRememberTokenName(): string
    {
        return '';
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
