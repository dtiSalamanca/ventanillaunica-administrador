<?php

namespace Tests\Feature;

use App\Models\Dependencia;
use App\Models\Requisito;
use App\Models\RequisitoTramite;
use App\Models\Tramite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TramitesQuitarRequisitoTest extends TestCase
{
    use RefreshDatabase;

    public function test_quita_un_requisito_asignado_al_tramite(): void
    {
        $usuario = User::factory()->create();

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

        $requisito = Requisito::create([
            'nombre_requisito' => 'INE vigente',
            'descripcion_requisito' => 'Identificación oficial.',
            'estatus_requisito' => true,
        ]);

        // El id que manda el front es la PK de la tabla pivote (tbl_requisitos_tramites.id_requisito).
        $pivote = RequisitoTramite::create([
            'fk_requisito' => $requisito->id_requisito,
            'fk_tramite' => $tramite->id_tramite,
        ]);

        $response = $this->actingAs($usuario)
            ->post("/tramites/requisitos/{$tramite->id_tramite}/quitar/{$pivote->id_requisito}");

        $response->assertOk();
        $response->assertJson(['message' => 'Requisito quitado del trámite correctamente.']);

        $this->assertDatabaseMissing('tbl_requisitos_tramites', [
            'id_requisito' => $pivote->id_requisito,
        ]);
    }

    public function test_devuelve_404_si_el_requisito_no_existe(): void
    {
        $usuario = User::factory()->create();

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

        $response = $this->actingAs($usuario)
            ->post("/tramites/requisitos/{$tramite->id_tramite}/quitar/99999");

        $response->assertNotFound();
    }
}
