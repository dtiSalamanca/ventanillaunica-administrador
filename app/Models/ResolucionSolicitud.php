<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResolucionSolicitud extends Model
{
    protected $table = 'tbl_resoluciones_solicitudes';

    protected $primaryKey = 'id_resolucion';

    protected $fillable = [
        'fk_turnado',
        'resolucion_solicitud',
        'documento_resolucion',
    ];

    public function turnado(): BelongsTo
    {
        return $this->belongsTo(TurnadoSolicitud::class, 'fk_turnado', 'id_turnado');
    }
}
