<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tramite extends Model
{
    protected $table = 'cat_tramites';

    protected $primaryKey = 'id_tramite';

    protected $fillable = [
        'nombre_tramite',
        'descripcion_tramite',
        'estatus_tramite',
        'fk_dependencia',
        'precio_tramite',
    ];

    public function dependencia(): BelongsTo
    {
        return $this->belongsTo(Dependencia::class, 'fk_dependencia', 'id_dependencia');
    }

    public function requisitos(): BelongsToMany
    {
        return $this->belongsToMany(Requisito::class, 'tbl_requisitos_tramites', 'fk_tramite', 'fk_requisito', 'id_tramite', 'id_requisito');
    }

    /**
     * Trámites que este trámite requiere como prerequisito.
     */
    public function tramitesRequeridos(): BelongsToMany
    {
        return $this->belongsToMany(
            Tramite::class,
            'tbl_tramites_prerequisitos',
            'fk_tramite',
            'fk_tramite_requerido',
            'id_tramite',
            'id_tramite'
        )->withTimestamps();
    }

    /**
     * Trámites que requieren este trámite como prerequisito.
     */
    public function tramitesQueLoRequieren(): BelongsToMany
    {
        return $this->belongsToMany(
            Tramite::class,
            'tbl_tramites_prerequisitos',
            'fk_tramite_requerido',
            'fk_tramite',
            'id_tramite',
            'id_tramite'
        );
    }
}
