<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProntuarioDiario extends Model
{
    use HasFactory;

    protected $table = 'prontuarios_diarios';

    protected $fillable = [
        'paciente_id',
        'data',
        'conteudo',
        'atualizado_por',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function atualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atualizado_por');
    }

    public function estaEncerrado(): bool
    {
        return DiarioStatus::where('data', $this->data)->value('encerrado') ?? false;
    }
}
