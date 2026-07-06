<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Models\catDocumentoPersonal;
use App\Models\tblDocumentoPersonal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VisualizarDocumentoPersonalTest extends TestCase
{
    use RefreshDatabase;

    private string $apiUrl = 'http://ciudadano.test/api/documentos-personales';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.ventanilla_ciudadano.base_url' => 'http://ciudadano.test',
            'services.ventanilla_ciudadano.api_token' => 'token-compartido-de-prueba',
        ]);
    }

    public function test_visualizar_devuelve_el_pdf_inline_cuando_la_api_responde_ok(): void
    {
        $this->authenticateAdUser();

        $catalogo = catDocumentoPersonal::create([
            'nombre_documento' => 'INE',
            'vigencia_meses' => 12,
            'estatus_documento' => true,
        ]);

        $documento = tblDocumentoPersonal::create([
            'fk_usuario' => User::factory()->create()->id,
            'fk_documento_personal' => $catalogo->id_documento,
            'fecha_registro' => now(),
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_EN_REVISION,
        ]);

        Http::fake([
            "http://ciudadano.test/api/documentos-personales/{$documento->id_documento}/archivo" => Http::response(
                '%PDF-1.4 fake content',
                200,
                ['Content-Type' => 'application/pdf'],
            ),
        ]);

        $response = $this->get(route('visualizarDocumentoPersonal', $documento->id_documento));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('inline;', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('ine.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_visualizar_devuelve_html_de_error_cuando_la_api_responde_404(): void
    {
        $this->authenticateAdUser();

        $catalogo = catDocumentoPersonal::create([
            'nombre_documento' => 'Comprobante de domicilio',
            'vigencia_meses' => 6,
            'estatus_documento' => true,
        ]);

        $documento = tblDocumentoPersonal::create([
            'fk_usuario' => User::factory()->create()->id,
            'fk_documento_personal' => $catalogo->id_documento,
            'fecha_registro' => now(),
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_EN_REVISION,
        ]);

        Http::fake([
            "http://ciudadano.test/api/documentos-personales/{$documento->id_documento}/archivo" => Http::response(
                ['message' => 'El archivo no existe.'],
                404,
            ),
        ]);

        $response = $this->get(route('visualizarDocumentoPersonal', $documento->id_documento));

        $response->assertStatus(502);
        $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
        $response->assertSee('Documento no disponible', false);
    }

    public function test_visualizar_envia_el_token_compartido_en_la_peticion_a_la_api(): void
    {
        $this->authenticateAdUser();

        $catalogo = catDocumentoPersonal::create([
            'nombre_documento' => 'INE',
            'vigencia_meses' => 12,
            'estatus_documento' => true,
        ]);

        $documento = tblDocumentoPersonal::create([
            'fk_usuario' => User::factory()->create()->id,
            'fk_documento_personal' => $catalogo->id_documento,
            'fecha_registro' => now(),
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_EN_REVISION,
        ]);

        Http::fake([
            "http://ciudadano.test/api/documentos-personales/{$documento->id_documento}/archivo" => Http::response(
                '%PDF-1.4 fake content',
                200,
                ['Content-Type' => 'application/pdf'],
            ),
        ]);

        $this->get(route('visualizarDocumentoPersonal', $documento->id_documento));

        Http::assertSent(function (Request $request) use ($documento): bool {
            return $request->url() === "http://ciudadano.test/api/documentos-personales/{$documento->id_documento}/archivo"
                && $request->hasHeader('X-Api-Token', 'token-compartido-de-prueba');
        });
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
