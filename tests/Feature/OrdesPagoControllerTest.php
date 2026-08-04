<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrdesPagoControllerTest extends TestCase
{
    public function test_it_proxies_the_cuentas_cri_request_to_the_internal_api(): void
    {
        Http::fake([
            'http://172.17.5.214/sisalamanca/public/api/consultaCuentasCri*' => Http::response([
                'success' => true,
                'data' => ['cuenta' => '12345'],
            ], 200, ['Content-Type' => 'application/json']),
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/ajax/ordenes-pago/consulta-cuentas-cri?cuenta=12345');

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => ['cuenta' => '12345'],
        ]);

        Http::assertSent(function ($request) {
            return $request->method() === 'GET'
                && str_contains($request->url(), '172.17.5.214/sisalamanca/public/api/consultaCuentasCri')
                && str_contains($request->url(), 'cuenta=12345');
        });
    }
}
