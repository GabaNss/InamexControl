<?php

namespace Database\Seeders;

use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\Prescricao;
use App\Models\RegistroMedicacao;
use App\Models\User;
use App\Services\DiarioService;
use App\Services\RegistroMedicacaoService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Popula o banco com 10 linhas em cada tabela de dominio, usando os mesmos
 * Services de producao (nao inserts crus) para que os dados gerados respeitem
 * as regras de negocio reais: registros nascem via RegistroMedicacaoService,
 * dias fecham via DiarioService (que tambem grava a auditoria).
 *
 * Uso: php artisan db:seed --class=DemoDataSeeder
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $cargos = ['diretor', 'medico', 'medico', 'enfermeiro', 'enfermeiro', 'tecnico', 'tecnico', 'tecnico', 'pendente', 'admin'];

        $usuarios = collect($cargos)->map(fn (string $cargo, int $i) => User::factory()->create([
            'name' => 'Demo '.ucfirst($cargo).' '.($i + 1),
            'cargo' => $cargo,
            'ativo' => true,
        ]));

        $medico = $usuarios->firstWhere('cargo', 'medico');
        $enfermeira = $usuarios->firstWhere('cargo', 'enfermeiro');

        $pacientes = Paciente::factory()->count(10)->create();
        $medicamentos = Medicamento::factory()->count(10)->create();

        $doses = ['1 comprimido', '5ml', '1 ampola', '2 comprimidos', '10ml'];

        collect(range(0, 9))->each(fn (int $i) => Prescricao::create([
            'paciente_id' => $pacientes[$i]->id,
            'medicamento_id' => $medicamentos[$i]->id,
            'dose' => $doses[$i % count($doses)],
            'horario' => $i % 2 === 0 ? 'manha' : 'tarde',
            'ativa' => true,
            'prescrito_por' => $medico->id,
        ]));

        // 9 dias passados encerrados (automatico, sem usuario) + hoje aberto = 10 linhas.
        $diarioService = app(DiarioService::class);
        for ($i = 9; $i >= 1; $i--) {
            $diarioService->encerrarDia(Carbon::today()->subDays($i));
        }
        $diarioService->abrirDia(Carbon::today());

        // Gera os 10 registros de hoje pelo Service real (1 por prescricao ativa).
        $registroService = app(RegistroMedicacaoService::class);
        $registroService->gerarRegistrosDoDia(Carbon::today());

        // Marca um registro como administrado para ter um exemplo preenchido
        // e completar a 10a linha de auditoria.
        $primeiroRegistro = RegistroMedicacao::where('data', Carbon::today())->first();
        $registroService->registrarAdministracao(
            $primeiroRegistro,
            true,
            'Administrado sem intercorrencias.',
            $enfermeira,
        );
    }
}
