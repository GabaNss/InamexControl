<?php

namespace Tests\Unit\Policies;

use App\Models\Medicamento;
use App\Models\User;
use App\Policies\MedicamentoPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicamentoPolicyTest extends TestCase
{
    use RefreshDatabase;

    private MedicamentoPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new MedicamentoPolicy();
    }

    public function test_pendente_nao_pode_ver_medicamentos(): void
    {
        $pendente = User::factory()->create(['cargo' => 'pendente']);

        $this->assertFalse($this->policy->viewAny($pendente));
    }

    /**
     * @dataProvider cargosComAcesso
     */
    public function test_qualquer_cargo_real_pode_ver_medicamentos(string $cargo): void
    {
        $usuario = User::factory()->create(['cargo' => $cargo]);

        $this->assertTrue($this->policy->viewAny($usuario));
    }

    public function test_enfermeiro_e_tecnico_nao_podem_gerenciar_o_catalogo(): void
    {
        $medicamento = Medicamento::factory()->create();

        foreach (['enfermeiro', 'tecnico'] as $cargo) {
            $usuario = User::factory()->create(['cargo' => $cargo]);

            $this->assertFalse($this->policy->create($usuario));
            $this->assertFalse($this->policy->update($usuario, $medicamento));
            $this->assertFalse($this->policy->delete($usuario, $medicamento));
        }
    }

    public function test_admin_diretor_e_medico_podem_gerenciar_o_catalogo(): void
    {
        $medicamento = Medicamento::factory()->create();

        foreach (['admin', 'diretor', 'medico'] as $cargo) {
            $usuario = User::factory()->create(['cargo' => $cargo]);

            $this->assertTrue($this->policy->create($usuario));
            $this->assertTrue($this->policy->update($usuario, $medicamento));
            $this->assertTrue($this->policy->delete($usuario, $medicamento));
        }
    }

    public static function cargosComAcesso(): array
    {
        return [['admin'], ['diretor'], ['medico'], ['enfermeiro'], ['tecnico']];
    }
}
