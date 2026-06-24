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
        Schema::table('tbl_requisitos', function (Blueprint $table) {
            $table->dropColumn('fk_tramite');
        });

        Schema::rename('tbl_requisitos', 'cat_requisitos');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('cat_requisitos', 'tbl_requisitos');

        Schema::table('tbl_requisitos', function (Blueprint $table) {
            $table->unsignedInteger('fk_tramite')->after('activo');
        });
    }
};
