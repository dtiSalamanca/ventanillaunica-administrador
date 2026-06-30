<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Auth\AdUserProvider;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UsuariosAdTest extends TestCase
{
    private string $authUrl = 'http://ad.test/login';

    private string $usersUrl = 'http://ad.test/usersApp';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.active_directory.url' => $this->authUrl,
            'services.active_directory.users_url' => $this->usersUrl,
            'services.active_directory.aplicativo' => '24',
        ]);
    }

    public function test_ad_provider_stores_encrypted_password_for_authenticated_user(): void
    {
        Http::fake([
            $this->authUrl => Http::response([
                'success' => true,
                'permisos' => [
                    'id_usuario' => 5,
                    'username' => 'desarrollo',
                    'nombre' => 'JEFATURA',
                    'apaterno' => 'DE',
                    'amaterno' => 'DESARROLLO',
                    'app' => 'SIA',
                    'rol_id' => 35,
                    'id_area' => 31,
                    'rol' => 'admin',
                    'activo' => true,
                ],
            ]),
        ]);

        $user = (new AdUserProvider)->retrieveByCredentials([
            'username' => 'desarrollo',
            'password' => 'secret-password',
        ]);

        $this->assertInstanceOf(AdUser::class, $user);
        $this->assertNotSame('secret-password', $user->getEncryptedPassword());
        $this->assertSame('secret-password', Crypt::decryptString($user->getEncryptedPassword()));
        $this->assertArrayNotHasKey(AdUser::EncryptedPasswordAttribute, $user->toArray());
    }

    public function test_usuarios_index_renders_for_authenticated_ad_user(): void
    {
        $this->authenticateAdUser();

        $response = $this->get(route('indexUsuarios'));

        $response
            ->assertOk()
            ->assertViewIs('usuarios.indexUsuarios');
    }

    public function test_get_usuarios_ad_returns_normalized_users(): void
    {
        $this->authenticateAdUser();

        Http::fake([
            $this->usersUrl => Http::response([
                [
                    'success' => true,
                    'permisos' => [
                        'id_usuario' => 5,
                        'username' => 'desarrollo',
                        'nombre' => 'JEFATURA',
                        'apaterno' => 'DE',
                        'amaterno' => 'DESARROLLO',
                        'app' => 'SIA',
                        'rol_id' => 35,
                        'id_area' => 31,
                        'rol' => 'admin',
                        'activo' => true,
                    ],
                ],
                [
                    'success' => false,
                    'permisos' => [
                        'id_usuario' => 99,
                    ],
                ],
                [
                    'success' => true,
                ],
            ]),
        ]);

        $response = $this->getJson(route('getUsuariosAd'));

        $response
            ->assertOk()
            ->assertExactJson([
                [
                    'id_usuario' => 5,
                    'username' => 'desarrollo',
                    'nombre' => 'JEFATURA',
                    'apaterno' => 'DE',
                    'amaterno' => 'DESARROLLO',
                    'nombre_completo' => 'JEFATURA DE DESARROLLO',
                    'app' => 'SIA',
                    'rol_id' => 35,
                    'id_area' => 31,
                    'rol' => 'admin',
                    'activo' => true,
                ],
            ]);

        Http::assertSent(fn (Request $request): bool => $request->url() === $this->usersUrl
            && $request['usuario'] === 'desarrollo'
            && $request['password'] === 'secret-password'
            && $request['aplicativo'] === '24');
    }

    public function test_get_usuarios_ad_returns_error_when_password_is_missing(): void
    {
        $this->authenticateAdUser([
            'id_usuario' => 5,
            'username' => 'desarrollo',
        ]);

        Http::fake();

        $response = $this->getJson(route('getUsuariosAd'));

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'No se encontró la contraseña de AD en la sesión. Vuelva a iniciar sesión.',
            ]);

        Http::assertNothingSent();
    }

    public function test_get_usuarios_ad_returns_error_when_service_fails(): void
    {
        $this->authenticateAdUser();

        Http::fake([
            $this->usersUrl => Http::response(['message' => 'Server error'], 500),
        ]);

        $response = $this->getJson(route('getUsuariosAd'));

        $response
            ->assertStatus(502)
            ->assertJson([
                'message' => 'El servicio de usuarios AD no respondió correctamente.',
            ]);
    }

    /**
     * @param  array<string, mixed>|null  $attributes
     */
    private function authenticateAdUser(?array $attributes = null): void
    {
        $attributes ??= [
            'id_usuario' => 5,
            'username' => 'desarrollo',
            'nombre' => 'JEFATURA',
            'apaterno' => 'DE',
            'amaterno' => 'DESARROLLO',
            'app' => 'SIA',
            'rol_id' => 35,
            'id_area' => 31,
            'rol' => 'admin',
            'activo' => true,
            AdUser::EncryptedPasswordAttribute => Crypt::encryptString('secret-password'),
        ];

        Cache::put("ad_user_{$attributes['id_usuario']}", $attributes, now()->addHour());

        $this->actingAs(new AdUser($attributes), 'ad');
    }
}
