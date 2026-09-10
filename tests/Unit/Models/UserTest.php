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
}
