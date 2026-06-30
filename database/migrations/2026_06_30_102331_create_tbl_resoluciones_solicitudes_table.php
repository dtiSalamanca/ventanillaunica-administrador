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
        Schema::create('tbl_resoluciones_solicitudes', function (Blueprint $table) {
            $table->id('id_resolucion');
            $table->unsignedBigInteger('fk_turnado');
            $table->text('resolucion_solicitud');
            $table->string('documento_resolucion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_resoluciones_solicitudes');
    }
};
