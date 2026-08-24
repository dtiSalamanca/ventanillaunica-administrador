<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fecha en que el administrador aprobó el documento. La vigencia del
     * documento empieza a correr a partir de esta fecha (o de la fecha de
     * registro como respaldo para documentos aprobados antes de este cambio).
     */
    public function up(): void
    {
        Schema::table('tbl_documentos_personales', function (Blueprint $table) {
            $table->date('fecha_aprobacion')->nullable()->after('fecha_registro');
        });

        Schema::table('tbl_documentos_predios', function (Blueprint $table) {
            $table->date('fecha_aprobacion')->nullable()->after('estatus_documento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_documentos_personales', function (Blueprint $table) {
            $table->dropColumn('fecha_aprobacion');
        });

        Schema::table('tbl_documentos_predios', function (Blueprint $table) {
            $table->dropColumn('fecha_aprobacion');
        });
    }
};
