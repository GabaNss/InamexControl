<div class="space-y-3">

    {{-- ── Cabeçalho ── --}}
    <div class="bg-white rounded border border-gray-200">
        <div class="px-6 py-5 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">
                    {{ \Carbon\Carbon::today()->locale('pt_BR')->isoFormat('dddd[,] D [de] MMMM [de] YYYY') }}
                </p>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight leading-none mt-1">
                    Olá, {{ explode(' ', $usuario->name)[0] }}
                </h1>
            </div>
            <p class="shrink-0 text-sm font-bold uppercase tracking-wide pb-0.5
                {{ $diaAberto
                    ? 'text-gray-900 border-b-2 border-green-500'
                    : 'text-gray-400 border-b-2 border-gray-300' }}">
                Diário {{ $diaAberto ? 'aberto' : 'encerrado' }}
            </p>
        </div>
    </div>

    {{-- ── Métricas em faixa ── --}}
    <div class="bg-white rounded border border-gray-200 overflow-hidden">
        <div class="flex flex-col sm:flex-row">

            {{-- Internas --}}
            <div class="flex-1 p-5 border-b sm:border-b-0 sm:border-r border-gray-200">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Internas ativas</p>
                <p class="text-5xl font-black text-gray-900 tracking-tighter leading-none tabular-nums">
                    {{ $totalInternas }}
                </p>
                <div class="mt-3 h-px bg-gray-200">
                    <div class="h-full bg-gray-900" style="width: 100%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-2">{{ $prescricoesAtivas }} prescrições ativas</p>
            </div>

            {{-- Doses hoje --}}
            <div class="flex-1 p-5 border-b sm:border-b-0 sm:border-r border-gray-200">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Doses hoje</p>
                <p class="text-5xl font-black text-gray-900 tracking-tighter leading-none tabular-nums">
                    {{ $administradoHoje }}<span class="text-2xl font-normal text-gray-300">/{{ $totalHoje }}</span>
                </p>
                <div class="mt-3 h-px bg-gray-200">
                    <div class="h-full bg-green-500 transition-all"
                        style="width: {{ $totalHoje > 0 ? round($administradoHoje / $totalHoje * 100) : 0 }}%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-2">
                    {{ $totalHoje > 0 ? round($administradoHoje / $totalHoje * 100) : 0 }}% administradas
                </p>
            </div>

            {{-- Manhã --}}
            <div class="flex-1 p-5 border-b sm:border-b-0 sm:border-r border-gray-200">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Manhã</p>
                <p class="text-5xl font-black text-gray-900 tracking-tighter leading-none tabular-nums">
                    {{ $admManha }}<span class="text-2xl font-normal text-gray-300">/{{ $totalManha }}</span>
                </p>
                <div class="mt-3 h-px bg-gray-200">
                    <div class="h-full bg-amber-400 transition-all"
                        style="width: {{ $totalManha > 0 ? round($admManha / $totalManha * 100) : 0 }}%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-2">
                    {{ $totalManha > 0 ? round($admManha / $totalManha * 100) : 0 }}%
                </p>
            </div>

            {{-- Tarde --}}
            <div class="flex-1 p-5">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Tarde</p>
                <p class="text-5xl font-black text-gray-900 tracking-tighter leading-none tabular-nums">
                    {{ $admTarde }}<span class="text-2xl font-normal text-gray-300">/{{ $totalTarde }}</span>
                </p>
                <div class="mt-3 h-px bg-gray-200">
                    <div class="h-full bg-gray-700 transition-all"
                        style="width: {{ $totalTarde > 0 ? round($admTarde / $totalTarde * 100) : 0 }}%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-2">
                    {{ $totalTarde > 0 ? round($admTarde / $totalTarde * 100) : 0 }}%
                </p>
            </div>

        </div>
    </div>

    {{-- ── Pendências ── --}}
    @php $totalPendencias = $semProntuarioHoje->count() + $comDosesPendentes->count() + $semPrescricao->count(); @endphp
    @if ($totalPendencias > 0)
        <div class="relative bg-white rounded border border-gray-200 px-5 py-4">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-400 rounded-l"></div>
            <p class="text-xs font-bold uppercase tracking-widest text-amber-600 mb-2.5">
                {{ $totalPendencias }} pendência{{ $totalPendencias > 1 ? 's' : '' }} hoje
            </p>
            <div class="flex flex-wrap gap-2">

                @if ($semProntuarioHoje->count())
                    <div x-data="{ aberto: false }" class="relative">
                        <button @click="aberto = !aberto"
                            class="text-xs font-semibold text-gray-700 border border-gray-300 bg-white px-3 py-1 rounded-sm hover:bg-gray-50 hover:border-gray-500 transition-colors">
                            {{ $semProntuarioHoje->count() }} sem prontuário
                        </button>
                        <div x-show="aberto" x-cloak @click.outside="aberto = false"
                            class="absolute left-0 top-full mt-1 z-10 w-56 bg-white rounded border border-gray-200 shadow-md py-1">
                            @foreach ($semProntuarioHoje as $interna)
                                <a href="{{ route('pacientes.show', $interna) }}" wire:navigate
                                    class="block px-4 py-1.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors truncate">
                                    {{ $interna->nome }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($comDosesPendentes->count())
                    <div x-data="{ aberto: false }" class="relative">
                        <button @click="aberto = !aberto"
                            class="text-xs font-semibold text-gray-700 border border-gray-300 bg-white px-3 py-1 rounded-sm hover:bg-gray-50 hover:border-gray-500 transition-colors">
                            {{ $comDosesPendentes->count() }} com doses pendentes
                        </button>
                        <div x-show="aberto" x-cloak @click.outside="aberto = false"
                            class="absolute left-0 top-full mt-1 z-10 w-64 bg-white rounded border border-gray-200 shadow-md py-1">
                            @foreach ($comDosesPendentes as $interna)
                                @php
                                    $regs = $registrosPorPaciente->get($interna->id, collect());
                                    $pendentes = $regs->where('administrado', false)->count();
                                @endphp
                                <a href="{{ route('pacientes.show', $interna) }}" wire:navigate
                                    class="flex items-center justify-between px-4 py-1.5 hover:bg-gray-50 transition-colors">
                                    <span class="text-sm text-gray-700 truncate">{{ $interna->nome }}</span>
                                    <span class="ml-2 shrink-0 text-xs font-bold text-amber-600">{{ $pendentes }}×</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($semPrescricao->count())
                    <div x-data="{ aberto: false }" class="relative">
                        <button @click="aberto = !aberto"
                            class="text-xs font-semibold text-gray-700 border border-gray-300 bg-white px-3 py-1 rounded-sm hover:bg-gray-50 hover:border-gray-500 transition-colors">
                            {{ $semPrescricao->count() }} sem prescrição
                        </button>
                        <div x-show="aberto" x-cloak @click.outside="aberto = false"
                            class="absolute left-0 top-full mt-1 z-10 w-56 bg-white rounded border border-gray-200 shadow-md py-1">
                            @foreach ($semPrescricao as $interna)
                                <a href="{{ route('pacientes.show', $interna) }}" wire:navigate
                                    class="block px-4 py-1.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors truncate">
                                    {{ $interna->nome }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endif

    {{-- ── Lista + Feed ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">

        {{-- Lista de internas --}}
        <div class="lg:col-span-2 bg-white rounded border border-gray-200">
            <div class="flex items-baseline justify-between px-5 py-3 border-b border-gray-200">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Internas ativas</p>
                <a href="{{ route('pacientes.index') }}" wire:navigate
                    class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-gray-900 transition-colors">
                    Ver todas →
                </a>
            </div>

            @if ($internasAtivas->isEmpty())
                <div class="px-5 py-14 text-center text-gray-400 text-sm">Nenhuma interna ativa.</div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach ($internasAtivas as $interna)
                        @php
                            $regs          = $registrosPorPaciente->get($interna->id, collect());
                            $totalRegs     = $regs->count();
                            $admRegs       = $regs->where('administrado', true)->count();
                            $temPront      = isset($prontuariosHoje[$interna->id]);
                            $temPrescricao = $interna->prescricoes_ativas_count > 0;
                        @endphp
                        <a href="{{ route('pacientes.show', $interna) }}" wire:navigate
                            class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors group">

                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 group-hover:underline truncate">
                                    {{ $interna->nome }}
                                </p>
                                @if ($interna->quarto)
                                    <p class="text-xs text-gray-400 mt-0.5">Quarto {{ $interna->quarto }}</p>
                                @endif
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0 ml-4">

                                {{-- Prescrições --}}
                                <span class="text-xs font-bold uppercase tracking-wide px-1.5 py-0.5 border rounded-sm
                                    {{ $temPrescricao
                                        ? 'border-blue-200 bg-blue-50 text-blue-700'
                                        : 'border-red-200 bg-red-50 text-red-600' }}">
                                    {{ $interna->prescricoes_ativas_count }} Rx
                                </span>

                                {{-- Prontuário --}}
                                <span class="text-xs font-bold uppercase tracking-wide px-1.5 py-0.5 border rounded-sm
                                    {{ $temPront
                                        ? 'border-green-200 bg-green-50 text-green-700'
                                        : 'border-amber-200 bg-amber-50 text-amber-700' }}">
                                    {{ $temPront ? 'Pront ✓' : 'Pront !' }}
                                </span>

                                {{-- Doses --}}
                                @if ($totalRegs > 0)
                                    <span class="text-xs font-bold uppercase tracking-wide px-1.5 py-0.5 border rounded-sm tabular-nums
                                        {{ $admRegs === $totalRegs
                                            ? 'border-green-200 bg-green-50 text-green-700'
                                            : 'border-orange-200 bg-orange-50 text-orange-700' }}">
                                        {{ $admRegs }}/{{ $totalRegs }}
                                    </span>
                                @else
                                    <span class="text-xs font-bold px-1.5 py-0.5 border border-gray-200 text-gray-400 rounded-sm">
                                        —
                                    </span>
                                @endif

                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Feed de doses --}}
        <div class="bg-white rounded border border-gray-200 flex flex-col">
            <div class="px-5 py-3 border-b border-gray-200">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500">Doses administradas hoje</p>
            </div>

            @if ($dosesRecentes->isEmpty())
                <div class="flex-1 flex flex-col items-center justify-center py-14 text-center px-5">
                    <p class="text-sm font-semibold text-gray-500">Sem doses registradas</p>
                    <p class="text-xs text-gray-400 mt-1">As administrações aparecerão aqui.</p>
                </div>
            @else
                <div class="flex-1 divide-y divide-gray-100 overflow-y-auto">
                    @foreach ($dosesRecentes as $reg)
                        <div class="px-5 py-3">
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                {{ $reg->prescricao->paciente->nome ?? '—' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">
                                {{ $reg->prescricao->medicamento->nome ?? '—' }} · {{ $reg->prescricao->dose }}
                            </p>
                            <p class="text-xs font-bold uppercase tracking-wide text-gray-400 mt-1">
                                {{ ucfirst($reg->turno) }}@if ($reg->usuario) · {{ $reg->usuario->name }}@endif
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>
