<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatDocumentosPrediosSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('cat_documentos_predios')->insert([
            0 => [
                'id_documento_predio' => 1,
                'nombre_documento' => 'Escritura del predio',
                'vigencia_meses' => 12,
                'estatus_documento' => 1,
                'created_at' => '2026-07-03 15:05:08',
                'updated_at' => '2026-07-03 15:05:08',
            ],
            1 => [
                'id_documento_predio' => 2,
                'nombre_documento' => 'Boleta predial',
                'vigencia_meses' => 6,
                'estatus_documento' => 1,
                'created_at' => '2026-07-03 15:05:08',
                'updated_at' => '2026-07-03 15:05:08',
            ],
            2 => [
                'id_documento_predio' => 3,
                'nombre_documento' => 'Plano de ubicación',
                'vigencia_meses' => 24,
                'estatus_documento' => 1,
                'created_at' => '2026-07-03 15:05:08',
                'updated_at' => '2026-07-03 15:05:08',
            ],
            3 => [
                'id_documento_predio' => 4,
                'nombre_documento' => 'Comprobante de pago de impuestos',
                'vigencia_meses' => 3,
                'estatus_documento' => 0,
                'created_at' => '2026-07-03 15:05:08',
                'updated_at' => '2026-07-03 15:05:08',
            ],
            4 => [
                'id_documento_predio' => 5,
                'nombre_documento' => 'aaac',
                'vigencia_meses' => 23,
                'estatus_documento' => 1,
                'created_at' => '2026-07-03 15:05:42',
                'updated_at' => '2026-07-03 15:05:55',
            ],
        ]);
    }
}
