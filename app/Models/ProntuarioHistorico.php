<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProntuarioHistorico extends Model
{
    use HasFactory;

    protected $table = 'prontuarios_historicos';

    protected $fillable = [
        'paciente_id',
        'conteudo',
        'atualizado_por',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function atualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atualizado_por');
    }
}
