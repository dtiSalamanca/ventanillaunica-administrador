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
        Schema::create('cat_predios', function (Blueprint $table) {
            $table->id('id_predio');
            $table->string('clave_catastral');
            $table->integer('propietario')->default(1)->comment('1 si es propietario, 0 no es propietario');
            $table->integer('renta')->default(1)->comment('1 si es rentado, 0 no es rentado');
            $table->unsignedInteger('fk_ciudadano');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_predios');
    }
};
