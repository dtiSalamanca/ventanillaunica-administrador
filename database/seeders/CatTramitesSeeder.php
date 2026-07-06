<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatTramitesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('cat_tramites')->insert([
            0 => [
                'id_tramite' => 4,
                'nombre_tramite' => 'Licencia de Construcción',
                'descripcion_tramite' => 'Trámite para obtener la autorización oficial que permite iniciar obras de construcción nueva dentro del municipio.',
                'estatus_tramite' => 1,
                'fk_dependencia' => 4,
                'precio_tramite' => '1850.00',
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 10:07:39',
            ],
            1 => [
                'id_tramite' => 5,
                'nombre_tramite' => 'Licencia de Uso de Suelo',
                'descripcion_tramite' => 'Solicitud para determinar y autorizar el uso de suelo permitido en un predio conforme al plan de desarrollo urbano.',
                'estatus_tramite' => 1,
                'fk_dependencia' => 4,
                'precio_tramite' => '950.00',
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 10:07:39',
            ],
            2 => [
                'id_tramite' => 6,
                'nombre_tramite' => 'Manifestación de Construcción',
                'descripcion_tramite' => 'Registro y autorización de proyectos de construcción, ampliación o remodelación ante la dependencia correspondiente.',
                'estatus_tramite' => 1,
                'fk_dependencia' => 4,
                'precio_tramite' => '2300.00',
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 10:07:39',
            ],
            3 => [
                'id_tramite' => 7,
                'nombre_tramite' => 'Pago de Impuesto Predial',
                'descripcion_tramite' => 'Pago anual o periódico del impuesto predial correspondiente a un inmueble registrado en el padrón catastral.',
                'estatus_tramite' => 1,
                'fk_dependencia' => 5,
                'precio_tramite' => '620.00',
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 10:07:39',
            ],
            4 => [
                'id_tramite' => 8,
                'nombre_tramite' => 'Constancia de No Adeudo de Predial',
                'descripcion_tramite' => 'Documento que certifica que un predio no tiene adeudos pendientes de impuesto predial.',
                'estatus_tramite' => 1,
                'fk_dependencia' => 5,
                'precio_tramite' => '150.00',
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 10:07:39',
            ],
            5 => [
                'id_tramite' => 9,
                'nombre_tramite' => 'Acta de Nacimiento (copia certificada)',
                'descripcion_tramite' => 'Emisión de copia certificada del acta de nacimiento registrada en el Registro Civil.',
                'estatus_tramite' => 1,
                'fk_dependencia' => 6,
                'precio_tramite' => '120.00',
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 10:07:39',
            ],
            6 => [
                'id_tramite' => 10,
                'nombre_tramite' => 'Acta de Matrimonio',
                'descripcion_tramite' => 'Emisión de copia certificada del acta de matrimonio registrada en el Registro Civil.',
                'estatus_tramite' => 1,
                'fk_dependencia' => 6,
                'precio_tramite' => '180.00',
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 10:07:39',
            ],
            7 => [
                'id_tramite' => 11,
                'nombre_tramite' => 'Avalúo Catastral',
                'descripcion_tramite' => 'Determinación del valor catastral de un predio con fines fiscales y administrativos.',
                'estatus_tramite' => 1,
                'fk_dependencia' => 7,
                'precio_tramite' => '500.00',
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 10:07:39',
            ],
            8 => [
                'id_tramite' => 12,
                'nombre_tramite' => 'Constancia Catastral',
                'descripcion_tramite' => 'Documento que acredita la información catastral vigente de un predio.',
                'estatus_tramite' => 1,
                'fk_dependencia' => 7,
                'precio_tramite' => '200.00',
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 10:07:39',
            ],
            9 => [
                'id_tramite' => 13,
                'nombre_tramite' => 'Permiso de Obra Menor',
                'descripcion_tramite' => 'Autorización para realizar trabajos de construcción de bajo impacto, como bardas o reparaciones menores.',
                'estatus_tramite' => 1,
                'fk_dependencia' => 8,
                'precio_tramite' => '450.00',
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 10:07:39',
            ],
            10 => [
                'id_tramite' => 14,
                'nombre_tramite' => 'Solicitud de Baja de Servicios (agua/alumbrado)',
                'descripcion_tramite' => 'Solicitud para dar de baja servicios municipales como agua potable o alumbrado público asociados a un predio.',
                'estatus_tramite' => 1,
                'fk_dependencia' => 9,
                'precio_tramite' => '100.00',
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 10:07:39',
            ],
        ]);
    }
}
