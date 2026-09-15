<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cargo',
        'ativo',
        'turno_inicio',
        'turno_fim',
    ];

    /**
     * Cargos validos para o campo 'cargo' (usado pelas Gates/Policies para
     * decidir o que cada usuario pode ver/fazer no sistema). 'pendente' e o
     * estado de quem se auto-cadastrou e ainda nao tem nenhuma permissao.
     */
    public const CARGOS = ['pendente', 'admin', 'medico', 'enfermeiro', 'chefe_enfermagem', 'tecnico', 'diretor'];

    /**
     * Cargos que efetivamente dao algum acesso ao sistema (todos exceto 'pendente').
     */
    public const CARGOS_COM_ACESSO = ['admin', 'medico', 'enfermeiro', 'chefe_enfermagem', 'tecnico', 'diretor'];

    public static function labelCargo(string $cargo): string
    {
        return match ($cargo) {
            'chefe_enfermagem' => 'Chefe de Enfermagem',
            default => ucfirst($cargo),
        };
    }

    /**
     * Prescricoes feitas por este usuario (quando ele e medico).
     * Relacionamento: User hasMany Prescricao (chave 'prescrito_por').
     */
    public function prescricoes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Prescricao::class, 'prescrito_por');
    }

    /**
     * Registros de medicacao preenchidos por este usuario (enfermeiro/tecnico).
     * Relacionamento: User hasMany RegistroMedicacao (chave 'usuario_id').
     */
    public function registrosMedicacao(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RegistroMedicacao::class, 'usuario_id');
    }

    /**
     * Logs de auditoria gerados pelas acoes deste usuario.
     * Relacionamento: User hasMany LogAuditoria (chave 'usuario_id').
     */
    public function logsAuditoria(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LogAuditoria::class, 'usuario_id');
    }

    /**
     * Helper usado pelas Gates (ver App\Providers\AppServiceProvider) para checar
     * o cargo do usuario autenticado, ex: $user->temCargo('medico', 'diretor').
     */
    public function temCargo(string ...$cargos): bool
    {
        return in_array($this->cargo, $cargos, true);
    }

    /**
     * Usado pelas Policies para liberar telas a "qualquer cargo clinico" —
     * ou seja, qualquer cargo real, exceto 'pendente' (quem se auto-cadastrou
     * e o admin ainda nao atribuiu nenhum cargo).
     */
    public function temCargoAtribuido(): bool
    {
        return $this->temCargo(...self::CARGOS_COM_ACESSO);
    }

    /**
     * Verifica se o horario atual esta dentro do turno do usuario.
     * Admin sempre retorna true (sem restricao de turno).
     * Se turno_inicio ou turno_fim for null, sem restricao (turno nao definido).
     * Suporta turnos noturnos que passam da meia-noite (ex: 22:00-06:00).
     */
    public function estaNoTurno(): bool
    {
        if ($this->temCargo('admin')) {
            return true;
        }

        if ($this->turno_inicio === null || $this->turno_fim === null) {
            return true;
        }

        $agora = now()->format('H:i:s');

        if ($this->turno_inicio <= $this->turno_fim) {
            return $agora >= $this->turno_inicio && $agora <= $this->turno_fim;
        }

        // Turno noturno: passa da meia-noite
        return $agora >= $this->turno_inicio || $agora <= $this->turno_fim;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
