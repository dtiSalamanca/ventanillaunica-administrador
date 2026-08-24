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

class EnlaceSinCostoTest extends TestCase
{
    use RefreshDatabase;

    public function test_aprobar_tramite_sin_costo_no_requiere_precio_y_no_crea_orden(): void
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
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseCount('ordenes_pagos', 0);

        $this->assertDatabaseHas('tbl_solicitudes', [
            'id_solicitud' => $solicitud->id_solicitud,
            'estatus_solicitud' => 3,
        ]);
    }

    public function test_aprobar_tramite_sin_costo_ignora_precio_enviado(): void
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

        $this->assertDatabaseCount('ordenes_pagos', 0);
    }

    public function test_vista_detalles_no_muestra_campo_precio_para_tramite_sin_costo(): void
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
        $response->assertSee('Este trámite es sin costo', false);
        $response->assertDontSee('id="precioM2"', false);
    }

    public function test_vista_detalles_muestra_campo_precio_para_tramite_con_costo(): void
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

    private function crearTramite(Dependencia $dependencia, bool $sinCosto): Tramite
    {
        return Tramite::create([
            'nombre_tramite' => $sinCosto
                ? 'Acta de Nacimiento'
                : 'Licencia de Construcción',
            'descripcion_tramite' => 'Descripción del trámite.',
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => $sinCosto ? 0 : 150.00,
            'tramite_cri' => 3,
            'cobra_por_m2' => false,
            'sin_costo' => $sinCosto,
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
