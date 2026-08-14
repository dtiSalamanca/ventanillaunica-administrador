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
            $table->boolean('cobra_por_m2')->default(false)->after('tramite_cri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cat_tramites', function (Blueprint $table) {
            $table->dropColumn('cobra_por_m2');
        });
    }
};
