<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La columna puede existir ya en la BD compartida, pues la agrega el
     * proyecto ciudadano; por eso se protege con hasColumn.
     */
    public function up(): void
    {
        if (! Schema::hasTable('cat_tramites') || Schema::hasColumn('cat_tramites', 'cuenta_predial')) {
            return;
        }

        Schema::table('cat_tramites', function (Blueprint $table) {
            $table->integer('cuenta_predial')->default(0)->after('precio_tramite');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('cat_tramites') && Schema::hasColumn('cat_tramites', 'cuenta_predial')) {
            Schema::table('cat_tramites', function (Blueprint $table) {
                $table->dropColumn('cuenta_predial');
            });
        }
    }
};
