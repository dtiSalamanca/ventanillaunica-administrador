<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FirmaCompromisoService
{
    public function haFirmado(string $usuario, int $aplicativo): bool
    {
        $url = $this->verificarUrl();
        $token = $this->token();

        if ($url === '' || $token === '') {
            Log::warning('Falta configurar CARTA_FIRMANTE_URL o CARTA_FIRMANTE_TOKEN.');

            return $this->failOpen();
        }

        $usuario = strtolower(trim($usuario));

        try {
            $response = Http::asJson()
                ->acceptJson()
                ->timeout($this->timeout())
                ->withToken($token)
                ->post($url, [
                    'usuario' => $usuario,
                    'id_aplicacion' => $aplicativo,
                ]);
        } catch (Throwable $exception) {
            Log::warning('No fue posible conectar con el servicio de firmas.', [
                'exception' => $exception->getMessage(),
            ]);

            return $this->failOpen();
        }

        if (! $response->successful()) {
            return $this->failOpen();
        }

        $payload = $response->json();

        if (! is_array($payload) || ! array_key_exists('firmada', $payload)) {
            return $this->failOpen();
        }

        return (bool) $payload['firmada'];
    }

    protected function verificarUrl(): string
    {
        $base = config('services.carta_compromiso.url');

        if (! is_string($base) || trim($base) === '') {
            return '';
        }

        return rtrim(trim($base), '/').'/api/firmas/verificar';
    }

    protected function token(): string
    {
        return trim((string) config('services.carta_compromiso.token'));
    }

    protected function timeout(): int
    {
        $timeout = config('services.carta_compromiso.timeout');

        return is_numeric($timeout) ? (int) $timeout : 10;
    }

    protected function failOpen(): bool
    {
        return (bool) config('services.carta_compromiso.fail_open');
    }
}
