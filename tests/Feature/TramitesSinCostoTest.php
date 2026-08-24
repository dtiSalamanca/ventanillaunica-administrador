<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Models\Dependencia;
use App\Models\Tramite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class TramitesSinCostoTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrar_tramite_sin_costo_guarda_flag(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();

        $response = $this->post(route('registrarTramite'), [
            'nombre' => 'Acta de Nacimiento',
            'descripcion' => 'Trámite gratuito.',
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => 1,
            'sin_costo' => 1,
            'vigencia_dias' => 180,
        ]);

        $response->assertRedirect(route('indexTramites'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cat_tramites', [
            'nombre_tramite' => 'Acta de Nacimiento',
            'sin_costo' => 1,
            'cobra_por_m2' => 0,
        ]);
    }

    public function test_registrar_tramite_sin_marcar_sin_costo_guarda_flag_en_cero(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();

        $response = $this->post(route('registrarTramite'), [
            'nombre' => 'Licencia de Funcionamiento',
            'descripcion' => 'Trámite con costo.',
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => 1,
            'vigencia_dias' => 365,
        ]);

        $response->assertRedirect(route('indexTramites'));

        $this->assertDatabaseHas('cat_tramites', [
            'nombre_tramite' => 'Licencia de Funcionamiento',
            'sin_costo' => 0,
        ]);
    }

    public function test_actualizar_tramite_puede_marcar_sin_costo(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = $this->crearTramite($dependencia);

        $response = $this->post(route('actualizarTramite', $tramite->id_tramite), [
            'nombre' => $tramite->nombre_tramite,
            'descripcion' => $tramite->descripcion_tramite,
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => $tramite->tramite_cri,
            'sin_costo' => 1,
            'vigencia_dias' => 365,
        ]);

        $response->assertRedirect(route('indexTramites'));

        $this->assertDatabaseHas('cat_tramites', [
            'id_tramite' => $tramite->id_tramite,
            'sin_costo' => 1,
        ]);
    }

    public function test_actualizar_tramite_puede_desmarcar_sin_costo(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = Tramite::create([
            'nombre_tramite' => 'Constancia de Residencia',
            'descripcion_tramite' => 'Trámite gratuito.',
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => 0,
            'tramite_cri' => 1,
            'sin_costo' => true,
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
            'sin_costo' => 0,
        ]);
    }

    public function test_tramite_sin_costo_aparece_en_listado_activos(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        Tramite::create([
            'nombre_tramite' => 'Acta de Nacimiento',
            'descripcion_tramite' => 'Trámite gratuito.',
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => 0,
            'tramite_cri' => 1,
            'sin_costo' => true,
        ]);

        $response = $this->getJson(route('getTramitesActivos'));

        $response->assertOk();
        $response->assertJsonFragment([
            'nombre_tramite' => 'Acta de Nacimiento',
            'sin_costo' => true,
        ]);
    }

    public function test_vista_agregar_tramite_muestra_checkbox_sin_costo(): void
    {
        $this->authenticateAdUser();

        $response = $this->get(route('agregarTramite'));

        $response->assertOk();
        $response->assertSee('Sin costo', false);
        $response->assertSee('id="sin_costo"', false);
    }

    public function test_vista_editar_tramite_muestra_checkbox_marcado_cuando_sin_costo(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = Tramite::create([
            'nombre_tramite' => 'Acta de Nacimiento',
            'descripcion_tramite' => 'Trámite gratuito.',
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => 0,
            'tramite_cri' => 1,
            'sin_costo' => true,
        ]);

        $response = $this->get(route('editarTramite', $tramite->id_tramite));

        $response->assertOk();
        $response->assertSee('Sin costo', false);
        $response->assertSee('id="sin_costo"', false);
        $response->assertSee('checked', false);
    }

    private function crearDependencia(): Dependencia
    {
        return Dependencia::create([
            'nombre_dependencia' => 'Desarrollo Urbano',
            'estatus_dependencia' => true,
        ]);
    }

    private function crearTramite(Dependencia $dependencia): Tramite
    {
        return Tramite::create([
            'nombre_tramite' => 'Licencia de Uso de Suelo',
            'descripcion_tramite' => 'Descripción del trámite.',
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => 0,
            'tramite_cri' => 2,
            'cobra_por_m2' => false,
            'sin_costo' => false,
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
