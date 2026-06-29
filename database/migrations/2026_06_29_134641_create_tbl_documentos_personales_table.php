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
        Schema::create('tbl_documentos_personales', function (Blueprint $table) {
            $table->id('id_documento');
            $table->unsignedBigInteger('fk_usuario');
            $table->unsignedBigInteger('fk_documento_personal');
            $table->date('fecha_registro');
            $table->integer('estatus_documento')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_documentos_personales');
    }
};
