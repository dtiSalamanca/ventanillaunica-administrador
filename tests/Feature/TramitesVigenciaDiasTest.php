<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Models\Dependencia;
use App\Models\Tramite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class TramitesVigenciaDiasTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrar_tramite_con_vigencia_dias_guardada(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();

        $response = $this->post(route('registrarTramite'), [
            'nombre' => 'Licencia de Construcción',
            'descripcion' => 'Trámite con vigencia de un año.',
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => 1,
            'vigencia_dias' => 365,
        ]);

        $response->assertRedirect(route('indexTramites'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cat_tramites', [
            'nombre_tramite' => 'Licencia de Construcción',
            'vigencia_dias' => 365,
        ]);
    }

    public function test_registrar_tramite_sin_vigencia_dias_se_guarda_con_cero(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();

        $response = $this->from(route('agregarTramite'))->post(route('registrarTramite'), [
            'nombre' => 'Trámite sin vigencia',
            'descripcion' => 'La vigencia es opcional.',
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => 1,
        ]);

        $response->assertRedirect(route('indexTramites'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cat_tramites', [
            'nombre_tramite' => 'Trámite sin vigencia',
            'vigencia_dias' => 0,
        ]);
    }

    public function test_registrar_tramite_con_vigencia_de_uno_o_dos_digitos_se_guarda(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();

        $response = $this->from(route('agregarTramite'))->post(route('registrarTramite'), [
            'nombre' => 'Trámite con vigencia corta',
            'descripcion' => 'Vigencia de dos dígitos es válida.',
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => 1,
            'vigencia_dias' => 45,
        ]);

        $response->assertRedirect(route('indexTramites'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cat_tramites', [
            'nombre_tramite' => 'Trámite con vigencia corta',
            'vigencia_dias' => 45,
        ]);
    }

    public function test_registrar_tramite_con_vigencia_de_cuatro_digitos_falla_validacion(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();

        $response = $this->from(route('agregarTramite'))->post(route('registrarTramite'), [
            'nombre' => 'Trámite con vigencia larga',
            'descripcion' => 'Debe fallar la validación.',
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => 1,
            'vigencia_dias' => 2455,
        ]);

        $response->assertRedirect(route('agregarTramite'));
        $response->assertSessionHasErrors('vigencia_dias');

        $this->assertDatabaseMissing('cat_tramites', [
            'nombre_tramite' => 'Trámite con vigencia larga',
        ]);
    }

    public function test_registrar_tramite_con_vigencia_con_letras_falla_validacion(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();

        $response = $this->from(route('agregarTramite'))->post(route('registrarTramite'), [
            'nombre' => 'Trámite con vigencia inválida',
            'descripcion' => 'Debe fallar la validación.',
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => 1,
            'vigencia_dias' => 'abc',
        ]);

        $response->assertRedirect(route('agregarTramite'));
        $response->assertSessionHasErrors('vigencia_dias');

        $this->assertDatabaseMissing('cat_tramites', [
            'nombre_tramite' => 'Trámite con vigencia inválida',
        ]);
    }

    public function test_actualizar_tramite_con_vigencia_dias_guardada(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = $this->crearTramite($dependencia);

        $response = $this->post(route('actualizarTramite', $tramite->id_tramite), [
            'nombre' => $tramite->nombre_tramite,
            'descripcion' => $tramite->descripcion_tramite,
            'fk_dependencia' => $dependencia->id_dependencia,
            'fk_cri' => $tramite->tramite_cri,
            'vigencia_dias' => 300,
        ]);

        $response->assertRedirect(route('indexTramites'));

        $this->assertDatabaseHas('cat_tramites', [
            'id_tramite' => $tramite->id_tramite,
            'vigencia_dias' => 300,
        ]);
    }

    public function test_tramite_con_vigencia_dias_aparece_en_listado_activos(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        Tramite::create([
            'nombre_tramite' => 'Licencia con vigencia',
            'descripcion_tramite' => 'Trámite con vigencia en días.',
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => 0,
            'tramite_cri' => 1,
            'cobra_por_m2' => false,
            'vigencia_dias' => 300,
        ]);

        $response = $this->getJson(route('getTramitesActivos'));

        $response->assertOk();
        $response->assertJsonFragment([
            'nombre_tramite' => 'Licencia con vigencia',
            'vigencia_dias' => 300,
        ]);
    }

    public function test_vista_agregar_tramite_muestra_campo_vigencia(): void
    {
        $this->authenticateAdUser();

        $response = $this->get(route('agregarTramite'));

        $response->assertOk();
        $response->assertSee('Vigencia (días)', false);
        $response->assertSee('id="vigencia_dias"', false);
    }

    public function test_vista_editar_tramite_muestra_valor_de_vigencia(): void
    {
        $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = $this->crearTramite($dependencia);

        $response = $this->get(route('editarTramite', $tramite->id_tramite));

        $response->assertOk();
        $response->assertSee('Vigencia (días)', false);
        $response->assertSee('id="vigencia_dias"', false);
        $response->assertSee('value="'.$tramite->vigencia_dias.'"', false);
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
            'vigencia_dias' => 365,
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
