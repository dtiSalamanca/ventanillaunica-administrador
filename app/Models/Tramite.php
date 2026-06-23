<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tramite extends Model
{
    protected $table = 'tbl_tramites';

    protected $primaryKey = 'id_tramite';

    protected $fillable = [
        'nombre',
        'activo',
        'fk_dependencia',
    ];

    public function dependencia(): BelongsTo
    {
        return $this->belongsTo(Dependencia::class, 'fk_dependencia', 'id_dependencia');
    }
}
