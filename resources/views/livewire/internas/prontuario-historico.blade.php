<div>
    @if (session('status-historico'))
        <div class="mb-4 flex items-center gap-2 bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 border border-green-200">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('status-historico') }}
        </div>
    @endif

    @if ($editando)
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                    {{ $historico ? 'Editando Prontuário Histórico' : 'Novo Prontuário Histórico' }}
                </h3>
                <button type="button" wire:click="cancelar"
                    class="text-sm text-gray-400 hover:text-gray-600 transition-colors">Cancelar</button>
            </div>
            <p class="text-xs text-gray-400 -mt-2">
                Registro acumulado — inclui histórico clínico, evolução, internações anteriores e qualquer informação relevante de longo prazo.
            </p>
            <textarea
                wire:model.blur="conteudo"
                rows="16"
                placeholder="Histórico clínico da interna..."
                class="block w-full rounded-xl border-gray-200 shadow-sm text-sm leading-relaxed focus:border-brand-400 focus:ring-brand-400"
            ></textarea>
            <x-input-error :messages="$errors->get('conteudo')" />

            <div class="flex items-center gap-3">
                <x-primary-button type="button" wire:click="salvar" wire:loading.attr="disabled" wire:target="salvar">
                    <span wire:loading.remove wire:target="salvar">Salvar</span>
                    <span wire:loading wire:target="salvar">Salvando...</span>
                </x-primary-button>
            </div>
        </div>

    @else
        <div class="flex items-start justify-between mb-5">
            <div>
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Prontuário Histórico</h3>
                <p class="text-xs text-gray-400 mt-0.5">Registro acumulado de longo prazo</p>
            </div>
            <div class="flex items-center gap-3">
                @if ($historico)
                    @can('update', $historico)
                        <button type="button" wire:click="iniciarEdicao"
                            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar
                        </button>
                    @endcan
                    @can('delete', $historico)
                        <button type="button" wire:click="deletar"
                            wire:confirm="Remover todo o prontuário histórico? Esta ação não pode ser desfeita."
                            class="inline-flex items-center gap-1.5 text-sm text-red-400 hover:text-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Remover
                        </button>
                    @endcan
                @else
                    @can('create', App\Models\ProntuarioHistorico::class)
                        <button type="button" wire:click="iniciarEdicao"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-600 hover:text-brand-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Criar histórico
                        </button>
                    @endcan
                @endif
            </div>
        </div>

        @if (!$conteudo)
            <div class="text-center py-14 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <p class="text-sm font-medium">Prontuário histórico em branco</p>
                <p class="text-xs mt-1">O histórico clínico acumulado será exibido aqui.</p>
                @can('create', App\Models\ProntuarioHistorico::class)
                    @if (!$historico)
                        <button type="button" wire:click="iniciarEdicao"
                            class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-brand-600 hover:text-brand-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Criar agora
                        </button>
                    @endif
                @endcan
            </div>
        @else
            <div class="rounded-xl border border-gray-100 overflow-hidden">
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-400 rounded-l"></div>
                    <div class="pl-5 pr-4 py-4 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap min-h-48">
                        {{ $conteudo }}
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
