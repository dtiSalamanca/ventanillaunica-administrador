<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_documentos_predios', function (Blueprint $table) {
            $table->dropColumn('nombre_documento');
            $table->unsignedBigInteger('fk_cat_documento_predio')->after('id_documento_predio');
            $table->foreign('fk_cat_documento_predio')->references('id_documento_predio')->on('cat_documentos_predios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_documentos_predios', function (Blueprint $table) {
            $table->dropForeign('tbl_documentos_predios_fk_cat_documento_predio_foreign');
            $table->dropColumn('fk_cat_documento_predio');
            $table->string('nombre_documento')->after('id_documento_predio');
        });
    }
};
