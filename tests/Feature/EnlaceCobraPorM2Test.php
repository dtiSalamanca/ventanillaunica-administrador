<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Models\Dependencia;
use App\Models\Solicitud;
use App\Models\Tramite;
use App\Models\TurnadoSolicitud;
use App\Models\User;
use App\Models\UsuarioAD;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class EnlaceCobraPorM2Test extends TestCase
{
    use RefreshDatabase;

    public function test_aprobar_tramite_por_m2_guarda_orden_de_pago_con_precio(): void
    {
        $usuarioAd = $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = $this->crearTramite($dependencia, true);
        $solicitud = $this->crearSolicitud($tramite);
        $turnado = TurnadoSolicitud::create([
            'fk_usuario_ad' => $usuarioAd->id_usuario,
            'fk_solicitud' => $solicitud->id_solicitud,
            'estatus_turnado' => false,
        ]);

        $response = $this->post(route('enlace.tramitesTurnadosAprobar', $turnado->id_turnado), [
            'resolucion_solicitud' => 'Atendido',
            'precio_tramite' => 250,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('ordenes_pagos', [
            'nombre_tramite' => $tramite->nombre_tramite,
            'precio_tramite' => 250,
            'numero_cri' => $tramite->tramite_cri,
            'fk_tramite' => $tramite->id_tramite,
            'fk_solicitud' => $solicitud->id_solicitud,
        ]);

        $this->assertDatabaseHas('tbl_solicitudes', [
            'id_solicitud' => $solicitud->id_solicitud,
            'estatus_solicitud' => 3,
        ]);
    }

    public function test_aprobar_tramite_por_m2_sin_precio_falla_validacion(): void
    {
        $usuarioAd = $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = $this->crearTramite($dependencia, true);
        $solicitud = $this->crearSolicitud($tramite);
        $turnado = TurnadoSolicitud::create([
            'fk_usuario_ad' => $usuarioAd->id_usuario,
            'fk_solicitud' => $solicitud->id_solicitud,
            'estatus_turnado' => false,
        ]);

        $response = $this->from(route('enlace.tramitesTurnadosDetalles', $turnado->id_turnado))
            ->post(route('enlace.tramitesTurnadosAprobar', $turnado->id_turnado), [
                'resolucion_solicitud' => 'Atendido',
            ]);

        $response->assertSessionHasErrors('precio_tramite');
        $this->assertDatabaseCount('ordenes_pagos', 0);
        $this->assertDatabaseHas('tbl_solicitudes', [
            'id_solicitud' => $solicitud->id_solicitud,
            'estatus_solicitud' => 1,
        ]);
    }

    public function test_aprobar_tramite_fijo_requiere_precio_y_crea_orden(): void
    {
        $usuarioAd = $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = $this->crearTramite($dependencia, false);
        $solicitud = $this->crearSolicitud($tramite);
        $turnado = TurnadoSolicitud::create([
            'fk_usuario_ad' => $usuarioAd->id_usuario,
            'fk_solicitud' => $solicitud->id_solicitud,
            'estatus_turnado' => false,
        ]);

        $response = $this->post(route('enlace.tramitesTurnadosAprobar', $turnado->id_turnado), [
            'resolucion_solicitud' => 'Atendido',
            'precio_tramite' => 250,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('ordenes_pagos', [
            'nombre_tramite' => $tramite->nombre_tramite,
            'precio_tramite' => 250,
            'numero_cri' => $tramite->tramite_cri,
            'fk_tramite' => $tramite->id_tramite,
            'fk_solicitud' => $solicitud->id_solicitud,
        ]);
    }

    public function test_vista_detalles_muestra_campo_precio_para_tramite_por_m2(): void
    {
        $usuarioAd = $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = $this->crearTramite($dependencia, true);
        $solicitud = $this->crearSolicitud($tramite);
        $turnado = TurnadoSolicitud::create([
            'fk_usuario_ad' => $usuarioAd->id_usuario,
            'fk_solicitud' => $solicitud->id_solicitud,
            'estatus_turnado' => false,
        ]);

        $response = $this->get(route('enlace.tramitesTurnadosDetalles', $turnado->id_turnado));

        $response->assertOk();
        $response->assertSee('Este trámite se cobra por metro cuadrado', false);
        $response->assertSee('id="precioM2"', false);
    }

    public function test_vista_detalles_muestra_campo_precio_para_tramite_fijo(): void
    {
        $usuarioAd = $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = $this->crearTramite($dependencia, false);
        $solicitud = $this->crearSolicitud($tramite);
        $turnado = TurnadoSolicitud::create([
            'fk_usuario_ad' => $usuarioAd->id_usuario,
            'fk_solicitud' => $solicitud->id_solicitud,
            'estatus_turnado' => false,
        ]);

        $response = $this->get(route('enlace.tramitesTurnadosDetalles', $turnado->id_turnado));

        $response->assertOk();
        $response->assertSee('id="precioM2"', false);
    }

    private function crearDependencia(): Dependencia
    {
        return Dependencia::create([
            'nombre_dependencia' => 'Desarrollo Urbano',
            'estatus_dependencia' => true,
        ]);
    }

    private function crearTramite(Dependencia $dependencia, bool $cobraPorM2): Tramite
    {
        return Tramite::create([
            'nombre_tramite' => $cobraPorM2
                ? 'Licencia de Construcción por m²'
                : 'Constancia de No Adeudo',
            'descripcion_tramite' => 'Descripción del trámite.',
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => $cobraPorM2 ? 0 : 150.00,
            'tramite_cri' => 3,
            'cobra_por_m2' => $cobraPorM2,
        ]);
    }

    private function crearSolicitud(Tramite $tramite): Solicitud
    {
        $usuario = User::factory()->create();

        return Solicitud::create([
            'fk_usuario' => $usuario->id,
            'fk_tramite' => $tramite->id_tramite,
            'fecha_solicitud' => now(),
            'estatus_solicitud' => 1,
        ]);
    }

    private function authenticateAdUser(): UsuarioAD
    {
        $attributes = [
            'id_usuario' => 5,
            'username' => 'enlace',
            'nombre' => 'ENLACE',
            'apaterno' => 'DE',
            'amaterno' => 'ATENCION',
            'app' => 'SIA',
            'rol_id' => 2,
            'id_area' => 31,
            'rol' => 'enlace',
            'activo' => true,
            AdUser::EncryptedPasswordAttribute => Crypt::encryptString('secret-password'),
        ];

        Cache::put("ad_user_{$attributes['id_usuario']}", $attributes, now()->addHour());

        $this->actingAs(new AdUser($attributes), 'ad');

        $dependencia = $this->crearDependencia();

        return UsuarioAD::create([
            'nombre_usuario' => 'enlace',
            'fk_dependencia' => $dependencia->id_dependencia,
        ]);
    }
}
