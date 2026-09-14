<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Auditoria;
use App\Livewire\Backup;
use App\Livewire\Diario;
use App\Livewire\Medicamentos;
use App\Livewire\Pacientes;
use App\Livewire\Prescricoes;
use App\Livewire\Registros;
use App\Livewire\Relatorios;
use App\Livewire\Usuarios;
use App\Models\FichaMedica;
use App\Models\Paciente;
use App\Models\ProntuarioDiario;
use App\Models\ProntuarioHistorico;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

// Sistema fechado: sem landing page publica. Visitante vai para o login;
// usuario autenticado vai direto para o dashboard.
Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil do proprio usuario autenticado (Breeze) — qualquer cargo logado.
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Rotas autenticadas do InamexControl, agrupadas por dominio.
| Cada tela e um componente Livewire full-page; a autorizacao fina (quem
| pode o que) e feita dentro de cada componente via $this->authorize(),
| delegando para as Policies em app/Policies. Os comentarios abaixo indicam
| a intencao de cada grupo de cargo.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Pacientes: leitura para toda a equipe clinica; escrita restrita a
    // admin/diretor (ver PacientePolicy).
    Route::get('/pacientes', Pacientes\Index::class)->name('pacientes.index');
    Route::get('/pacientes/create', Pacientes\Form::class)->name('pacientes.create');
    Route::get('/pacientes/{paciente}/edit', Pacientes\Form::class)->name('pacientes.edit');
    Route::get('/pacientes/{paciente}', Pacientes\Show::class)->name('pacientes.show');

    // Medicamentos: leitura para toda a equipe clinica; escrita restrita a
    // admin/diretor/medico (ver MedicamentoPolicy).
    Route::get('/medicamentos', Medicamentos\Index::class)->name('medicamentos.index');
    Route::get('/medicamentos/create', Medicamentos\Form::class)->name('medicamentos.create');
    Route::get('/medicamentos/{medicamento}/edit', Medicamentos\Form::class)->name('medicamentos.edit');

    // Prescricoes: leitura para toda a equipe clinica; criacao/edicao restrita
    // a medico/diretor/admin, pois e um ato medico (ver PrescricaoPolicy).
    Route::get('/prescricoes', Prescricoes\Index::class)->name('prescricoes.index');
    Route::get('/prescricoes/create', Prescricoes\Form::class)->name('prescricoes.create');
    Route::get('/prescricoes/{prescricao}/edit', Prescricoes\Form::class)->name('prescricoes.edit');

    // Registros de medicacao: tela operacional diaria preenchida por
    // enfermeiro/tecnico (e visivel a medico/diretor/admin). Edicao bloqueada
    // quando o dia ja estiver encerrado (ver RegistroMedicacaoPolicy).
    Route::get('/registros', Registros\Index::class)->name('registros.index');

    // Diario de operacoes: encerramento e reabertura do dia (admin).
    Route::get('/diario', Diario\Show::class)->name('diario.show');

    // Relatorios/exportacoes: acesso restrito via Gate 'gerar-relatorios'.
    Route::get('/relatorios', Relatorios\Index::class)->name('relatorios.index');

    // Exportações PDF por interna — rotas diretas (fora do Livewire) porque
    // o DomPDF retorna uma Response comum que o Livewire não intercepta.

    Route::get('/exportacao/{paciente}/ficha-medica', function (Paciente $paciente) {
        Gate::authorize('gerar-relatorios');

        $ficha = FichaMedica::where('paciente_id', $paciente->id)->with('atualizadoPor')->first();

        $pdf = Pdf::loadView('pdf.ficha-medica', compact('paciente', 'ficha'))
            ->setPaper('a4');

        return $pdf->download("ficha-medica-{$paciente->prontuario}.pdf");
    })->name('exportacao.ficha-medica');

    Route::get('/exportacao/{paciente}/prontuario-diario', function (Request $request, Paciente $paciente) {
        Gate::authorize('gerar-relatorios');

        $dados = $request->validate([
            'inicio' => ['required', 'date'],
            'fim' => ['required', 'date', 'after_or_equal:inicio'],
        ]);

        $inicio = Carbon::parse($dados['inicio']);
        $fim = Carbon::parse($dados['fim']);

        $prontuarios = ProntuarioDiario::where('paciente_id', $paciente->id)
            ->whereBetween('data', [$inicio, $fim])
            ->orderBy('data')
            ->get();

        $pdf = Pdf::loadView('pdf.prontuario-diario', compact('paciente', 'prontuarios', 'inicio', 'fim'))
            ->setPaper('a4');

        return $pdf->download("prontuario-diario-{$paciente->prontuario}.pdf");
    })->name('exportacao.prontuario-diario');

    Route::get('/exportacao/{paciente}/completo', function (Paciente $paciente) {
        Gate::authorize('gerar-relatorios');

        $ficha = FichaMedica::where('paciente_id', $paciente->id)->first();
        $historico = ProntuarioHistorico::where('paciente_id', $paciente->id)->first();
        $prontuarios = ProntuarioDiario::where('paciente_id', $paciente->id)
            ->orderByDesc('data')
            ->get();

        $pdf = Pdf::loadView('pdf.completo', compact('paciente', 'ficha', 'historico', 'prontuarios'))
            ->setPaper('a4');

        return $pdf->download("relatorio-completo-{$paciente->prontuario}.pdf");
    })->name('exportacao.completo');

    // Usuarios do sistema: restrito a admin (ver UserPolicy). Nao ha
    // auto-registro publico — contas sao criadas apenas por aqui.
    Route::get('/usuarios', Usuarios\Index::class)->name('usuarios.index');
    Route::get('/usuarios/create', Usuarios\Form::class)->name('usuarios.create');
    Route::get('/usuarios/{user}/edit', Usuarios\Form::class)->name('usuarios.edit');

    // Trilha de auditoria: restrito a admin e diretor (ver Gate 'ver-auditoria').
    Route::get('/auditoria', Auditoria\Index::class)->name('auditoria.index');

    // Backup manual do banco inteiro: restrito a admin (ver Gate 'gerenciar-backup').
    Route::get('/backup', Backup\Index::class)->name('backup.index');
});

require __DIR__.'/auth.php';
