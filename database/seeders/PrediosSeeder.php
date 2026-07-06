<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrediosSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('tbl_predios')->insert([
            0 => [
                'id_predio' => 1,
                'clave_predio' => '2000MAH2USMAZE',
                'estatus_predio' => 2,
                'fk_usuario' => 8,
                'created_at' => '2026-07-03 21:21:24',
                'updated_at' => '2026-07-06 13:39:45',
            ],
            1 => [
                'id_predio' => 2,
                'clave_predio' => 'SSSSS2222ss',
                'estatus_predio' => 2,
                'fk_usuario' => 8,
                'created_at' => '2026-07-06 19:35:06',
                'updated_at' => '2026-07-06 13:39:50',
            ],
        ]);
    }
}
