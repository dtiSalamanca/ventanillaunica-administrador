<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentosPrediosSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('tbl_documentos_predios')->insert([
            0 => [
                'id_documento_predio' => 1,
                'fk_cat_documento_predio' => 5,
                'ruta_documento' => 'documentos_predios/8/1/5/F8F31HQk6ROyIrd7K6HlwYo2QX42mGQKQZmox73N.pdf',
                'fk_predio' => 1,
                'estatus_documento' => 2,
                'created_at' => '2026-07-06 15:54:25',
                'updated_at' => '2026-07-06 13:14:37',
            ],
            1 => [
                'id_documento_predio' => 2,
                'fk_cat_documento_predio' => 2,
                'ruta_documento' => 'documentos_predios/8/1/2/MLjFaPVGIrHjuEx8vury7JruOm7s2AVXeL2kN5r4.pdf',
                'fk_predio' => 1,
                'estatus_documento' => 2,
                'created_at' => '2026-07-06 15:54:40',
                'updated_at' => '2026-07-06 13:16:41',
            ],
            2 => [
                'id_documento_predio' => 3,
                'fk_cat_documento_predio' => 1,
                'ruta_documento' => 'documentos_predios/8/1/1/rC0DK6O6IMhrJhDkwkMOsON9p6V3pAKQKAiuT7sM.pdf',
                'fk_predio' => 1,
                'estatus_documento' => 2,
                'created_at' => '2026-07-06 17:43:26',
                'updated_at' => '2026-07-06 13:16:45',
            ],
            3 => [
                'id_documento_predio' => 4,
                'fk_cat_documento_predio' => 3,
                'ruta_documento' => 'documentos_predios/8/1/3/SF8oOlzZYWKGRb1DvXCLaUt2jA3mVTByQPS1LnVQ.pdf',
                'fk_predio' => 1,
                'estatus_documento' => 2,
                'created_at' => '2026-07-06 17:44:16',
                'updated_at' => '2026-07-06 13:16:47',
            ],
        ]);
    }
}
