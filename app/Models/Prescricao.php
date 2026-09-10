<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Prescricao medica de um medicamento para um paciente, em um horario fixo
 * (manha/tarde). Enquanto 'ativa' = true, o DiarioService deve gerar um
 * RegistroMedicacao por dia/turno para esta prescricao.
 *
 * @property int $paciente_id
 * @property int $medicamento_id
 * @property string $dose
 * @property string $horario
 * @property bool $ativa
 * @property int|null $prescrito_por
 */
class Prescricao extends Model
{
    use HasFactory;

    // Pluralizacao padrao do Eloquent resultaria em "prescricaos".
    protected $table = 'prescricoes';

    protected $fillable = [
        'paciente_id',
        'medicamento_id',
        'dose',
        'horario',
        'ativa',
        'prescrito_por',
    ];

    protected $casts = [
        'ativa' => 'boolean',
    ];

    /**
     * Paciente ao qual esta prescricao pertence.
     * Relacionamento: Prescricao belongsTo Paciente.
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * Medicamento prescrito.
     * Relacionamento: Prescricao belongsTo Medicamento.
     */
    public function medicamento(): BelongsTo
    {
        return $this->belongsTo(Medicamento::class);
    }

    /**
     * Medico (User) que fez a prescricao.
     * Relacionamento: Prescricao belongsTo User (chave 'prescrito_por').
     */
    public function prescritor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prescrito_por');
    }

    /**
     * Historico de todos os registros de administracao gerados a partir desta prescricao.
     * Relacionamento: Prescricao hasMany RegistroMedicacao.
     */
    public function registrosMedicacao(): HasMany
    {
        return $this->hasMany(RegistroMedicacao::class);
    }
}
