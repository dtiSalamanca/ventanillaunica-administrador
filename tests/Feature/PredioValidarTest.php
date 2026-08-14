<?php

namespace Tests\Feature;

use App\Auth\AdUser;
use App\Mail\PredioRevisado;
use App\Models\Predio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PredioValidarTest extends TestCase
{
    use RefreshDatabase;

    public function test_validar_aprueba_automaticamente_cuando_el_predio_existe_en_predial(): void
    {
        $this->authenticateAdUser();
        Mail::fake();

        $predio = $this->crearPredio(Predio::ESTATUS_POR_REVISAR);

        Http::fake([
            'https://recibopredial.salamanca.gob.mx/api/consulta/predio/*' => Http::response(['respuesta' => true]),
        ]);

        $response = $this->getJson(route('predio.validar', ['clave' => $predio->clave_predio]));

        $response->assertOk();
        $response->assertJson([
            'existe' => true,
            'aprobado' => true,
        ]);

        $this->assertDatabaseHas('tbl_predios', [
            'id_predio' => $predio->id_predio,
            'estatus_predio' => Predio::ESTATUS_APROBADO,
            'consultado' => Predio::CONSULTADO_EXISTE,
        ]);

        Mail::assertSent(PredioRevisado::class, function (PredioRevisado $mail) use ($predio): bool {
            return $mail->hasTo($predio->usuario->email);
        });
    }

    public function test_validar_rechaza_el_predio_con_motivo_cuando_no_existe_en_predial(): void
    {
        $this->authenticateAdUser();

        $predio = $this->crearPredio(Predio::ESTATUS_POR_REVISAR);

        Http::fake([
            'https://recibopredial.salamanca.gob.mx/api/consulta/predio/*' => Http::response(['respuesta' => false], 404),
        ]);

        $response = $this->getJson(route('predio.validar', ['clave' => $predio->clave_predio]));

        $response->assertJson(['existe' => false]);

        $this->assertDatabaseHas('tbl_predios', [
            'id_predio' => $predio->id_predio,
            'estatus_predio' => Predio::ESTATUS_RECHAZADO,
            'consultado' => Predio::CONSULTADO_NO_EXISTE,
            'motivo_rechazo' => 'El predio registrado no existe en el sistema de predial.',
        ]);
    }

    public function test_validar_responde_502_cuando_el_servicio_responde_de_forma_inesperada(): void
    {
        $this->authenticateAdUser();

        $predio = $this->crearPredio(Predio::ESTATUS_POR_REVISAR);

        Http::fake([
            'https://recibopredial.salamanca.gob.mx/api/consulta/predio/*' => Http::response('<html>error</html>', 500),
        ]);

        $response = $this->getJson(route('predio.validar', ['clave' => $predio->clave_predio]));

        $response->assertStatus(502);
        $response->assertJson(['existe' => null]);
    }

    public function test_validar_responde_502_cuando_no_se_puede_conectar_al_servicio(): void
    {
        $this->authenticateAdUser();

        $predio = $this->crearPredio(Predio::ESTATUS_POR_REVISAR);

        Http::fake([
            'https://recibopredial.salamanca.gob.mx/api/consulta/predio/*' => function () {
                throw new ConnectionException('Connection refused');
            },
        ]);

        $response = $this->getJson(route('predio.validar', ['clave' => $predio->clave_predio]));

        $response->assertStatus(502);
        $response->assertJson(['existe' => null]);
    }

    public function test_validar_responde_404_si_la_cuenta_no_esta_registrada(): void
    {
        $this->authenticateAdUser();

        Http::fake();

        $response = $this->getJson(route('predio.validar', ['clave' => 'NO-EXISTE']));

        $response->assertStatus(404);
        $response->assertJson(['existe' => false]);
    }

    private function crearPredio(int $estatus): Predio
    {
        $usuario = User::factory()->create();

        return Predio::create([
            'clave_predio' => '25D000057002',
            'estatus_predio' => $estatus,
            'fk_usuario' => $usuario->id,
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
