<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenPago extends Model
{
    use HasFactory;

    protected $table = 'ordenes_pagos';

    protected $primaryKey = 'id_orden_pago';

    protected $fillable = [
        'nombre_tramite',
        'precio_tramite',
        'numero_cri',
        'orden_estatus',
        'folio_pago',
        'fk_tramite',
        'fk_solicitud',
    ];

    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class, 'fk_tramite', 'id_tramite');
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'fk_solicitud', 'id_solicitud');
    }

    /**
     * Descripción mostrada al sistema de pagos, con el mismo formato con el que
     * se nombran los resolutivos: {siglas}-{id solicitud}-{fecha}.
     * Ej.: "DeU-01-2026-08-13".
     */
    public function descripcionResolutivo(): string
    {
        $siglas = $this->tramite?->dependencia?->siglas() ?? 'GEN';
        $idSolicitud = str_pad((string) $this->fk_solicitud, 2, '0', STR_PAD_LEFT);
        $fecha = $this->created_at?->format('Y-m-d') ?? now()->format('Y-m-d');

        return "{$siglas}-{$idSolicitud}-{$fecha}";
    }
}
