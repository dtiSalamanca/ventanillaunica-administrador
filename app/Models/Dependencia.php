<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dependencia extends Model
{
    protected $table = 'cat_dependencias';

    protected $primaryKey = 'id_dependencia';

    protected $fillable = [
        'nombre_dependencia',
        'estatus_dependencia',
    ];

    protected function casts(): array
    {
        return [
            'estatus_dependencia' => 'boolean',
        ];
    }

    /**
     * Siglas de la dependencia derivadas de su nombre, para nombrar resolutivos.
     *
     * Toma las 2 primeras letras de la primera palabra significativa y la inicial
     * de la siguiente (omitiendo conectores como "de", "y", "del").
     * Ej: "Tesorería Municipal" -> "TeM".
     */
    public function siglas(): string
    {
        $conectores = ['de', 'del', 'y', 'e', 'la', 'las', 'los', 'el', 'al', 'a', 'en', 'para', 'por'];

        $palabras = preg_split('/\s+/', trim((string) $this->nombre_dependencia)) ?: [];
        $palabras = array_values(array_filter(
            $palabras,
            fn (string $palabra): bool => $palabra !== '' && ! in_array(mb_strtolower($palabra, 'UTF-8'), $conectores, true)
        ));

        $primera = $palabras[0] ?? null;
        $segunda = $palabras[1] ?? null;

        if (! $primera) {
            return 'GEN';
        }

        if (! $segunda) {
            // Una sola palabra significativa: primeras 3 letras.
            return mb_strtoupper(mb_substr($primera, 0, 1, 'UTF-8'), 'UTF-8')
                .mb_strtolower(mb_substr($primera, 1, 2, 'UTF-8'), 'UTF-8');
        }

        // Dos primeras letras de la primera palabra + inicial de la segunda.
        return mb_strtoupper(mb_substr($primera, 0, 1, 'UTF-8'), 'UTF-8')
            .mb_strtolower(mb_substr($primera, 1, 1, 'UTF-8'), 'UTF-8')
            .mb_strtoupper(mb_substr($segunda, 0, 1, 'UTF-8'), 'UTF-8');
    }

    public function tramites(): HasMany
    {
        return $this->hasMany(Tramite::class, 'fk_dependencia', 'id_dependencia');
    }
}
