<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Mail\ResolutivoDisponible;
use App\Models\Dependencia;
use App\Models\Solicitud;
use App\Models\Tramite;
use App\Models\TurnadoSolicitud;
use App\Models\User;
use App\Models\UsuarioAD;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResolutivoDisponibleTest extends TestCase
{
    use RefreshDatabase;

    public function test_al_subir_el_resolutivo_se_envia_correo_al_ciudadano(): void
    {
        Storage::fake('local');
        Mail::fake();

        $usuarioAd = $this->authenticateAdUser();
        $dependencia = Dependencia::find($usuarioAd->fk_dependencia);
        $tramite = $this->crearTramite($dependencia, false);
        $solicitud = $this->crearSolicitud($tramite);
        $turnado = TurnadoSolicitud::create([
            'fk_usuario_ad' => $usuarioAd->id_usuario,
            'fk_solicitud' => $solicitud->id_solicitud,
            'estatus_turnado' => false,
        ]);

        $this->post(route('enlace.tramitesTurnadosAprobar', $turnado->id_turnado), [
            'resolucion_solicitud' => 'Atendido',
            'precio_tramite' => 250,
            'documento_resolucion' => UploadedFile::fake()->create('resolucion.pdf', 100),
        ])->assertOk();

        Mail::assertSent(ResolutivoDisponible::class, function (ResolutivoDisponible $mail) use ($solicitud, $tramite): bool {
            return $mail->hasTo($solicitud->user->email)
                && $mail->nombreUsuario === $solicitud->user->name
                && $mail->nombreTramite === $tramite->nombre_tramite;
        });
    }

    public function test_sin_documento_resolutivo_no_se_envia_correo(): void
    {
        Storage::fake('local');
        Mail::fake();

        $usuarioAd = $this->authenticateAdUser();
        $dependencia = Dependencia::find($usuarioAd->fk_dependencia);
        $tramite = $this->crearTramite($dependencia, false);
        $solicitud = $this->crearSolicitud($tramite);
        $turnado = TurnadoSolicitud::create([
            'fk_usuario_ad' => $usuarioAd->id_usuario,
            'fk_solicitud' => $solicitud->id_solicitud,
            'estatus_turnado' => false,
        ]);

        $this->post(route('enlace.tramitesTurnadosAprobar', $turnado->id_turnado), [
            'resolucion_solicitud' => 'Atendido',
            'precio_tramite' => 250,
        ])->assertOk();

        Mail::assertNothingSent();
    }

    public function test_mailable_resolutivo_contiene_datos_y_url_absoluta(): void
    {
        $mailable = new ResolutivoDisponible(
            nombreUsuario: 'Juan Pérez',
            nombreTramite: 'Constancia de No Adeudo',
            urlPortal: 'http://ciudadano.test/tramites/mis-tramites',
        );

        $html = $mailable->render();

        $this->assertStringContainsString('Juan Pérez', $html);
        $this->assertStringContainsString('Tu trámite ya tiene una respuesta.', $html);
        $this->assertStringContainsString('Para consultar tu resolutivo, realiza el pago correspondiente en caja.', $html);
        $this->assertStringContainsString('http://ciudadano.test/tramites/mis-tramites', $html);
        $this->assertStringContainsString('Ingresar al portal', $html);
        $this->assertStringContainsString('Trámite concluido', $html);
        $this->assertStringContainsString('cid:escudo@salamanca.gob.mx', $html);
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
}
