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
        Schema::create('tbl_predios', function (Blueprint $table) {
            $table->bigIncrements('id_predio');
            $table->string('clave_predio');
            $table->integer('estatus_predio')->default(-1);
            $table->unsignedBigInteger('fk_user');
            $table->timestamps();

            $table->foreign('fk_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_predios');
    }
};
