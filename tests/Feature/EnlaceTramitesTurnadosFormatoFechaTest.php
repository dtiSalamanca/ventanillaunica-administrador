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

class EnlaceTramitesTurnadosFormatoFechaTest extends TestCase
{
    use RefreshDatabase;

    public function test_fecha_turnado_se_muestra_con_hora_pm_en_formato_12_horas(): void
    {
        $usuarioAd = $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = $this->crearTramite($dependencia);
        $solicitud = $this->crearSolicitud($tramite);
        $turnado = TurnadoSolicitud::create([
            'fk_usuario_ad' => $usuarioAd->id_usuario,
            'fk_solicitud' => $solicitud->id_solicitud,
            'estatus_turnado' => false,
        ]);
        $turnado->forceFill(['created_at' => now()->setTime(14, 35, 0)])->save();

        $response = $this->getJson(route('enlace.getTramitesTurnados'));

        $response->assertOk();
        $response->assertJsonPath(
            'data.0.fecha_turnado',
            $turnado->created_at->format('d/m/Y').' 02:35 p. m.'
        );
    }

    public function test_fecha_turnado_se_muestra_con_hora_am_en_formato_12_horas(): void
    {
        $usuarioAd = $this->authenticateAdUser();
        $dependencia = $this->crearDependencia();
        $tramite = $this->crearTramite($dependencia);
        $solicitud = $this->crearSolicitud($tramite);
        $turnado = TurnadoSolicitud::create([
            'fk_usuario_ad' => $usuarioAd->id_usuario,
            'fk_solicitud' => $solicitud->id_solicitud,
            'estatus_turnado' => false,
        ]);
        $turnado->forceFill(['created_at' => now()->setTime(8, 5, 0)])->save();

        $response = $this->getJson(route('enlace.getTramitesTurnados'));

        $response->assertOk();
        $response->assertJsonPath(
            'data.0.fecha_turnado',
            $turnado->created_at->format('d/m/Y').' 08:05 a. m.'
        );
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
            'nombre_tramite' => 'Licencia de Construcción por m²',
            'descripcion_tramite' => 'Descripción del trámite.',
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => 0,
            'tramite_cri' => 3,
            'cobra_por_m2' => true,
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
