<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentosPersonalesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('tbl_documentos_personales')->insert([
            0 => [
                'id_documento' => 33,
                'fk_usuario' => 8,
                'fk_documento_personal' => 1,
                'fecha_registro' => '2026-07-06',
                'estatus_documento' => 1,
                'ruta_archivo' => 'documentos_personales/8/1/cBWjjqcscywJcmMbsZeMew4U7BW8PFTDY5HEECmO.pdf',
                'created_at' => '2026-07-06 15:51:22',
                'updated_at' => '2026-07-06 15:51:22',
            ],
            1 => [
                'id_documento' => 34,
                'fk_usuario' => 8,
                'fk_documento_personal' => 3,
                'fecha_registro' => '2026-07-06',
                'estatus_documento' => 1,
                'ruta_archivo' => 'documentos_personales/8/3/EyloKOwkOPrqIpye4Q5JAkfGutVhfQzvipIb5ZIJ.pdf',
                'created_at' => '2026-07-06 15:51:57',
                'updated_at' => '2026-07-06 15:51:57',
            ],
            2 => [
                'id_documento' => 35,
                'fk_usuario' => 8,
                'fk_documento_personal' => 2,
                'fecha_registro' => '2026-07-06',
                'estatus_documento' => 1,
                'ruta_archivo' => 'documentos_personales/8/2/Xs61OHa3d3u5zmo0HbsNDioZJPUEsn2PlieTYvFy.pdf',
                'created_at' => '2026-07-06 15:52:37',
                'updated_at' => '2026-07-06 15:52:37',
            ],
            3 => [
                'id_documento' => 36,
                'fk_usuario' => 8,
                'fk_documento_personal' => 4,
                'fecha_registro' => '2026-07-06',
                'estatus_documento' => 1,
                'ruta_archivo' => 'documentos_personales/8/4/xpV0EGX2WlvcWNz4s1TX9YtyttqJTBgXRBR6Odu3.pdf',
                'created_at' => '2026-07-06 15:53:49',
                'updated_at' => '2026-07-06 15:53:49',
            ],
        ]);
    }
}
