<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoTramite extends Model
{
    protected $table = 'tbl_documentos_tramites';

    protected $primaryKey = 'id_documento';

    protected $fillable = [
        'fk_requisito',
        'fk_documento_personal',
        'fk_documento_solicitud',
        'fk_solicitud',
    ];

    public function requisito(): BelongsTo
    {
        return $this->belongsTo(Requisito::class, 'fk_requisito', 'id_requisito');
    }

    public function documentoPersonal(): BelongsTo
    {
        return $this->belongsTo(tblDocumentoPersonal::class, 'fk_documento_personal', 'id_documento');
    }

    public function documentoSolicitud(): BelongsTo
    {
        return $this->belongsTo(DocumentoSolicitud::class, 'fk_documento_solicitud', 'id_documento');
    }
}
