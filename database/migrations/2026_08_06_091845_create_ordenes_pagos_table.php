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
        Schema::create('ordenes_pagos', function (Blueprint $table) {
            $table->id('id_orden_pago');
            $table->string('nombre_tramite');
            $table->decimal('precio_tramite', 10, 2)->default(0);
            $table->integer('numero_cri');
            $table->integer('orden_estatus')->default(1)->comment('1: Pendiente, 2: Pagada, 3: Cancelada');
            $table->string('folio_pago')->nullable();
            $table->bigInteger('fk_tramite')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_pagos');
    }
};
