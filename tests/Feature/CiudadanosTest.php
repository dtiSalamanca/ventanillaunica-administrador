<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class CiudadanosTest extends TestCase
{
    use RefreshDatabase;

    public function test_ciudadanos_index_renders_for_authenticated_ad_user(): void
    {
        $this->authenticateAdUser();

        $response = $this->get(route('indexCiudadanos'));

        $response
            ->assertOk()
            ->assertViewIs('ciudadanos.indexCiudadanos');
    }

    public function test_get_ciudadanos_returns_active_and_blocked_citizens(): void
    {
        $this->authenticateAdUser();

        $activo = User::factory()->create([
            'name' => 'Ciudadana Activa',
            'email' => 'activa@example.com',
        ]);

        $bloqueado = User::factory()->create([
            'name' => 'Ciudadano Bloqueado',
            'email' => 'bloqueado@example.com',
            'bloqueado' => true,
        ]);

        $response = $this->getJson(route('getCiudadanos'));

        $response
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'id' => $activo->id,
                'nombre_completo' => 'Ciudadana Activa',
                'email' => 'activa@example.com',
                'activo' => true,
                'bloqueado' => false,
            ])
            ->assertJsonFragment([
                'id' => $bloqueado->id,
                'nombre_completo' => 'Ciudadano Bloqueado',
                'email' => 'bloqueado@example.com',
                'activo' => false,
                'bloqueado' => true,
            ]);
    }

    public function test_get_ciudadanos_returns_empty_list_when_no_citizens(): void
    {
        $this->authenticateAdUser();

        $response = $this->getJson(route('getCiudadanos'));

        $response
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_bloquear_ciudadanos_updates_bloqueado(): void
    {
        $this->authenticateAdUser();

        $ciudadano = User::factory()->create();

        $response = $this->postJson(route('bloquearCiudadanos'), [
            'ciudadanos' => [$ciudadano->id],
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => '1 ciudadano(s) bloqueado(s) correctamente.',
            ]);

        $this->assertTrue($ciudadano->fresh()->bloqueado);
    }

    public function test_desbloquear_ciudadanos_updates_bloqueado(): void
    {
        $this->authenticateAdUser();

        $ciudadano = User::factory()->create(['bloqueado' => true]);

        $response = $this->postJson(route('desbloquearCiudadanos'), [
            'ciudadanos' => [$ciudadano->id],
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => '1 ciudadano(s) desbloqueado(s) correctamente.',
            ]);

        $this->assertFalse($ciudadano->fresh()->bloqueado);
    }

    public function test_bloquear_ciudadanos_redirects_back_when_validation_fails(): void
    {
        $this->authenticateAdUser();

        $response = $this->post(route('bloquearCiudadanos'), [
            'ciudadanos' => ['no-existe'],
        ]);

        $response
            ->assertRedirect()
            ->assertSessionHasErrors('ciudadanos.0');
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
