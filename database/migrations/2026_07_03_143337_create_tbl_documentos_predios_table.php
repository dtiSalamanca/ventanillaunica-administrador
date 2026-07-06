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
        Schema::create('tbl_documentos_predios', function (Blueprint $table) {
            $table->bigIncrements('id_documento_predio');
            $table->string('nombre_documento');
            $table->string('ruta_documento');
            $table->unsignedBigInteger('fk_predio');
            $table->integer('estatus_documento')->default(1);
            $table->timestamps();

            $table->foreign('fk_predio')->references('id_predio')->on('tbl_predios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_documentos_predios');
    }
};
