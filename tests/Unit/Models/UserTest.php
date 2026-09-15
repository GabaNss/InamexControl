<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_tem_cargo_confere_o_cargo_atual(): void
    {
        $medico = User::factory()->create(['cargo' => 'medico']);

        $this->assertTrue($medico->temCargo('medico'));
        $this->assertTrue($medico->temCargo('admin', 'medico', 'diretor'));
        $this->assertFalse($medico->temCargo('admin', 'diretor'));
        $this->assertFalse($medico->temCargo('tecnico'));
    }

    public function test_tem_cargo_atribuido_e_falso_para_pendente(): void
    {
        $pendente = User::factory()->create(['cargo' => 'pendente']);

        $this->assertFalse($pendente->temCargoAtribuido());
    }

    /**
     * @dataProvider cargosComAcesso
     */
    public function test_tem_cargo_atribuido_e_verdadeiro_para_qualquer_cargo_real(string $cargo): void
    {
        $usuario = User::factory()->create(['cargo' => $cargo]);

        $this->assertTrue($usuario->temCargoAtribuido());
    }

    public static function cargosComAcesso(): array
    {
        return [
            ['admin'],
            ['diretor'],
            ['medico'],
            ['enfermeiro'],
            ['tecnico'],
        ];
    }

    public function test_esta_no_turno_retorna_true_para_admin_independente_do_horario(): void
    {
        $admin = User::factory()->create([
            'cargo' => 'admin',
            'turno_inicio' => '00:00:00',
            'turno_fim' => '00:01:00',
        ]);

        $this->assertTrue($admin->estaNoTurno());
    }

    public function test_esta_no_turno_retorna_true_quando_turno_nao_definido(): void
    {
        $usuario = User::factory()->create([
            'cargo' => 'enfermeiro',
            'turno_inicio' => null,
            'turno_fim' => null,
        ]);

        $this->assertTrue($usuario->estaNoTurno());
    }

    public function test_esta_no_turno_retorna_true_dentro_do_turno(): void
    {
        $agora = now();

        $usuario = User::factory()->create([
            'cargo' => 'enfermeiro',
            'turno_inicio' => $agora->copy()->subHour()->format('H:i:s'),
            'turno_fim' => $agora->copy()->addHour()->format('H:i:s'),
        ]);

        $this->assertTrue($usuario->estaNoTurno());
    }

    public function test_esta_no_turno_retorna_false_fora_do_turno(): void
    {
        $agora = now();

        $usuario = User::factory()->create([
            'cargo' => 'enfermeiro',
            'turno_inicio' => $agora->copy()->addHour()->format('H:i:s'),
            'turno_fim' => $agora->copy()->addHours(2)->format('H:i:s'),
        ]);

        $this->assertFalse($usuario->estaNoTurno());
    }

    public function test_esta_no_turno_suporta_turno_noturno_que_passa_da_meia_noite(): void
    {
        $usuario = User::factory()->create([
            'cargo' => 'tecnico',
            'turno_inicio' => '22:00:00',
            'turno_fim' => '06:00:00',
        ]);

        // Simula horarios dentro e fora do turno noturno
        \Illuminate\Support\Facades\Date::setTestNow(now()->setTime(23, 0));
        $this->assertTrue($usuario->estaNoTurno());

        \Illuminate\Support\Facades\Date::setTestNow(now()->setTime(3, 0));
        $this->assertTrue($usuario->estaNoTurno());

        \Illuminate\Support\Facades\Date::setTestNow(now()->setTime(12, 0));
        $this->assertFalse($usuario->estaNoTurno());

        \Illuminate\Support\Facades\Date::setTestNow(null);
    }
}
