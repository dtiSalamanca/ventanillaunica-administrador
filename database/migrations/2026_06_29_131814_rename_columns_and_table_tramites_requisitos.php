<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cat_requisitos', function (Blueprint $table) {
            $table->renameColumn('nombre', 'nombre_requisito');
            $table->renameColumn('activo', 'estatus_requisito');
        });

        Schema::rename('tbl_tramites', 'cat_tramites');

        Schema::table('cat_tramites', function (Blueprint $table) {
            $table->renameColumn('nombre', 'nombre_tramite');
            $table->renameColumn('activo', 'estatus_tramite');
        });
    }

    public function down(): void
    {
        Schema::table('cat_requisitos', function (Blueprint $table) {
            $table->renameColumn('nombre_requisito', 'nombre');
            $table->renameColumn('estatus_requisito', 'activo');
        });

        Schema::table('cat_tramites', function (Blueprint $table) {
            $table->renameColumn('nombre_tramite', 'nombre');
            $table->renameColumn('estatus_tramite', 'activo');
        });

        Schema::rename('cat_tramites', 'tbl_tramites');
    }
};
