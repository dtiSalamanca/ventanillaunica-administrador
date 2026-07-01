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
        Schema::create('cat_documentos_personales', function (Blueprint $table) {
            $table->id('id_documento');
            $table->string('nombre_documento');
            $table->integer('vigencia_meses');
            $table->boolean('estatus_documento')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_documentos_personales');
    }
};
