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
        Schema::create('tbl_personales', function (Blueprint $table) {
            $table->id('id_personal');
            $table->unsignedInteger('fk_ciudadano');
            $table->string('nombre_documento');
            $table->string('ruta_documento');
            $table->date('validez')->nullable()->default('2000-01-01');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_personales');
    }
};
