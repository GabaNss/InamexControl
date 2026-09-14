<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Controla se um dia esta "aberto" (equipe pode preencher/editar registros) ou
 * "encerrado" (registros daquela data tornam-se imutaveis). O encerramento
 * automatico ocorre via App\Console\Commands\EncerrarDiarioCommand, agendado
 * para meia-noite em routes/console.php — ver App\Services\DiarioService para
 * a regra central de quando/como encerrar.
 *
 * @property \Illuminate\Support\Carbon $data
 * @property bool $encerrado
 * @property \Illuminate\Support\Carbon|null $encerrado_em
 */
class DiarioStatus extends Model
{
    use HasFactory;

    // Pluralizacao padrao do Eloquent resultaria em "diario_statuses".
    protected $table = 'diario_status';

    protected $fillable = [
        'data',
        'encerrado',
        'encerrado_em',
    ];

    protected $casts = [
        'encerrado' => 'boolean',
        'encerrado_em' => 'datetime',
    ];

    // Garante que 'data' é sempre escrito como 'Y-m-d' no banco, independente
    // do driver (SQLite armazena como TEXT sem truncar o componente de hora).
    protected function data(): Attribute
    {
        return Attribute::make(
            get: fn ($v) => $v ? Carbon::parse($v)->startOfDay() : null,
            set: fn ($v) => Carbon::parse($v)->toDateString(),
        );
    }

    /**
     * Registros de medicacao referentes a esta mesma data (busca por data, nao FK).
     * Relacionamento: DiarioStatus hasMany RegistroMedicacao (chave 'data').
     */
    public function registrosMedicacao(): HasMany
    {
        return $this->hasMany(RegistroMedicacao::class, 'data', 'data');
    }
}
