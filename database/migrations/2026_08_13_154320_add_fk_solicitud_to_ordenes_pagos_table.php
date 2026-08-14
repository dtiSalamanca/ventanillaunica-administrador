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
        Schema::table('ordenes_pagos', function (Blueprint $table) {
            $table->unsignedBigInteger('fk_solicitud')->nullable()->after('fk_tramite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordenes_pagos', function (Blueprint $table) {
            $table->dropColumn('fk_solicitud');
        });
    }
};
