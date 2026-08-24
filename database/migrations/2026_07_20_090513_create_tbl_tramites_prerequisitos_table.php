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
        Schema::create('tbl_tramites_prerequisitos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fk_tramite');
            $table->unsignedBigInteger('fk_tramite_requerido');
            $table->timestamps();

            $table->foreign('fk_tramite')
                ->references('id_tramite')
                ->on('cat_tramites')
                ->onDelete('cascade');

            $table->foreign('fk_tramite_requerido')
                ->references('id_tramite')
                ->on('cat_tramites')
                ->onDelete('cascade');

            $table->unique(['fk_tramite', 'fk_tramite_requerido'], 'uq_tramite_prerequisito');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_tramites_prerequisitos');
    }
};
