<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Models\catDocumentoPersonal;
use App\Models\catDocumentoPredio;
use App\Models\DocumentoPredio;
use App\Models\Predio;
use App\Models\tblDocumentoPersonal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DocumentoVigenciaTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_aprobar_documento_personal_guarda_fecha_de_aprobacion(): void
    {
        $this->authenticateAdUser();
        Mail::fake();

        $usuario = User::factory()->create();

        $catalogo = catDocumentoPersonal::create([
            'nombre_documento' => 'INE',
            'vigencia_meses' => 3,
            'estatus_documento' => true,
        ]);

        $documento = tblDocumentoPersonal::create([
            'fk_usuario' => $usuario->id,
            'fk_documento_personal' => $catalogo->id_documento,
            'fecha_registro' => now(),
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_EN_REVISION,
        ]);

        $this->postJson(route('aprobarDocumentoPersonal', $documento->id_documento))->assertOk();

        $documento->refresh();

        $this->assertSame(tblDocumentoPersonal::ESTATUS_APROBADO, $documento->estatus_documento);
        $this->assertEquals(now()->toDateString(), $documento->fecha_aprobacion?->toDateString());
    }

    public function test_aprobar_documento_predio_guarda_fecha_de_aprobacion(): void
    {
        $this->authenticateAdUser();
        Mail::fake();

        $usuario = User::factory()->create();

        $predio = Predio::create([
            'clave_predio' => 'CLAVE-VIG-001',
            'estatus_predio' => Predio::ESTATUS_APROBADO,
            'fk_usuario' => $usuario->id,
        ]);

        $catalogo = catDocumentoPredio::create([
            'nombre_documento' => 'Boleta predial',
            'vigencia_meses' => 6,
            'estatus_documento' => true,
        ]);

        $documento = DocumentoPredio::create([
            'ruta_documento' => 'predios/clave-vig-001/boleta.pdf',
            'fk_predio' => $predio->id_predio,
            'fk_cat_documento_predio' => $catalogo->id_documento_predio,
            'estatus_documento' => DocumentoPredio::ESTATUS_EN_REVISION,
        ]);

        $this->postJson(route('aprobarDocumentoPredio', $documento->id_documento_predio))->assertOk();

        $documento->refresh();

        $this->assertSame(DocumentoPredio::ESTATUS_APROBADO, $documento->estatus_documento);
        $this->assertEquals(now()->toDateString(), $documento->fecha_aprobacion?->toDateString());
    }

    public function test_fecha_vencimiento_corre_desde_la_aprobacion(): void
    {
        $usuario = User::factory()->create();

        $catalogo = catDocumentoPersonal::create([
            'nombre_documento' => 'INE',
            'vigencia_meses' => 3,
            'estatus_documento' => true,
        ]);

        $documento = tblDocumentoPersonal::create([
            'fk_usuario' => $usuario->id,
            'fk_documento_personal' => $catalogo->id_documento,
            'fecha_registro' => '2026-08-01',
            'fecha_aprobacion' => '2026-08-19',
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_APROBADO,
        ]);

        $this->assertEquals('2026-11-19', $documento->fechaVencimiento()?->toDateString());
    }

    public function test_documento_es_valido_el_dia_del_vencimiento_y_expirado_al_dia_siguiente(): void
    {
        $usuario = User::factory()->create();

        $catalogo = catDocumentoPersonal::create([
            'nombre_documento' => 'INE',
            'vigencia_meses' => 3,
            'estatus_documento' => true,
        ]);

        $documento = tblDocumentoPersonal::create([
            'fk_usuario' => $usuario->id,
            'fk_documento_personal' => $catalogo->id_documento,
            'fecha_registro' => '2026-08-19',
            'fecha_aprobacion' => '2026-08-19',
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_APROBADO,
        ]);

        // El 19/11 (día del vencimiento) el documento sigue siendo válido.
        Carbon::setTestNow('2026-11-19 23:59:59');
        $this->assertFalse($documento->estaExpirado());

        // A partir del 20/11 ya está expirado.
        Carbon::setTestNow('2026-11-20 00:00:00');
        $this->assertTrue($documento->estaExpirado());
    }

    public function test_documento_con_vigencia_cero_no_expira(): void
    {
        $usuario = User::factory()->create();

        $catalogo = catDocumentoPersonal::create([
            'nombre_documento' => 'CURP',
            'vigencia_meses' => 0,
            'estatus_documento' => true,
        ]);

        $documento = tblDocumentoPersonal::create([
            'fk_usuario' => $usuario->id,
            'fk_documento_personal' => $catalogo->id_documento,
            'fecha_registro' => '2020-01-01',
            'fecha_aprobacion' => '2020-01-01',
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_APROBADO,
        ]);

        $this->assertNull($documento->fechaVencimiento());
        $this->assertNull($documento->diasParaVencer());
        $this->assertFalse($documento->estaExpirado());
        $this->assertFalse($documento->estaPorVencer());
    }

    public function test_esta_por_vencer_detecta_documentos_a_tres_dias_o_vencidos(): void
    {
        $usuario = User::factory()->create();

        $catalogo = catDocumentoPersonal::create([
            'nombre_documento' => 'INE',
            'vigencia_meses' => 3,
            'estatus_documento' => true,
        ]);

        $documento = tblDocumentoPersonal::create([
            'fk_usuario' => $usuario->id,
            'fk_documento_personal' => $catalogo->id_documento,
            'fecha_registro' => '2026-08-19',
            'fecha_aprobacion' => '2026-08-19',
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_APROBADO,
        ]);

        // Vence el 19/11: a 4 días aún es válido para trámite.
        Carbon::setTestNow('2026-11-15 10:00:00');
        $this->assertSame(4, $documento->diasParaVencer());
        $this->assertFalse($documento->estaPorVencer(3));

        // A 3 días o menos ya se bloquea.
        Carbon::setTestNow('2026-11-16 10:00:00');
        $this->assertSame(3, $documento->diasParaVencer());
        $this->assertTrue($documento->estaPorVencer(3));

        // El día del vencimiento sigue bloqueado para trámite.
        Carbon::setTestNow('2026-11-19 10:00:00');
        $this->assertSame(0, $documento->diasParaVencer());
        $this->assertTrue($documento->estaPorVencer(3));

        // Ya vencido.
        Carbon::setTestNow('2026-11-20 10:00:00');
        $this->assertSame(-1, $documento->diasParaVencer());
        $this->assertTrue($documento->estaPorVencer(3));
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
