<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdUserProvider implements UserProvider
{
    public function retrieveById(mixed $identifier): ?Authenticatable
    {
        $data = Cache::get("ad_user_{$identifier}");

        return $data ? new AdUser($data) : null;
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token): void {}

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if (empty($credentials['username']) || empty($credentials['password'])) {
            return null;
        }

        try {
            $response = Http::timeout(10)->post(config('services.active_directory.url'), [
                'usuario' => $credentials['username'],
                'password' => $credentials['password'],
                'aplicativo' => config('services.active_directory.aplicativo'),
            ]);

            if (! $response->successful() || ! $response->json('success')) {
                return null;
            }

            $permisos = $response->json('permisos');

            if (! ($permisos['activo'] ?? false)) {
                return null;
            }

            $permisos[AdUser::EncryptedPasswordAttribute] = Crypt::encryptString($credentials['password']);

            Cache::put("ad_user_{$permisos['id_usuario']}", $permisos, now()->addHours(8));

            return new AdUser($permisos);
        } catch (\Exception $e) {
            Log::error('AD authentication error: '.$e->getMessage());

            return null;
        }
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return $user instanceof AdUser;
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void {}
}
