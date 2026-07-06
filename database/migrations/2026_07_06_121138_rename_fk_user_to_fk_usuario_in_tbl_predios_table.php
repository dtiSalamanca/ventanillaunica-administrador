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
            $table->dropForeign(['fk_user']);
        });

        Schema::table('tbl_predios', function (Blueprint $table) {
            $table->renameColumn('fk_user', 'fk_usuario');
        });

        Schema::table('tbl_predios', function (Blueprint $table) {
            $table->foreign('fk_usuario')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_predios', function (Blueprint $table) {
            $table->dropForeign(['fk_usuario']);
        });

        Schema::table('tbl_predios', function (Blueprint $table) {
            $table->renameColumn('fk_usuario', 'fk_user');
        });

        Schema::table('tbl_predios', function (Blueprint $table) {
            $table->foreign('fk_user')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
