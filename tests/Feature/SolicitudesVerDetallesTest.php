<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Models\catDocumentoPredio;
use App\Models\Dependencia;
use App\Models\DocumentoPredio;
use App\Models\DocumentoTramite;
use App\Models\Predio;
use App\Models\Solicitud;
use App\Models\Tramite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class SolicitudesVerDetallesTest extends TestCase
{
    use RefreshDatabase;

    public function test_ver_detalles_muestra_el_documento_del_predio_por_tipo(): void
    {
        $this->authenticateAdUser();

        $usuario = User::factory()->create();
        $tramite = $this->crearTramite();
        $predio = Predio::create([
            'clave_predio' => '1234567890',
            'estatus_predio' => Predio::ESTATUS_APROBADO,
            'fk_usuario' => $usuario->id,
        ]);

        $catalogoPredio = catDocumentoPredio::create([
            'nombre_documento' => 'Escritura del predio',
            'vigencia_meses' => 12,
            'estatus_documento' => true,
        ]);

        DocumentoPredio::create([
            'fk_cat_documento_predio' => $catalogoPredio->id_documento_predio,
            'ruta_documento' => 'documentos_predios/25/3/1/archivo-predio-ejemplo.pdf',
            'fk_predio' => $predio->id_predio,
            'estatus_documento' => DocumentoPredio::ESTATUS_APROBADO,
        ]);

        $solicitud = Solicitud::create([
            'fk_usuario' => $usuario->id,
            'fk_tramite' => $tramite->id_tramite,
            'fecha_solicitud' => now(),
            'estatus_solicitud' => 0,
        ]);

        DocumentoTramite::create([
            'fk_requisito' => $catalogoPredio->id_documento_predio,
            'fk_documento_personal' => null,
            'fk_documento_solicitud' => null,
            'fk_solicitud' => $solicitud->id_solicitud,
        ]);

        $response = $this->get(route('solicitudes.verDetalles', $solicitud->id_solicitud));

        $response->assertOk();
        $response->assertSee('Escritura del predio', false);
        $response->assertSee('archivo-predio-ejemplo.pdf', false);
        $response->assertDontSee('No adjuntado', false);
    }

    public function test_ver_detalles_muestra_no_adjuntado_cuando_falta_el_documento_del_predio(): void
    {
        $this->authenticateAdUser();

        $usuario = User::factory()->create();
        $tramite = $this->crearTramite();

        $solicitud = Solicitud::create([
            'fk_usuario' => $usuario->id,
            'fk_tramite' => $tramite->id_tramite,
            'fecha_solicitud' => now(),
            'estatus_solicitud' => 0,
        ]);

        DocumentoTramite::create([
            'fk_requisito' => 99,
            'fk_documento_personal' => null,
            'fk_documento_solicitud' => null,
            'fk_solicitud' => $solicitud->id_solicitud,
        ]);

        $response = $this->get(route('solicitudes.verDetalles', $solicitud->id_solicitud));

        $response->assertOk();
        $response->assertSee('No adjuntado', false);
    }

    private function crearTramite(): Tramite
    {
        $dependencia = Dependencia::create([
            'nombre_dependencia' => 'Desarrollo Urbano',
            'estatus_dependencia' => true,
        ]);

        return Tramite::create([
            'nombre_tramite' => 'Licencia de Uso de Suelo',
            'descripcion_tramite' => null,
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => 0,
            'tramite_cri' => 0,
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
