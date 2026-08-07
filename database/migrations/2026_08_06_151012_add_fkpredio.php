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
        //
        Schema::table('tbl_requisitos_tramites', function (Blueprint $table) {
            $table->integer('fk_predio')->after('fk_requisito')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('tbl_requisitos_tramites', function (Blueprint $table) {
            $table->dropColumn('fk_predio');
        });
    }
};
