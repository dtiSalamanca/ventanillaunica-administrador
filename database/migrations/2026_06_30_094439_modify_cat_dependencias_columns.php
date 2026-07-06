<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cat_dependencias', function (Blueprint $table) {
            $table->renameColumn('nombre', 'nombre_dependencia');
            $table->renameColumn('activo', 'estatus_dependencia');
        });
    }

    public function down(): void
    {
        Schema::table('cat_dependencias', function (Blueprint $table) {
            $table->renameColumn('nombre_dependencia', 'nombre');
            $table->renameColumn('estatus_dependencia', 'activo');
        });
    }
};
