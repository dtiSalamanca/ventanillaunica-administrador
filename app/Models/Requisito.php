<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requisito extends Model
{
    protected $table = 'tbl_requisitos';

    protected $primaryKey = 'id_requisito';

    protected $fillable = [
        'nombre',
        'activo',
        'fk_tramite',
    ];

    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class, 'fk_tramite', 'id_tramite');
    }
}
