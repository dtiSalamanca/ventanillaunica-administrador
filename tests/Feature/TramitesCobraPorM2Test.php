<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Models\Dependencia;
use App\Models\Tramite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class TramitesCobraPorM2Test extends TestCase
{
    use RefreshDatabase;

    public function test_registrar_tramite_con_cobra_por_m2_no_requiere_precio(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();

        $response = $this->post(route('registrarTramite'), [
            'nombre' => 'Licencia de Construcción por m²',
            'descripcion' => 'Trámite que se cobra por metro cuadrado.',
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => 1,
            'cobra_por_m2' => 1,
        ]);

        $response->assertRedirect(route('indexTramites'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cat_tramites', [
            'nombre_tramite' => 'Licencia de Construcción por m²',
            'precio_tramite' => 0,
            'cobra_por_m2' => 1,
        ]);
    }

    public function test_registrar_tramite_sin_cobra_por_m2_no_requiere_precio(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();

        $response = $this->from(route('agregarTramite'))->post(route('registrarTramite'), [
            'nombre' => 'Constancia de No Adeudo',
            'descripcion' => 'Trámite de precio fijo.',
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => 1,
        ]);

        $response->assertRedirect(route('indexTramites'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cat_tramites', [
            'nombre_tramite' => 'Constancia de No Adeudo',
            'precio_tramite' => 0,
            'cobra_por_m2' => 0,
        ]);
    }

    public function test_actualizar_tramite_con_cobra_por_m2_guarda_flag(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = $this->crearTramite($dependencia);

        $response = $this->post(route('actualizarTramite', $tramite->id_tramite), [
            'nombre' => $tramite->nombre_tramite,
            'descripcion' => $tramite->descripcion_tramite,
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => $tramite->tramite_cri,
            'cobra_por_m2' => 1,
        ]);

        $response->assertRedirect(route('indexTramites'));

        $this->assertDatabaseHas('cat_tramites', [
            'id_tramite' => $tramite->id_tramite,
            'precio_tramite' => 0,
            'cobra_por_m2' => 1,
        ]);
    }

    public function test_tramite_con_cobra_por_m2_aparece_en_listado_activos(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        Tramite::create([
            'nombre_tramite' => 'Licencia por m²',
            'descripcion_tramite' => 'Trámite por metro cuadrado.',
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => 0,
            'tramite_cri' => 1,
            'cobra_por_m2' => true,
        ]);

        $response = $this->getJson(route('getTramitesActivos'));

        $response->assertOk();
        $response->assertJsonFragment([
            'nombre_tramite' => 'Licencia por m²',
            'cobra_por_m2' => true,
        ]);
    }

    public function test_vista_agregar_tramite_muestra_checkbox_por_m2(): void
    {
        $this->authenticateAdUser();

        $response = $this->get(route('agregarTramite'));

        $response->assertOk();
        $response->assertSee('Se cobra por metro cuadrado', false);
        $response->assertSee('id="cobra_por_m2"', false);
    }

    public function test_vista_editar_tramite_muestra_checkbox_marcado_cuando_cobra_por_m2(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = Tramite::create([
            'nombre_tramite' => 'Licencia por m²',
            'descripcion_tramite' => 'Trámite por metro cuadrado.',
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => 0,
            'tramite_cri' => 1,
            'cobra_por_m2' => true,
        ]);

        $response = $this->get(route('editarTramite', $tramite->id_tramite));

        $response->assertOk();
        $response->assertSee('Se cobra por metro cuadrado', false);
        $response->assertSee('id="cobra_por_m2"', false);
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
            'precio_tramite' => 950.00,
            'tramite_cri' => 2,
            'cobra_por_m2' => false,
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
