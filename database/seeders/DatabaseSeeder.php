<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database con los datos actuales de desarrollo,
     * para que el equipo cuente con los mismos datos de prueba. Todos los
     * usuarios seedeados quedan con la contraseña "password".
     */
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            CatDependenciasSeeder::class,
            CatTramitesSeeder::class,
            CatRequisitosSeeder::class,
            RequisitosTramitesSeeder::class,
            CatDocumentosPersonalesSeeder::class,
            CatDocumentosPrediosSeeder::class,
            PrediosSeeder::class,
            DocumentosPrediosSeeder::class,
            DocumentosPersonalesSeeder::class,
        ]);
    }
}
