<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Mail\DocumentoRechazado;
use App\Mail\PredioRevisado;
use App\Models\catDocumentoPersonal;
use App\Models\catDocumentoPredio;
use App\Models\DocumentoPredio;
use App\Models\Predio;
use App\Models\tblDocumentoPersonal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RechazarDocumentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_rechazar_documento_personal_guarda_motivo_y_envia_correo(): void
    {
        $this->authenticateAdUser();
        Mail::fake();

        $usuario = User::factory()->create();

        $catalogo = catDocumentoPersonal::create([
            'nombre_documento' => 'INE',
            'vigencia_meses' => 12,
            'estatus_documento' => true,
        ]);

        $documento = tblDocumentoPersonal::create([
            'fk_usuario' => $usuario->id,
            'fk_documento_personal' => $catalogo->id_documento,
            'fecha_registro' => now(),
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_EN_REVISION,
        ]);

        $response = $this->postJson(route('rechazarDocumentoPersonal', $documento->id_documento), [
            'motivo' => 'El documento es ilegible.',
        ]);

        $response->assertOk();
        $response->assertJson(['message' => 'Documento rechazado y notificado correctamente.']);

        $this->assertDatabaseHas('tbl_documentos_personales', [
            'id_documento' => $documento->id_documento,
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_RECHAZADO,
            'motivo_rechazo' => 'El documento es ilegible.',
        ]);

        Mail::assertSent(DocumentoRechazado::class, function (DocumentoRechazado $mail) use ($usuario): bool {
            return $mail->hasTo($usuario->email)
                && $mail->nombreDocumento === 'INE'
                && $mail->motivoRechazo === 'El documento es ilegible.';
        });
    }

    public function test_rechazar_documento_predio_guarda_motivo_y_envia_correo(): void
    {
        $this->authenticateAdUser();
        Mail::fake();

        $usuario = User::factory()->create();

        $predio = Predio::create([
            'clave_predio' => 'CLAVE-001',
            'estatus_predio' => Predio::ESTATUS_EN_REVISION,
            'fk_usuario' => $usuario->id,
        ]);

        $catalogo = catDocumentoPredio::create([
            'nombre_documento' => 'Escritura del predio',
            'vigencia_meses' => 12,
            'estatus_documento' => true,
        ]);

        $documento = DocumentoPredio::create([
            'ruta_documento' => 'predios/clave-001/escritura.pdf',
            'fk_predio' => $predio->id_predio,
            'fk_cat_documento_predio' => $catalogo->id_documento_predio,
            'estatus_documento' => DocumentoPredio::ESTATUS_EN_REVISION,
        ]);

        $response = $this->postJson(route('rechazarDocumentoPredio', $documento->id_documento_predio), [
            'motivo' => 'Falta la firma del notario.',
        ]);

        $response->assertOk();
        $response->assertJson(['message' => 'Documento rechazado y notificado correctamente.']);

        $this->assertDatabaseHas('tbl_documentos_predios', [
            'id_documento_predio' => $documento->id_documento_predio,
            'estatus_documento' => DocumentoPredio::ESTATUS_RECHAZADO,
            'motivo_rechazo' => 'Falta la firma del notario.',
        ]);

        Mail::assertSent(DocumentoRechazado::class, function (DocumentoRechazado $mail) use ($usuario): bool {
            return $mail->hasTo($usuario->email)
                && $mail->nombreDocumento === 'Escritura del predio'
                && $mail->motivoRechazo === 'Falta la firma del notario.';
        });
    }

    public function test_rechazar_predio_guarda_motivo_y_envia_correo(): void
    {
        $this->authenticateAdUser();
        Mail::fake();

        $usuario = User::factory()->create();

        $predio = Predio::create([
            'clave_predio' => 'CLAVE-002',
            'estatus_predio' => Predio::ESTATUS_EN_REVISION,
            'fk_usuario' => $usuario->id,
        ]);

        $response = $this->postJson(route('rechazarPredio', $predio->id_predio), [
            'motivo' => 'La cuenta predial es incorrecta.',
        ]);

        $response->assertOk();
        $response->assertJson(['message' => 'Predio rechazado y notificado correctamente.']);

        $this->assertDatabaseHas('tbl_predios', [
            'id_predio' => $predio->id_predio,
            'estatus_predio' => Predio::ESTATUS_RECHAZADO,
            'motivo_rechazo' => 'La cuenta predial es incorrecta.',
        ]);

        Mail::assertSent(PredioRevisado::class, function (PredioRevisado $mail) use ($usuario): bool {
            return $mail->hasTo($usuario->email)
                && $mail->motivoRechazo === 'La cuenta predial es incorrecta.';
        });
    }

    public function test_rechazar_documento_personal_requiere_motivo(): void
    {
        $this->authenticateAdUser();
        Mail::fake();

        $usuario = User::factory()->create();

        $catalogo = catDocumentoPersonal::create([
            'nombre_documento' => 'INE',
            'vigencia_meses' => 12,
            'estatus_documento' => true,
        ]);

        $documento = tblDocumentoPersonal::create([
            'fk_usuario' => $usuario->id,
            'fk_documento_personal' => $catalogo->id_documento,
            'fecha_registro' => now(),
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_EN_REVISION,
        ]);

        $response = $this->postJson(route('rechazarDocumentoPersonal', $documento->id_documento));

        $response->assertRedirect();
        $response->assertSessionHasErrors('motivo');

        $this->assertDatabaseHas('tbl_documentos_personales', [
            'id_documento' => $documento->id_documento,
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_EN_REVISION,
        ]);

        Mail::assertNothingSent();
    }

    public function test_mailable_documento_rechazado_contiene_los_datos_del_rechazo(): void
    {
        $mailable = new DocumentoRechazado(
            nombreUsuario: 'Juan Pérez',
            nombreDocumento: 'INE',
            motivoRechazo: 'El documento es ilegible.',
            urlPortal: 'http://ciudadano.test/perfiles/mi-perfil',
        );

        $html = $mailable->render();

        $this->assertStringContainsString('Juan Pérez', $html);
        $this->assertStringContainsString('INE', $html);
        $this->assertStringContainsString('El documento es ilegible.', $html);
        $this->assertStringContainsString('http://ciudadano.test/perfiles/mi-perfil', $html);
        $this->assertStringContainsString('Ingresar al portal', $html);
        $this->assertStringContainsString('cid:escudo@salamanca.gob.mx', $html);
    }

    public function test_mailable_predio_revisado_genera_url_absoluta_del_portal(): void
    {
        $usuario = User::factory()->create();

        $predio = Predio::create([
            'clave_predio' => 'CLAVE-009',
            'estatus_predio' => Predio::ESTATUS_RECHAZADO,
            'motivo_rechazo' => 'cuenta predial incorrecta.',
            'fk_usuario' => $usuario->id,
        ]);

        $html = (new PredioRevisado($predio, 'cuenta predial incorrecta.'))->render();

        $this->assertStringContainsString('http://', $html);
        $this->assertStringContainsString('/perfiles/mi-perfil', $html);
        $this->assertStringContainsString('cuenta predial incorrecta.', $html);
        $this->assertStringContainsString('cuenta predial', $html);
        $this->assertStringContainsString('cid:escudo@salamanca.gob.mx', $html);
    }

    public function test_mailable_predio_aprobado_contiene_mensaje_y_boton(): void
    {
        $usuario = User::factory()->create();

        $predio = Predio::create([
            'clave_predio' => 'CLAVE-010',
            'estatus_predio' => Predio::ESTATUS_APROBADO,
            'fk_usuario' => $usuario->id,
        ]);

        $html = (new PredioRevisado($predio))->render();

        $this->assertStringContainsString('Predio aprobado', $html);
        $this->assertStringContainsString('CLAVE-010', $html);
        $this->assertStringContainsString('cuenta predial', $html);
        $this->assertStringContainsString('cargar la documentación necesaria y comenzar a realizar tus trámites', $html);
        $this->assertStringContainsString('Ingresar al portal', $html);
        $this->assertStringContainsString('/perfiles/mi-perfil', $html);
        $this->assertStringContainsString('cid:escudo@salamanca.gob.mx', $html);
    }

    public function test_aprobar_documento_personal_no_envia_correo(): void
    {
        $this->authenticateAdUser();
        Mail::fake();

        $usuario = User::factory()->create();

        $catalogo = catDocumentoPersonal::create([
            'nombre_documento' => 'INE',
            'vigencia_meses' => 12,
            'estatus_documento' => true,
        ]);

        $documento = tblDocumentoPersonal::create([
            'fk_usuario' => $usuario->id,
            'fk_documento_personal' => $catalogo->id_documento,
            'fecha_registro' => now(),
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_EN_REVISION,
        ]);

        $this->postJson(route('aprobarDocumentoPersonal', $documento->id_documento))->assertOk();

        Mail::assertNothingSent();
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
