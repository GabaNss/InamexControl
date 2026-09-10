<div>
    @if (session('status-ficha'))
        <div class="mb-4 flex items-center gap-2 bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 border border-green-200">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('status-ficha') }}
        </div>
    @endif

    @if ($editando)
        <div class="space-y-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                    {{ $ficha ? 'Editando Ficha Médica' : 'Nova Ficha Médica' }}
                </h3>
                <button type="button" wire:click="cancelar" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">Cancelar</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <x-input-label value="Diagnósticos" />
                    <p class="text-xs text-gray-400 mb-1">CID, hipóteses diagnósticas</p>
                    <textarea wire:model.blur="diagnosticos" rows="5"
                        placeholder="Ex: F20.0 - Esquizofrenia paranoide..."
                        class="mt-0 block w-full rounded-lg border-gray-200 shadow-sm text-sm focus:border-brand-400 focus:ring-brand-400"></textarea>
                    <x-input-error :messages="$errors->get('diagnosticos')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Medicamentos Crônicos" />
                    <p class="text-xs text-gray-400 mb-1">Uso contínuo (fora das prescrições diárias)</p>
                    <textarea wire:model.blur="medicamentosCronicos" rows="5"
                        placeholder="Ex: Risperidona 2mg..."
                        class="mt-0 block w-full rounded-lg border-gray-200 shadow-sm text-sm focus:border-brand-400 focus:ring-brand-400"></textarea>
                    <x-input-error :messages="$errors->get('medicamentosCronicos')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label value="Laudos e Exames" />
                <p class="text-xs text-gray-400 mb-1">Resultados de exames, laudos médicos, pareceres</p>
                <textarea wire:model.blur="laudos" rows="5"
                    placeholder="Ex: ECG 12/06 — ritmo sinusal..."
                    class="mt-0 block w-full rounded-lg border-gray-200 shadow-sm text-sm focus:border-brand-400 focus:ring-brand-400"></textarea>
                <x-input-error :messages="$errors->get('laudos')" class="mt-1" />
            </div>

            <div class="flex items-center gap-3 pt-1">
                <x-primary-button type="button" wire:click="salvar" wire:loading.attr="disabled" wire:target="salvar">
                    <span wire:loading.remove wire:target="salvar">Salvar ficha</span>
                    <span wire:loading wire:target="salvar">Salvando...</span>
                </x-primary-button>
            </div>
        </div>

    @else
        <div class="flex items-start justify-between mb-5">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Ficha Médica</h3>
            <div class="flex items-center gap-3">
                @if ($ficha)
                    @can('update', $ficha)
                        <button type="button" wire:click="iniciarEdicao"
                            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar
                        </button>
                    @endcan
                    @can('delete', $ficha)
                        <button type="button" wire:click="deletar"
                            wire:confirm="Remover toda a ficha médica? Esta ação não pode ser desfeita."
                            class="inline-flex items-center gap-1.5 text-sm text-red-400 hover:text-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Remover
                        </button>
                    @endcan
                @else
                    @can('create', App\Models\FichaMedica::class)
                        <button type="button" wire:click="iniciarEdicao"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-600 hover:text-brand-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Criar ficha
                        </button>
                    @endcan
                @endif
            </div>
        </div>

        @if (!$diagnosticos && !$medicamentosCronicos && !$laudos)
            <div class="text-center py-14 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-medium">Ficha médica em branco</p>
                <p class="text-xs mt-1">Os diagnósticos, medicamentos e laudos serão exibidos aqui.</p>
                @can('create', App\Models\FichaMedica::class)
                    @if (!$ficha)
                        <button type="button" wire:click="iniciarEdicao"
                            class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-brand-600 hover:text-brand-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Criar ficha agora
                        </button>
                    @endif
                @endcan
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                {{-- Diagnósticos --}}
                <div class="rounded-xl border border-gray-100 overflow-hidden">
                    <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                        <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Diagnósticos</span>
                    </div>
                    <div class="px-4 py-3 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed min-h-20">
                        {{ $diagnosticos ?: '—' }}
                    </div>
                </div>

                {{-- Medicamentos Crônicos --}}
                <div class="rounded-xl border border-gray-100 overflow-hidden">
                    <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                        <svg class="w-5 h-5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Medicamentos Crônicos</span>
                    </div>
                    <div class="px-4 py-3 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed min-h-20">
                        {{ $medicamentosCronicos ?: '—' }}
                    </div>
                </div>
            </div>

            {{-- Laudos --}}
            <div class="rounded-xl border border-gray-100 overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Laudos e Exames</span>
                </div>
                <div class="px-4 py-3 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed min-h-20">
                    {{ $laudos ?: '—' }}
                </div>
            </div>
        @endif
    @endif
</div>
