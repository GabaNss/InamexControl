<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FichaMedica extends Model
{
    use HasFactory;

    protected $table = 'fichas_medicas';

    protected $fillable = [
        'paciente_id',
        'laudos',
        'diagnosticos',
        'medicamentos_cronicos',
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
