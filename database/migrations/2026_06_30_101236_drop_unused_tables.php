<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('tbl_predios_docs');
        Schema::dropIfExists('tbl_predios');
        Schema::dropIfExists('tbl_personales');
        Schema::dropIfExists('tbl_extras');
    }

    public function down(): void
    {
        Schema::create('tbl_extras', function (Blueprint $table) {
            $table->id('id_extra');
            $table->string('nombre');
            $table->date('validez')->nullable()->default('2000-01-01');
            $table->unsignedInteger('fk_ciudadano');
            $table->timestamps();
        });

        Schema::create('tbl_personales', function (Blueprint $table) {
            $table->id('id_personal');
            $table->unsignedInteger('fk_ciudadano');
            $table->string('nombre_documento');
            $table->string('ruta_documento');
            $table->date('validez')->nullable()->default('2000-01-01');
            $table->timestamps();
        });

        Schema::create('tbl_predios', function (Blueprint $table) {
            $table->id('id_predio');
            $table->string('clave_catastral');
            $table->unsignedInteger('fk_ciudadano');
            $table->timestamps();
        });

        Schema::create('tbl_predios_docs', function (Blueprint $table) {
            $table->id('id_predio_doc');
            $table->unsignedInteger('fk_predio');
            $table->string('nombre_documento');
            $table->string('ruta_documento');
            $table->date('validez')->nullable()->default('2000-01-01');
            $table->timestamps();
        });
    }
};
