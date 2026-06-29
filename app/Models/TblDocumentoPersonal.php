<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TblDocumentoPersonal extends Model
{
    protected $table = 'tbl_documentos_personales';

    protected $primaryKey = 'id_documento';

    protected $fillable = [
        'fk_usuario',
        'fk_documento_personal',
        'fecha_registro',
        'estatus_documento',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fk_usuario', 'id');
    }

    public function catalogoDocumento(): BelongsTo
    {
        return $this->belongsTo(CatDocumentoPersonal::class, 'fk_documento_personal', 'id_documento');
    }
}
