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
        if (! Schema::hasTable('cat_tramites') || Schema::hasColumn('cat_tramites', 'sin_costo')) {
            return;
        }

        Schema::table('cat_tramites', function (Blueprint $table) {
            $table->boolean('sin_costo')->default(false)->after('cuenta_predial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('cat_tramites') && Schema::hasColumn('cat_tramites', 'sin_costo')) {
            Schema::table('cat_tramites', function (Blueprint $table) {
                $table->dropColumn('sin_costo');
            });
        }
    }
};
