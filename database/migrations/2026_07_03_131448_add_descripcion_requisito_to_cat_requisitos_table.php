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
        Schema::table('cat_requisitos', function (Blueprint $table) {
            $table->text('descripcion_requisito')->nullable()->after('nombre_requisito');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cat_requisitos', function (Blueprint $table) {
            $table->dropColumn('descripcion_requisito');
        });
    }
};
