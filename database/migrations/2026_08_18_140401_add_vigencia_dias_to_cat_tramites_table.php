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
        Schema::table('cat_tramites', function (Blueprint $table) {
            $table->unsignedInteger('vigencia_dias')->default(0)->after('cobra_por_m2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cat_tramites', function (Blueprint $table) {
            $table->dropColumn('vigencia_dias');
        });
    }
};
