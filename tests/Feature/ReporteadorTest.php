<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Models\Dependencia;
use App\Models\ResolucionSolicitud;
use App\Models\Solicitud;
use App\Models\Tramite;
use App\Models\TurnadoSolicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class ReporteadorTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_reporteador_muestra_indicadores_y_filtros(): void
    {
        $this->authenticateAdUser();

        $response = $this->get(route('indexReporteador'));

        $response->assertOk();
        $response->assertSee('Reporteador', false);
        $response->assertSee('Solicitudes totales', false);
        $response->assertSee('reporteador-grid', false);
        $response->assertSee('filtro-dependencia', false);
        $response->assertSee('filtro-estatus', false);
    }

    public function test_index_reporteador_cuenta_sin_costo_resuelto_como_completado(): void
    {
        $this->authenticateAdUser();

        $usuario = User::factory()->create();
        $tramite = $this->crearTramiteSinCosto();
        $solicitud = Solicitud::create([
            'fk_usuario' => $usuario->id,
            'fk_tramite' => $tramite->id_tramite,
            'fecha_solicitud' => now(),
            'estatus_solicitud' => 3, // Atendida por el enlace (sin orden de pago)
        ]);
        $this->crearResolucion($solicitud);

        $response = $this->get(route('indexReporteador'));

        $response->assertOk();
        // El trámite sin costo resuelto se cuenta como Completado, no como "Por pagar".
        $this->assertMatchesRegularExpression(
            '/reporteador-card-valor">0<\/span>\s*<span class="reporteador-card-etiqueta">Por pagar<\/span>/',
            $response->getContent()
        );
        $this->assertMatchesRegularExpression(
            '/reporteador-card-valor">1<\/span>\s*<span class="reporteador-card-etiqueta">Completadas<\/span>/',
            $response->getContent()
        );
    }

    public function test_get_reporteador_solicitudes_devuelve_las_solicitudes(): void
    {
        $this->authenticateAdUser();

        $usuario = User::factory()->create();
        $tramite = $this->crearTramite();
        Solicitud::create([
            'fk_usuario' => $usuario->id,
            'fk_tramite' => $tramite->id_tramite,
            'fecha_solicitud' => now(),
            'estatus_solicitud' => 0,
        ]);

        $response = $this->getJson(route('reporteador.solicitudes'));

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment([
            'nombre_tramite' => $tramite->nombre_tramite,
            'nombre_usuario' => $usuario->name,
            'estatus_mostrado' => 0,
        ]);
    }

    public function test_get_reporteador_solicitudes_marca_completado_para_tramite_sin_costo_resuelto(): void
    {
        $this->authenticateAdUser();

        $usuario = User::factory()->create();
        $tramite = $this->crearTramiteSinCosto();
        $solicitud = Solicitud::create([
            'fk_usuario' => $usuario->id,
            'fk_tramite' => $tramite->id_tramite,
            'fecha_solicitud' => now(),
            'estatus_solicitud' => 3,
        ]);
        $this->crearResolucion($solicitud);

        $response = $this->getJson(route('reporteador.solicitudes'));

        $response->assertOk();
        $response->assertJsonFragment([
            'id_solicitud' => $solicitud->id_solicitud,
            'estatus_mostrado' => 4,
        ]);
    }

    public function test_get_reporteador_solicitudes_filtra_por_dependencia(): void
    {
        $this->authenticateAdUser();

        $usuario = User::factory()->create();
        $tramite = $this->crearTramite(); // Dependencia "Desarrollo Urbano"
        Solicitud::create([
            'fk_usuario' => $usuario->id,
            'fk_tramite' => $tramite->id_tramite,
            'fecha_solicitud' => now(),
            'estatus_solicitud' => 0,
        ]);

        $response = $this->getJson(route('reporteador.solicitudes', ['fk_dependencia' => 999]));

        $response->assertOk();
        $response->assertJsonCount(0);
    }

    public function test_generar_reporte_excel_devuelve_archivo(): void
    {
        $this->authenticateAdUser();

        $usuario = User::factory()->create();
        $tramite = $this->crearTramite();
        Solicitud::create([
            'fk_usuario' => $usuario->id,
            'fk_tramite' => $tramite->id_tramite,
            'fecha_solicitud' => now(),
            'estatus_solicitud' => 0,
        ]);

        $response = $this->get(route('reporteador.excel'));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', (string) $response->headers->get('Content-Disposition'));
    }

    private function crearResolucion(Solicitud $solicitud): void
    {
        $turnado = TurnadoSolicitud::create([
            'fk_usuario_ad' => 1,
            'fk_solicitud' => $solicitud->id_solicitud,
            'estatus_turnado' => true,
        ]);

        ResolucionSolicitud::create([
            'fk_turnado' => $turnado->id_turnado,
            'resolucion_solicitud' => 'Atendido',
            'documento_resolucion' => 'doc_resolutivos/DIG-01-2026-08-24.pdf',
        ]);
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
            'tramite_cri' => 1,
        ]);
    }

    private function crearTramiteSinCosto(): Tramite
    {
        $dependencia = Dependencia::create([
            'nombre_dependencia' => 'Desarrollo Urbano',
            'estatus_dependencia' => true,
        ]);

        return Tramite::create([
            'nombre_tramite' => 'Acta de Nacimiento',
            'descripcion_tramite' => null,
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => 0,
            'tramite_cri' => 1,
            'sin_costo' => true,
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
