<?php

namespace Tests\Feature;

use App\Models\Dependencia;
use App\Models\OrdenPago;
use App\Models\Solicitud;
use App\Models\Tramite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiOrdenesPagoTest extends TestCase
{
    use RefreshDatabase;

    /** Token compartido configurado para las pruebas. */
    private const TOKEN_API = 'token-de-prueba';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.sistema_pagos.api_token' => self::TOKEN_API]);
        $this->withHeader('X-API-Key', self::TOKEN_API);
    }

    public function test_lista_ordenes_incluye_ciudadano_dependencia_y_monto(): void
    {
        $orden = $this->crearOrden();

        $response = $this->getJson('/api/ordenes-pago');

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                [
                    'id_orden_pago' => $orden->id_orden_pago,
                    'nombre_ciudadano' => $orden->solicitud->user->name,
                    'dependencia' => 'Desarrollo Urbano',
                    'monto' => 250.0,
                ],
            ],
        ]);
    }

    public function test_lista_solo_incluye_ordenes_pendientes_sin_folio(): void
    {
        $pendiente = $this->crearOrden();
        $pagada = $this->crearOrden();
        $pagada->update(['orden_estatus' => 2, 'folio_pago' => 'FOLIO-2026-001']);

        $response = $this->getJson('/api/ordenes-pago');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.id_orden_pago', $pendiente->id_orden_pago);
    }

    public function test_muestra_orden_especifica_con_descripcion_y_cri(): void
    {
        $orden = $this->crearOrden();
        // El CRI mostrado debe ser el asignado al trámite, aunque la orden tenga otro guardado.
        $orden->update(['numero_cri' => 99]);

        $response = $this->getJson("/api/ordenes-pago/{$orden->id_orden_pago}");

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                'id_orden_pago' => $orden->id_orden_pago,
                'nombre_ciudadano' => $orden->solicitud->user->name,
                'dependencia' => 'Desarrollo Urbano',
                'monto' => 250.0,
                'orden_estatus' => 1,
                'folio_pago' => null,
                'descripcion' => $orden->descripcionResolutivo(),
                'cri' => 3,
            ],
        ]);
    }

    public function test_devuelve_404_si_la_orden_no_existe(): void
    {
        $response = $this->getJson('/api/ordenes-pago/9999');

        $response->assertNotFound();
        $response->assertJson(['success' => false]);
    }

    public function test_asigna_folio_a_una_orden_de_pago(): void
    {
        $orden = $this->crearOrden();

        $response = $this->postJson("/api/ordenes-pago/{$orden->id_orden_pago}/folio", [
            'folio' => 'FOLIO-2026-001',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                'id_orden_pago' => $orden->id_orden_pago,
                'folio_pago' => 'FOLIO-2026-001',
            ],
        ]);

        $this->assertDatabaseHas('ordenes_pagos', [
            'id_orden_pago' => $orden->id_orden_pago,
            'folio_pago' => 'FOLIO-2026-001',
            'orden_estatus' => 1,
        ]);
    }

    public function test_no_cambia_el_estatus_al_asignar_folio(): void
    {
        $orden = $this->crearOrden();

        $this->postJson("/api/ordenes-pago/{$orden->id_orden_pago}/folio", [
            'folio' => 'FOLIO-2026-002',
        ])->assertOk();

        $this->assertDatabaseHas('ordenes_pagos', [
            'id_orden_pago' => $orden->id_orden_pago,
            'orden_estatus' => 1,
        ]);
    }

    public function test_folio_es_requerido(): void
    {
        $orden = $this->crearOrden();

        $response = $this->postJson("/api/ordenes-pago/{$orden->id_orden_pago}/folio", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('folio');
    }

    public function test_rechaza_peticion_sin_token(): void
    {
        $this->flushHeaders();

        $response = $this->getJson('/api/ordenes-pago');

        $response->assertUnauthorized();
        $response->assertJson(['success' => false]);
    }

    public function test_rechaza_peticion_con_token_incorrecto(): void
    {
        $this->flushHeaders();

        $response = $this->withHeader('X-API-Key', 'token-incorrecto')
            ->getJson('/api/ordenes-pago');

        $response->assertUnauthorized();
        $response->assertJson(['success' => false]);
    }

    public function test_acepta_peticion_con_token_correcto(): void
    {
        $this->crearOrden();

        $response = $this->getJson('/api/ordenes-pago');

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }

    public function test_aplica_rate_limit_de_20_peticiones_por_minuto(): void
    {
        $this->crearOrden();

        foreach (range(1, 20) as $i) {
            $this->getJson('/api/ordenes-pago')->assertOk();
        }

        $this->getJson('/api/ordenes-pago')->assertStatus(429);
    }

    private function crearOrden(): OrdenPago
    {
        $dependencia = Dependencia::create([
            'nombre_dependencia' => 'Desarrollo Urbano',
            'estatus_dependencia' => true,
        ]);

        $tramite = Tramite::create([
            'nombre_tramite' => 'Constancia de No Adeudo',
            'descripcion_tramite' => 'Descripción del trámite.',
            'estatus_tramite' => true,
            'fk_dependencia' => $dependencia->id_dependencia,
            'precio_tramite' => 150.00,
            'tramite_cri' => 3,
            'cobra_por_m2' => false,
        ]);

        $usuario = User::factory()->create(['name' => 'Juan Pérez']);

        $solicitud = Solicitud::create([
            'fk_usuario' => $usuario->id,
            'fk_tramite' => $tramite->id_tramite,
            'fecha_solicitud' => now(),
            'estatus_solicitud' => 3,
        ]);

        return OrdenPago::factory()->create([
            'nombre_tramite' => $tramite->nombre_tramite,
            'precio_tramite' => 250,
            'numero_cri' => $tramite->tramite_cri,
            'fk_tramite' => $tramite->id_tramite,
            'fk_solicitud' => $solicitud->id_solicitud,
        ]);
    }
}
