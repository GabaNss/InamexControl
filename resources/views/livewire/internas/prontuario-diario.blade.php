<div>
    @if (session('status-diario'))
        <div class="mb-4 flex items-center gap-2 bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 border border-green-200">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('status-diario') }}
        </div>
    @endif

    {{-- Tira de dias --}}
    <div class="mb-5 overflow-x-auto pb-1">
        <div class="flex gap-1.5 min-w-max">
            @foreach ($diasRecentes as $dia)
                @php $selecionado = $dataSelecionada === $dia['data']; @endphp
                <button
                    type="button"
                    wire:click="selecionarData('{{ $dia['data'] }}')"
                    class="flex flex-col items-center px-3 py-2 rounded-lg text-xs font-medium transition-all border
                        {{ $selecionado
                            ? 'bg-brand-600 text-white border-brand-600 shadow-sm'
                            : ($dia['temConteudo']
                                ? 'bg-brand-50 text-brand-700 border-brand-200 hover:bg-brand-100'
                                : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300') }}"
                >
                    <span class="text-xs leading-none mb-0.5 {{ $selecionado ? 'text-brand-200' : 'text-gray-400' }}">
                        {{ $dia['diaSemana'] }}
                    </span>
                    <span>{{ $dia['label'] }}</span>
                    @if ($dia['isHoje'] || $dia['temConteudo'])
                        <span class="w-1 h-1 rounded-full mt-1 {{ $selecionado ? 'bg-brand-300' : 'bg-brand-400' }}"></span>
                    @else
                        <span class="w-1 h-1 mt-1"></span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- Cabeçalho do dia: data + status + botões de ação --}}
    <div class="flex items-center justify-between mb-4 gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <h4 class="text-sm font-semibold text-gray-700 truncate">
                {{ \Carbon\Carbon::parse($dataSelecionada)->locale('pt_BR')->isoFormat('dddd, D [de] MMMM') }}
            </h4>
            @if ($diaEncerrado)
                <span class="shrink-0 inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 ring-1 ring-gray-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Encerrado
                </span>
            @else
                <span class="shrink-0 inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-green-50 text-green-700 ring-1 ring-green-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    Aberto
                </span>
            @endif
        </div>

        {{-- Botões de ação --}}
        @if (!$editando)
            <div class="shrink-0 flex items-center gap-3">
                @if (!$diaEncerrado)
                    @can('create', App\Models\ProntuarioDiario::class)
                        <button type="button" wire:click="iniciarEdicao"
                            class="inline-flex items-center gap-1.5 text-sm text-brand-600 hover:text-brand-700 font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{ $conteudo ? 'Editar' : 'Registrar' }}
                        </button>
                    @endcan
                @endif
                @if ($ultimaEdicao)
                    @can('delete', $ultimaEdicao)
                        <button type="button" wire:click="deletar"
                            wire:confirm="Remover o registro deste dia? Esta ação não pode ser desfeita."
                            class="inline-flex items-center gap-1.5 text-sm text-red-400 hover:text-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Remover
                        </button>
                    @endcan
                @endif
            </div>
        @endif
    </div>

    @if ($editando)
        <div class="space-y-3">
            <textarea
                wire:model.blur="conteudo"
                rows="10"
                placeholder="Registre as observações do dia..."
                class="block w-full rounded-xl border-gray-200 shadow-sm text-sm leading-relaxed focus:border-brand-400 focus:ring-brand-400"
            ></textarea>
            <x-input-error :messages="$errors->get('conteudo')" />

            <div class="flex items-center gap-3">
                <x-primary-button type="button" wire:click="salvar" wire:loading.attr="disabled" wire:target="salvar">
                    <span wire:loading.remove wire:target="salvar">Salvar</span>
                    <span wire:loading wire:target="salvar">Salvando...</span>
                </x-primary-button>
                <button type="button" wire:click="cancelar"
                    class="text-sm text-gray-400 hover:text-gray-600 transition-colors">Cancelar</button>
            </div>
        </div>

    @else
        {{-- Conteúdo do dia --}}
        <div class="relative rounded-xl border {{ $diaEncerrado ? 'border-gray-200 bg-gray-50' : 'border-brand-100 bg-brand-50/30' }} overflow-hidden">
            @if ($diaEncerrado)
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-300"></div>
            @else
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-400"></div>
            @endif
            <div class="px-5 py-4 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap min-h-36">
                {{ $conteudo ?: '' }}
                @if (!$conteudo)
                    <span class="text-gray-400 italic">Nenhum registro para este dia.</span>
                @endif
            </div>
        </div>

        {{-- Rodapé: quem editou --}}
        @if ($ultimaEdicao?->atualizadoPor)
            <p class="text-xs text-gray-400 mt-3">
                Editado por {{ $ultimaEdicao->atualizadoPor->name }}
                em {{ $ultimaEdicao->updated_at->format('d/m/Y H:i') }}
            </p>
        @endif
    @endif
</div>
