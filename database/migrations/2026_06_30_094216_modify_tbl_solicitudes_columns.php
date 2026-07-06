<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_solicitudes', function (Blueprint $table) {
            $table->renameColumn('fk_ciudadano', 'fk_usuario');
            $table->unsignedBigInteger('fk_tramite')->change();
            $table->dateTime('fecha_solicitud')->nullable()->change();
            $table->dateTime('fecha_resolucion')->nullable()->default(null)->change();
            $table->text('observacion_solicitud')->nullable()->after('fecha_resolucion');
            $table->date('validez_solicitud')->nullable()->after('observacion_solicitud');
            $table->integer('estatus_solicitud')->default(0)->after('validez_solicitud');
        });

        Schema::table('tbl_solicitudes', function (Blueprint $table) {
            $table->dropColumn(['estatus', 'observaciones', 'validez']);
        });
    }

    public function down(): void
    {
        Schema::table('tbl_solicitudes', function (Blueprint $table) {
            $table->renameColumn('fk_usuario', 'fk_ciudadano');
            $table->unsignedInteger('fk_tramite')->change();
            $table->date('fecha_solicitud')->nullable()->change();
            $table->date('fecha_resolucion')->nullable()->default('2000-01-01')->change();
            $table->string('estatus')->default('En proceso')->after('fk_tramite');
            $table->string('observaciones')->nullable()->after('fecha_resolucion');
            $table->date('validez')->nullable()->default('2000-01-01')->after('observaciones');
        });

        Schema::table('tbl_solicitudes', function (Blueprint $table) {
            $table->dropColumn(['observacion_solicitud', 'validez_solicitud', 'estatus_solicitud']);
        });
    }
};
