<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Catalogo de medicamentos que podem ser prescritos.
 *
 * @property string $nome
 * @property string $concentracao
 * @property string $via_administracao
 * @property bool $ativo
 */
class Medicamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'concentracao',
        'via_administracao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Prescricoes que usam este medicamento.
     * Relacionamento: Medicamento hasMany Prescricao.
     */
    public function prescricoes(): HasMany
    {
        return $this->hasMany(Prescricao::class);
    }
}
