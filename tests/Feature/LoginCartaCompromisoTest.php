<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LoginCartaCompromisoTest extends TestCase
{
    private string $adUrl = 'http://ad.test/login';

    private string $cartaUrl = 'http://carta.test';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.active_directory.url' => $this->adUrl,
            'services.active_directory.aplicativo' => '24',
            'services.carta_compromiso.url' => $this->cartaUrl,
            'services.carta_compromiso.token' => 'shared-token',
            'services.carta_compromiso.fail_open' => false,
        ]);
    }

    private function fakeAd(): void
    {
        Http::fake([
            $this->adUrl => Http::response([
                'success' => true,
                'permisos' => [
                    'id_usuario' => 5,
                    'username' => 'desarrollo',
                    'activo' => true,
                ],
            ]),
            $this->cartaUrl.'/api/firmas/verificar' => Http::response(['firmada' => true]),
        ]);
    }

    public function test_login_succeeds_when_user_has_signed_carta_compromiso(): void
    {
        $this->fakeAd();

        $response = $this->post('/login', [
            'username' => 'desarrollo',
            'password' => 'secret-password',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticated('ad');
    }

    public function test_login_is_denied_when_user_has_not_signed_carta_compromiso(): void
    {
        Http::fake([
            $this->adUrl => Http::response([
                'success' => true,
                'permisos' => [
                    'id_usuario' => 5,
                    'username' => 'desarrollo',
                    'activo' => true,
                ],
            ]),
            $this->cartaUrl.'/api/firmas/verificar' => Http::response(['firmada' => false]),
        ]);

        $response = $this->post('/login', [
            'username' => 'desarrollo',
            'password' => 'secret-password',
        ]);

        $response->assertSessionHasErrors('carta_compromiso');
        $this->assertGuest('ad');
    }

    public function test_login_is_denied_fail_closed_when_carta_compromiso_service_is_unreachable(): void
    {
        Http::fake([
            $this->adUrl => Http::response([
                'success' => true,
                'permisos' => [
                    'id_usuario' => 5,
                    'username' => 'desarrollo',
                    'activo' => true,
                ],
            ]),
            $this->cartaUrl.'/api/firmas/verificar' => Http::response(['message' => 'Server error'], 500),
        ]);

        $response = $this->post('/login', [
            'username' => 'desarrollo',
            'password' => 'secret-password',
        ]);

        $response->assertSessionHasErrors('carta_compromiso');
        $this->assertGuest('ad');
    }
}
