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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EnlaceResolutivoNombreTest extends TestCase
{
    use RefreshDatabase;

    public function test_siglas_se_derivan_del_nombre_de_la_dependencia(): void
    {
        $casos = [
            'Tesorería Municipal' => 'TeM',
            'Obras Públicas' => 'ObP',
            'Registro Civil' => 'ReC',
            'Catastro Municipal' => 'CaM',
            'Desarrollo Urbano y Ordenamiento Territorial' => 'DeU',
            'Servicios Públicos Municipales' => 'SeP',
        ];

        foreach ($casos as $nombre => $esperado) {
            $dependencia = Dependencia::create([
                'nombre_dependencia' => $nombre,
                'estatus_dependencia' => true,
            ]);

            $this->assertSame($esperado, $dependencia->siglas(), "Siglas de: {$nombre}");
        }
    }

    public function test_aprobar_tramite_guarda_resolutivo_con_siglas_solicitud_y_fecha(): void
    {
        Storage::fake('local');

        $usuarioAd = $this->authenticateAdUser();
        $dependencia = Dependencia::find($usuarioAd->fk_dependencia);
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
            'documento_resolucion' => UploadedFile::fake()->create('resolucion.pdf', 100),
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $siglas = $dependencia->siglas();
        $fecha = now()->format('Y-m-d');
        $idSolicitud = str_pad((string) $solicitud->id_solicitud, 2, '0', STR_PAD_LEFT);

        Storage::disk('local')->assertExists("doc_resolutivos/{$siglas}-{$idSolicitud}-{$fecha}.pdf");
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
