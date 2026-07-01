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
        Schema::create('tbl_documentos_tramites', function (Blueprint $table) {
            $table->id('id_documento');
            $table->unsignedBigInteger('fk_requisito');
            $table->unsignedBigInteger('fk_documento_personal')->nullable();
            $table->unsignedBigInteger('fk_documento_solicitud')->nullable();
            $table->unsignedBigInteger('fk_solicitud');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_documentos_tramites');
    }
};
