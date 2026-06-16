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
        Schema::create('tbl_tramites', function (Blueprint $table) {
            $table->id('id_tramite');
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('fk_dependencia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_tramites');
    }
};
