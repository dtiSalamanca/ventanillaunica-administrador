<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La cuenta predial ya no se exige por defecto: los trámites nuevos
     * deben quedar sin requerir cuenta predial a menos que el admin lo marque.
     */
    public function up(): void
    {
        if (! Schema::hasTable('cat_tramites') || ! Schema::hasColumn('cat_tramites', 'cuenta_predial')) {
            return;
        }

        Schema::table('cat_tramites', function (Blueprint $table) {
            $table->integer('cuenta_predial')->default(0)->change();
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('cat_tramites') && Schema::hasColumn('cat_tramites', 'cuenta_predial')) {
            Schema::table('cat_tramites', function (Blueprint $table) {
                $table->integer('cuenta_predial')->default(1)->change();
            });
        }
    }
};
