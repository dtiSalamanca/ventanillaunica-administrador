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
            $table->decimal('precio_tramite', 10, 2)->default(0)->after('fk_dependencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cat_tramites', function (Blueprint $table) {
            $table->dropColumn('precio_tramite');
        });
    }
};
