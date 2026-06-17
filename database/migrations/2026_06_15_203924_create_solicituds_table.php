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
        Schema::create('tbl_solicitudes', function (Blueprint $table) {
            $table->id('id_solicitud');
            $table->unsignedInteger('fk_ciudadano');
            $table->unsignedInteger('fk_tramite');
            $table->string('estatus')->default('En proceso');
            $table->date('fecha_solicitud')->nullable()->default(DB::raw('CURRENT_DATE'));
            $table->date('fecha_resolucion')->nullable()->default('2000-01-01');
            $table->string('observaciones')->nullable();
            $table->date('validez')->nullable()->default('2000-01-01');
            $table->string('folio_caja')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_solicitudes');
    }
};
