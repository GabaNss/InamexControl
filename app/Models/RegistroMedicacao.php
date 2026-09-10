<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro de que uma dose prescrita foi (ou nao) administrada em uma data/turno.
 *
 * REGRA CRITICA DE IMUTABILIDADE: depois que o DiarioStatus da 'data' deste
 * registro estiver com encerrado = true, este registro NAO PODE mais ser
 * editado ou excluido. Essa regra deve ser garantida em pelo menos duas
 * camadas: App\Policies\RegistroMedicacaoPolicy (update/delete) e
 * App\Services\RegistroMedicacaoService (regra de negocio central), nunca
 * apenas desabilitando botoes no frontend.
 *
 * Sem 'updated_at' de proposito: o registro nasce e nao deveria ser tocado
 * apos o fechamento do dia.
 *
 * @property int $prescricao_id
 * @property \Illuminate\Support\Carbon $data
 * @property string $turno
 * @property bool $administrado
 * @property string|null $observacao
 * @property int $usuario_id
 */
class RegistroMedicacao extends Model
{
    use HasFactory;

    // Pluralizacao padrao do Eloquent resultaria em "registro_medicacaos".
    protected $table = 'registros_medicacao';

    public const UPDATED_AT = null;

    protected $fillable = [
        'prescricao_id',
        'data',
        'turno',
        'administrado',
        'observacao',
        'usuario_id',
    ];

    protected $casts = [
        'data' => 'date',
        'administrado' => 'boolean',
    ];

    /**
     * Prescricao de origem deste registro.
     * Relacionamento: RegistroMedicacao belongsTo Prescricao.
     */
    public function prescricao(): BelongsTo
    {
        return $this->belongsTo(Prescricao::class);
    }

    /**
     * Usuario (enfermeiro/tecnico) que preencheu este registro.
     * Relacionamento: RegistroMedicacao belongsTo User (chave 'usuario_id').
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Status do diario (encerrado ou nao) correspondente a data deste registro.
     * Nao e uma foreign key direta: a busca e feita por data (ver DiarioService).
     * Mantido aqui apenas como referencia de onde a regra de imutabilidade mora.
     */
    public function diarioStatus(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DiarioStatus::class, 'data', 'data');
    }
}
