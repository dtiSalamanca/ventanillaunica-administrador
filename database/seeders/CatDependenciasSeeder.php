<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatDependenciasSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('cat_dependencias')->insert([
            0 => [
                'id_dependencia' => 4,
                'nombre_dependencia' => 'Desarrollo Urbano y Ordenamiento Territorial',
                'estatus_dependencia' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-02 17:57:32',
            ],
            1 => [
                'id_dependencia' => 5,
                'nombre_dependencia' => 'Tesorería Municipal',
                'estatus_dependencia' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-02 17:57:32',
            ],
            2 => [
                'id_dependencia' => 6,
                'nombre_dependencia' => 'Registro Civil',
                'estatus_dependencia' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-02 17:57:32',
            ],
            3 => [
                'id_dependencia' => 7,
                'nombre_dependencia' => 'Catastro Municipal',
                'estatus_dependencia' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-02 17:57:32',
            ],
            4 => [
                'id_dependencia' => 8,
                'nombre_dependencia' => 'Obras Públicas',
                'estatus_dependencia' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-02 17:57:32',
            ],
            5 => [
                'id_dependencia' => 9,
                'nombre_dependencia' => 'Servicios Públicos Municipales',
                'estatus_dependencia' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-02 17:57:32',
            ],
        ]);
    }
}
