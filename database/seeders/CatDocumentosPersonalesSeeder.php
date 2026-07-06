<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatDocumentosPersonalesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('cat_documentos_personales')->insert([
            0 => [
                'id_documento' => 1,
                'nombre_documento' => 'CURP',
                'descripcion_documento' => 'Clave Única de Registro de Población. Documento de identidad oficial que asigna un código alfanumérico único a cada ciudadano mexicano.',
                'vigencia_meses' => 0,
                'estatus_documento' => 1,
                'created_at' => '2026-07-01 08:49:35',
                'updated_at' => '2026-07-06 11:25:14',
            ],
            1 => [
                'id_documento' => 2,
                'nombre_documento' => 'Acta de Nacimiento',
                'descripcion_documento' => 'Documento oficial emitido por el Registro Civil que certifica el nacimiento de una persona.',
                'vigencia_meses' => 0,
                'estatus_documento' => 1,
                'created_at' => '2026-07-01 11:51:05',
                'updated_at' => '2026-07-06 11:25:14',
            ],
            2 => [
                'id_documento' => 3,
                'nombre_documento' => 'Comprobante de Domicilio',
                'descripcion_documento' => 'Documento que acredita la residencia del ciudadano. Debe tener una antigüedad no mayor a 3 meses.',
                'vigencia_meses' => 3,
                'estatus_documento' => 1,
                'created_at' => '2026-07-01 11:51:11',
                'updated_at' => '2026-07-06 11:25:14',
            ],
            3 => [
                'id_documento' => 4,
                'nombre_documento' => 'INE',
                'descripcion_documento' => 'Identificación oficial vigente que acredita la identidad del ciudadano.',
                'vigencia_meses' => 24,
                'estatus_documento' => 1,
                'created_at' => '2026-07-02 14:17:45',
                'updated_at' => '2026-07-03 13:22:56',
            ],
        ]);
    }
}
