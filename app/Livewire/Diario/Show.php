<?php

namespace App\Livewire\Diario;

use App\Models\DiarioStatus;
use App\Services\DiarioService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public function mount(): void
    {
        $this->authorize('viewAny', DiarioStatus::class);
    }

    /**
     * Encerramento manual, excepcional — o fluxo normal e automatico via
     * App\Console\Commands\EncerrarDiarioCommand a meia-noite.
     */
    public function encerrarHoje(): void
    {
        $this->authorize('encerrar-diario-manual');

        app(DiarioService::class)->encerrarDia(Carbon::today(), auth()->user());

        session()->flash('status', 'Diario de hoje encerrado manualmente.');
    }

    public function render(): View
    {
        $historico = DiarioStatus::orderByDesc('data')->limit(14)->get();

        return view('livewire.diario.show', ['historico' => $historico]);
    }
}
