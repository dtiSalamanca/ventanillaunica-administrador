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
        Schema::create('tbl_turnados_solicitudes', function (Blueprint $table) {
            $table->id('id_turnado');
            $table->unsignedBigInteger('fk_usuario_ad');
            $table->unsignedBigInteger('fk_solicitud');
            $table->boolean('estatus_turnado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_turnados_solicitudes');
    }
};
