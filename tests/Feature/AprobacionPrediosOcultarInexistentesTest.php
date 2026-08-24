<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Models\Predio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class AprobacionPrediosOcultarInexistentesTest extends TestCase
{
    use RefreshDatabase;

    public function test_oculta_los_predios_que_no_existen_en_predial(): void
    {
        $this->authenticateAdUser();

        $usuario = User::factory()->create(['name' => 'Ciudadano Visible']);

        $visible = Predio::create([
            'clave_predio' => '25A000000001',
            'estatus_predio' => Predio::ESTATUS_APROBADO,
            'consultado' => Predio::CONSULTADO_EXISTE,
            'fk_usuario' => $usuario->id,
        ]);

        $inexistente = Predio::create([
            'clave_predio' => '25B000000002',
            'estatus_predio' => Predio::ESTATUS_RECHAZADO,
            'consultado' => Predio::CONSULTADO_NO_EXISTE,
            'motivo_rechazo' => 'El predio registrado no existe en el sistema de predial.',
            'fk_usuario' => $usuario->id,
        ]);

        $response = $this->get(route('indexAprobacionesPredios'));

        $response->assertOk();
        $response->assertSee($visible->clave_predio);
        $response->assertDontSee($inexistente->clave_predio);
    }

    public function test_no_muestra_al_usuario_cuyos_predios_son_todos_inexistentes(): void
    {
        $this->authenticateAdUser();

        $soloInexistente = User::factory()->create(['name' => 'Usuario Solo Inexistente']);

        Predio::create([
            'clave_predio' => '25C000000003',
            'estatus_predio' => Predio::ESTATUS_RECHAZADO,
            'consultado' => Predio::CONSULTADO_NO_EXISTE,
            'motivo_rechazo' => 'El predio registrado no existe en el sistema de predial.',
            'fk_usuario' => $soloInexistente->id,
        ]);

        $response = $this->get(route('indexAprobacionesPredios'));

        $response->assertOk();
        $response->assertDontSee('Usuario Solo Inexistente');
    }

    private function authenticateAdUser(): void
    {
        $attributes = [
            'id_usuario' => 5,
            'username' => 'administrador',
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
