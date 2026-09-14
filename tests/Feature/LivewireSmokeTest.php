<?php

namespace Tests\Feature;

use App\Livewire\Diario;
use App\Livewire\Medicamentos;
use App\Livewire\Pacientes;
use App\Livewire\Prescricoes;
use App\Livewire\Registros;
use App\Models\DiarioStatus;
use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\Prescricao;
use App\Models\RegistroMedicacao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_fluxo_completo_via_componentes_livewire(): void
    {
        $admin = User::factory()->create(['cargo' => 'admin', 'ativo' => true]);
        $enfermeira = User::factory()->create(['cargo' => 'enfermeiro', 'ativo' => true]);

        $this->actingAs($admin);

        Livewire::test(Pacientes\Form::class)
            ->set('nome', 'Maria Teste')
            ->set('prontuario', 'P-999')
            ->set('quarto', '202')
            ->call('salvar')
            ->assertRedirect(route('pacientes.index'));

        $paciente = Paciente::where('prontuario', 'P-999')->first();
        $this->assertNotNull($paciente);

        Livewire::test(Pacientes\Index::class)->assertSee('Maria Teste');

        Livewire::test(Medicamentos\Form::class)
            ->set('nome', 'Haloperidol')
            ->set('concentracao', '5mg')
            ->set('via_administracao', 'oral')
            ->call('salvar')
            ->assertRedirect(route('medicamentos.index'));

        $medicamento = Medicamento::where('nome', 'Haloperidol')->first();
        $this->assertNotNull($medicamento);

        Livewire::test(Prescricoes\Form::class)
            ->set('paciente_id', $paciente->id)
            ->set('medicamento_id', $medicamento->id)
            ->set('dose', '1 ampola')
            ->set('horario', 'tarde')
            ->call('salvar')
            ->assertRedirect(route('prescricoes.index'));

        $this->assertTrue(Prescricao::where('paciente_id', $paciente->id)->exists());

        Livewire::test(Registros\Index::class);
        $registro = RegistroMedicacao::where('prescricao_id', Prescricao::first()->id)->first();
        $this->assertNotNull($registro);
        $this->assertFalse($registro->administrado);

        $this->actingAs($enfermeira);
        Livewire::test(Registros\Index::class)
            ->call('marcar', $registro->id, true)
            ->assertHasNoErrors();

        $registro->refresh();
        $this->assertTrue($registro->administrado);
        $this->assertEquals($enfermeira->id, $registro->usuario_id);

        $this->actingAs($admin);
        Livewire::test(Diario\Show::class)
            ->call('encerrarHoje')
            ->assertHasNoErrors();

        $this->assertTrue(DiarioStatus::where('data', today()->toDateString())->first()->encerrado);

        $this->actingAs($enfermeira);
        Livewire::test(Registros\Index::class)
            ->call('marcar', $registro->id, false);

        $registro->refresh();
        $this->assertTrue($registro->administrado);

        $tecnico = User::factory()->create(['cargo' => 'tecnico', 'ativo' => true]);
        $this->assertFalse($tecnico->can('create', Prescricao::class));
    }

    public function test_admin_pode_reabrir_diario_encerrado(): void
    {
        $admin = User::factory()->create(['cargo' => 'admin', 'ativo' => true]);

        $this->actingAs($admin);

        Livewire::test(Diario\Show::class)
            ->call('encerrarHoje')
            ->assertHasNoErrors();

        $this->assertTrue(DiarioStatus::where('data', today()->toDateString())->first()->encerrado);

        Livewire::test(Diario\Show::class)
            ->call('reabrirHoje')
            ->assertHasNoErrors();

        $this->assertFalse(DiarioStatus::where('data', today()->toDateString())->first()->encerrado);
    }

    public function test_nao_admin_nao_pode_reabrir_diario(): void
    {
        $admin = User::factory()->create(['cargo' => 'admin', 'ativo' => true]);
        $medico = User::factory()->create(['cargo' => 'medico', 'ativo' => true]);

        $this->actingAs($admin);
        Livewire::test(Diario\Show::class)->call('encerrarHoje');

        $this->actingAs($medico);
        Livewire::test(Diario\Show::class)
            ->call('reabrirHoje')
            ->assertForbidden();

        $this->assertTrue(DiarioStatus::where('data', today()->toDateString())->first()->encerrado);
    }
}
