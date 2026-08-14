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
        Schema::table('tbl_requisitos_tramites', function (Blueprint $table) {
            $table->string('fk_requisito', 255)->nullable()->change();
            $table->string('fk_predio', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_requisitos_tramites', function (Blueprint $table) {
            $table->unsignedBigInteger('fk_requisito')->nullable()->change();
            $table->unsignedBigInteger('fk_predio')->nullable()->change();
        });
    }
};
