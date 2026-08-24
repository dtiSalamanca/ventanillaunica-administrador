<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::table('tbl_predios', function (Blueprint $table) {
            $table->unique('clave_predio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_predios', function (Blueprint $table) {
            // $table->dropUnique('tbl_predios_clave_predio_unique');
            $table->dropUnique(['clave_predio']);
            // También puedes usar:
            // $table->dropUnique('nombre_tabla_nombre_campo_unique');
        });
    }
};
