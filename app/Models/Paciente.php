<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Representa um paciente internado na instituicao.
 *
 * @property string $nome
 * @property string $prontuario
 * @property string|null $quarto
 * @property string|null $foto
 * @property bool $ativa
 */
class Paciente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'prontuario',
        'quarto',
        'foto',
        'ativa',
    ];

    protected $casts = [
        'ativa' => 'boolean',
    ];

    /**
     * Todas as prescricoes (ativas e historicas) deste paciente.
     * Relacionamento: Paciente hasMany Prescricao.
     */
    public function prescricoes(): HasMany
    {
        return $this->hasMany(Prescricao::class);
    }

    /**
     * Apenas as prescricoes atualmente ativas do paciente.
     * Usado para gerar os RegistroMedicacao do dia (ver DiarioService).
     */
    public function prescricoesAtivas(): HasMany
    {
        return $this->prescricoes()->where('ativa', true);
    }

    public function fichaMedica(): HasOne
    {
        return $this->hasOne(FichaMedica::class);
    }

    public function prontuariosHistorico(): HasMany
    {
        return $this->hasMany(ProntuarioDiario::class)->orderByDesc('data');
    }

    public function prontuarioHistorico(): HasOne
    {
        return $this->hasOne(ProntuarioHistorico::class);
    }
}
