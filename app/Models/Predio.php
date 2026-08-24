<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Predio extends Model
{
    public const ESTATUS_POR_REVISAR = -1;

    public const ESTATUS_RECHAZADO = 0;

    public const ESTATUS_EN_REVISION = 1;

    public const ESTATUS_APROBADO = 2;

    /**
     * Resultado de la consulta contra el sistema de predial.
     * 0 = sin consultar, 1 = no existe (rechazado), 2 = existe.
     */
    public const CONSULTADO_SIN_CONSULTAR = 0;

    public const CONSULTADO_NO_EXISTE = 1;

    public const CONSULTADO_EXISTE = 2;

    protected $table = 'tbl_predios';

    protected $primaryKey = 'id_predio';

    protected $fillable = [
        'clave_predio',
        'estatus_predio',
        'motivo_rechazo',
        'consultado',
        'fk_usuario',
    ];

    protected $casts = [
        'consultado' => 'integer',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fk_usuario', 'id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoPredio::class, 'fk_predio', 'id_predio');
    }
}
