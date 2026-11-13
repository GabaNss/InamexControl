<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Garante que a foto da interna (dado pessoal sensivel) so e servida atras de
 * autenticacao + PacientePolicy@view, a partir do disco privado.
 */
class PacienteFotoTest extends TestCase
{
    use RefreshDatabase;

    private function pacienteComFoto(): Paciente
    {
        Storage::fake('local');
        $caminho = UploadedFile::fake()->image('foto.jpg')->store('pacientes', 'local');

        return Paciente::factory()->create(['foto' => $caminho]);
    }

    public function test_usuario_nao_autenticado_e_redirecionado_para_o_login(): void
    {
        $paciente = $this->pacienteComFoto();

        $this->get(route('pacientes.foto', $paciente))
            ->assertRedirect(route('login'));
    }

    public function test_usuario_sem_cargo_recebe_403(): void
    {
        $paciente = $this->pacienteComFoto();
        $pendente = User::factory()->create(['cargo' => 'pendente', 'ativo' => true]);

        $this->actingAs($pendente)
            ->get(route('pacientes.foto', $paciente))
            ->assertForbidden();
    }

    public function test_usuario_com_cargo_recebe_a_foto_com_cache_control_privado(): void
    {
        $paciente = $this->pacienteComFoto();
        $enfermeiro = User::factory()->create(['cargo' => 'enfermeiro', 'ativo' => true]);

        $resposta = $this->actingAs($enfermeiro)
            ->get(route('pacientes.foto', $paciente));

        $resposta->assertOk();
        $this->assertStringContainsString('private', $resposta->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $resposta->headers->get('Cache-Control'));
    }

    public function test_paciente_sem_foto_retorna_404(): void
    {
        Storage::fake('local');
        $paciente = Paciente::factory()->create(['foto' => null]);
        $enfermeiro = User::factory()->create(['cargo' => 'enfermeiro', 'ativo' => true]);

        $this->actingAs($enfermeiro)
            ->get(route('pacientes.foto', $paciente))
            ->assertNotFound();
    }
}
