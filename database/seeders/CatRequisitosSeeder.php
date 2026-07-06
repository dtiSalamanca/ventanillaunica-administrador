<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatRequisitosSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('cat_requisitos')->insert([
            0 => [
                'id_requisito' => 5,
                'nombre_requisito' => 'Identificación oficial vigente (INE/IFE)',
                'descripcion_requisito' => 'Documento vigente que acredita la identidad del solicitante (INE o IFE).',
                'estatus_requisito' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 13:16:21',
            ],
            1 => [
                'id_requisito' => 6,
                'nombre_requisito' => 'Clave Única de Registro de Población (CURP)',
                'descripcion_requisito' => 'Clave alfanumérica de 18 caracteres que identifica al ciudadano a nivel nacional.',
                'estatus_requisito' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 13:16:21',
            ],
            2 => [
                'id_requisito' => 7,
                'nombre_requisito' => 'Comprobante de domicilio (no mayor a 3 meses)',
                'descripcion_requisito' => 'Recibo de luz, agua, teléfono o predial con antigüedad no mayor a 3 meses.',
                'estatus_requisito' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 13:16:21',
            ],
            3 => [
                'id_requisito' => 8,
                'nombre_requisito' => 'Escrituras del predio debidamente inscritas',
                'descripcion_requisito' => 'Documento notarial que comprueba la propiedad del predio, inscrito en el Registro Público.',
                'estatus_requisito' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 13:16:21',
            ],
            4 => [
                'id_requisito' => 9,
                'nombre_requisito' => 'Plano arquitectónico autorizado',
                'descripcion_requisito' => 'Plano elaborado por profesional validado y autorizado por el área correspondiente.',
                'estatus_requisito' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 13:16:21',
            ],
            5 => [
                'id_requisito' => 10,
                'nombre_requisito' => 'Recibo de pago del impuesto predial del año en curso',
                'descripcion_requisito' => 'Comprobante del pago del impuesto predial correspondiente al año en curso.',
                'estatus_requisito' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 13:16:21',
            ],
            6 => [
                'id_requisito' => 11,
                'nombre_requisito' => 'Solicitud por escrito dirigida al titular del área',
                'descripcion_requisito' => 'Escrito libre dirigido al titular del área motivando la solicitud.',
                'estatus_requisito' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 13:16:21',
            ],
            7 => [
                'id_requisito' => 12,
                'nombre_requisito' => 'Fotografías del inmueble (frente y lateral)',
                'descripcion_requisito' => 'Fotografías recientes mostrando la fachada frontal y lateral del inmueble.',
                'estatus_requisito' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 13:16:21',
            ],
            8 => [
                'id_requisito' => 13,
                'nombre_requisito' => 'Acta de nacimiento',
                'descripcion_requisito' => 'Copia certificada del acta de nacimiento expedida por el Registro Civil.',
                'estatus_requisito' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 13:16:21',
            ],
            9 => [
                'id_requisito' => 14,
                'nombre_requisito' => 'Comprobante de pago de derechos',
                'descripcion_requisito' => 'Comprobante que acredita el pago de los derechos correspondientes al trámite.',
                'estatus_requisito' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 13:16:21',
            ],
            10 => [
                'id_requisito' => 15,
                'nombre_requisito' => 'Registro Federal de Contribuyentes (RFC)',
                'descripcion_requisito' => 'Cédula de identificación fiscal con la Clave del Registro Federal de Contribuyentes.',
                'estatus_requisito' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 13:16:21',
            ],
            11 => [
                'id_requisito' => 16,
                'nombre_requisito' => 'Poder legal o carta poder (en caso de representación)',
                'descripcion_requisito' => 'Instrumento público o carta poder notarial que acredita la representación del solicitante.',
                'estatus_requisito' => 1,
                'created_at' => '2026-07-02 17:57:32',
                'updated_at' => '2026-07-03 13:16:21',
            ],
        ]);
    }
}
