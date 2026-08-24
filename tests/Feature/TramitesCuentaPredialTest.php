<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Models\Dependencia;
use App\Models\Tramite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class TramitesCuentaPredialTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrar_tramite_sin_cuenta_predial_guarda_flag_en_cero(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();

        $response = $this->post(route('registrarTramite'), [
            'nombre' => 'Constancia de No Adeudo',
            'descripcion' => 'Trámite que solo usa documentos del perfil.',
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => 1,
            'vigencia_dias' => 180,
        ]);

        $response->assertRedirect(route('indexTramites'));

        $this->assertDatabaseHas('cat_tramites', [
            'nombre_tramite' => 'Constancia de No Adeudo',
            'cuenta_predial' => 0,
        ]);
    }

    public function test_registrar_tramite_con_cuenta_predial_guarda_flag_en_uno(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();

        $response = $this->post(route('registrarTramite'), [
            'nombre' => 'Licencia de Construcción',
            'descripcion' => 'Trámite que requiere una cuenta predial.',
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => 1,
            'cuenta_predial' => 1,
            'vigencia_dias' => 365,
        ]);

        $response->assertRedirect(route('indexTramites'));

        $this->assertDatabaseHas('cat_tramites', [
            'nombre_tramite' => 'Licencia de Construcción',
            'cuenta_predial' => 1,
        ]);
    }

    public function test_actualizar_tramite_puede_desmarcar_la_cuenta_predial(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = Tramite::create([
            'nombre_tramite' => 'Licencia de Uso de Suelo',
            'descripcion_tramite' => 'Descripción del trámite.',
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => 0,
            'tramite_cri' => 1,
            'cuenta_predial' => true,
        ]);

        $response = $this->post(route('actualizarTramite', $tramite->id_tramite), [
            'nombre' => $tramite->nombre_tramite,
            'descripcion' => $tramite->descripcion_tramite,
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => $tramite->tramite_cri,
            'vigencia_dias' => 90,
        ]);

        $response->assertRedirect(route('indexTramites'));

        $this->assertDatabaseHas('cat_tramites', [
            'id_tramite' => $tramite->id_tramite,
            'cuenta_predial' => 0,
        ]);
    }

    public function test_vista_agregar_tramite_muestra_checkbox_cuenta_predial(): void
    {
        $this->authenticateAdUser();

        $response = $this->get(route('agregarTramite'));

        $response->assertOk();
        $response->assertSee('Requiere cuenta predial', false);
        $response->assertSee('id="cuenta_predial"', false);
        $response->assertDontSee('checked', false);
    }

    public function test_vista_editar_tramite_muestra_checkbox_marcado_cuando_requiere_cuenta_predial(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = Tramite::create([
            'nombre_tramite' => 'Licencia por m²',
            'descripcion_tramite' => 'Trámite predial.',
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => 0,
            'tramite_cri' => 1,
            'cuenta_predial' => true,
        ]);

        $response = $this->get(route('editarTramite', $tramite->id_tramite));

        $response->assertOk();
        $response->assertSee('Requiere cuenta predial', false);
        $response->assertSee('id="cuenta_predial"', false);
        $response->assertSee('checked', false);
    }

    private function crearDependencia(): Dependencia
    {
        return Dependencia::create([
            'nombre_dependencia' => 'Desarrollo Urbano',
            'estatus_dependencia' => true,
        ]);
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
