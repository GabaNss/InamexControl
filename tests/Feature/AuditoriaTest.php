<?php

namespace Tests\Feature;

use App\Livewire\Internas\FichaMedica;
use App\Models\FichaMedica as ModelFichaMedica;
use App\Models\LogAuditoria;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Cobre a trilha de auditoria (LGPD) para uma mutacao de dado clinico
 * (ficha medica) e para uma exportacao de PDF.
 */
class AuditoriaTest extends TestCase
{
    use RefreshDatabase;

    public function test_salvar_ficha_medica_gera_log_de_auditoria(): void
    {
        $admin = User::factory()->create(['cargo' => 'admin', 'ativo' => true]);
        $paciente = Paciente::factory()->create();

        $this->actingAs($admin);

        Livewire::test(FichaMedica::class, ['pacienteId' => $paciente->id])
            ->set('laudos', 'Laudo inicial')
            ->set('diagnosticos', 'Diagnostico X')
            ->call('salvar');

        $ficha = ModelFichaMedica::where('paciente_id', $paciente->id)->firstOrFail();

        $log = LogAuditoria::where('acao', 'ficha_medica.criada')->first();

        $this->assertNotNull($log);
        $this->assertSame($admin->id, $log->usuario_id);
        $this->assertSame($ficha->getMorphClass(), $log->alvo_type);
        $this->assertSame($ficha->id, $log->alvo_id);
        $this->assertSame('Laudo inicial', $log->dados_depois['laudos']);
    }

    public function test_editar_ficha_medica_registra_dados_antes_e_depois(): void
    {
        $admin = User::factory()->create(['cargo' => 'admin', 'ativo' => true]);
        $paciente = Paciente::factory()->create();
        ModelFichaMedica::create([
            'paciente_id' => $paciente->id,
            'laudos' => 'Antes',
            'atualizado_por' => $admin->id,
        ]);

        $this->actingAs($admin);

        Livewire::test(FichaMedica::class, ['pacienteId' => $paciente->id])
            ->set('laudos', 'Depois')
            ->call('salvar');

        $log = LogAuditoria::where('acao', 'ficha_medica.atualizada')->firstOrFail();

        $this->assertSame('Antes', $log->dados_antes['laudos']);
        $this->assertSame('Depois', $log->dados_depois['laudos']);
    }

    public function test_exportacao_de_pdf_gera_log_de_auditoria(): void
    {
        $admin = User::factory()->create(['cargo' => 'admin', 'ativo' => true]);
        $paciente = Paciente::factory()->create();

        $this->actingAs($admin)
            ->get(route('exportacao.ficha-medica', $paciente))
            ->assertOk();

        $log = LogAuditoria::where('acao', 'exportacao.ficha_medica')->firstOrFail();

        $this->assertSame($admin->id, $log->usuario_id);
        $this->assertSame($paciente->getMorphClass(), $log->alvo_type);
        $this->assertSame($paciente->id, $log->alvo_id);
        $this->assertSame('pdf', $log->dados_depois['formato']);
    }
}
