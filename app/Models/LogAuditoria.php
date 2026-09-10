<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Linha de auditoria gravada pelo App\Services\AuditoriaService para toda
 * acao sensivel do sistema (quem fez, quando, de onde, e o que mudou).
 *
 * @property int|null $usuario_id
 * @property string $acao
 * @property array|null $dados_antes
 * @property array|null $dados_depois
 * @property string|null $ip_address
 * @property string|null $user_agent
 */
class LogAuditoria extends Model
{
    use HasFactory;

    // Pluralizacao padrao do Eloquent resultaria em "log_auditorias".
    protected $table = 'logs_auditoria';

    public const UPDATED_AT = null;

    protected $fillable = [
        'usuario_id',
        'acao',
        'alvo_type',
        'alvo_id',
        'dados_antes',
        'dados_depois',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'dados_antes' => 'array',
        'dados_depois' => 'array',
    ];

    /**
     * Usuario que executou a acao registrada.
     * Relacionamento: LogAuditoria belongsTo User (chave 'usuario_id').
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Entidade afetada pela acao (Paciente, Prescricao, RegistroMedicacao, etc).
     * Relacionamento polimorfico: LogAuditoria morphTo (coluna 'alvo_type'/'alvo_id').
     */
    public function alvo(): MorphTo
    {
        return $this->morphTo();
    }
}
