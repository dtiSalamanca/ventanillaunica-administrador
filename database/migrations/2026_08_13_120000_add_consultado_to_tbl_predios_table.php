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
        Schema::table('tbl_predios', function (Blueprint $table) {
            // 0 = sin consultar, 1 = consultado y no existe (rechazado),
            // 2 = consultado y existe (puede aprobarse).
            $table->tinyInteger('consultado')->default(0)->after('estatus_predio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_predios', function (Blueprint $table) {
            $table->dropColumn('consultado');
        });
    }
};
