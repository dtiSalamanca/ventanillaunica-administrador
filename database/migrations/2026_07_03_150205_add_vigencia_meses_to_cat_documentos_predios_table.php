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
        Schema::table('cat_documentos_predios', function (Blueprint $table) {
            $table->integer('vigencia_meses')->after('nombre_documento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cat_documentos_predios', function (Blueprint $table) {
            $table->dropColumn('vigencia_meses');
        });
    }
};
