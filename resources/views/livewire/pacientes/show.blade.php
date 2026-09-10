<x-slot name="header">
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('pacientes.index') }}" wire:navigate class="hover:text-brand-600 transition-colors">Internas</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-800 font-medium">{{ $paciente->nome }}</span>
    </div>
</x-slot>

<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Cartão de identidade da interna --}}
            <div class="bg-white shadow-sm sm:rounded-xl overflow-hidden">
                <div class="h-2 bg-gradient-to-r from-brand-600 to-accent-500"></div>
                <div class="p-6 flex items-center gap-5">

                    {{-- Dados --}}
                    <div class="flex-1 min-w-0">
                        <h1 class="text-lg font-semibold text-gray-900 truncate">{{ $paciente->nome }}</h1>
                    </div>

                    @can('update', $paciente)
                        <a href="{{ route('pacientes.edit', $paciente) }}" wire:navigate
                            class="shrink-0 inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Hub com abas --}}
            <div x-data="{ aba: 'ficha' }" class="bg-white shadow-sm sm:rounded-xl overflow-hidden">

                {{-- Tab bar --}}
                <div class="border-b border-gray-100 overflow-x-auto">
                    <nav class="flex min-w-max px-2">

                        @php
                            $abas = [
                                'ficha' => [
                                    'label' => 'Ficha Médica',
                                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                                ],
                                'diario' => [
                                    'label' => 'Prontuário Diário',
                                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                                ],
                                'historico' => [
                                    'label' => 'Prontuário Histórico',
                                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                                ],
                                'prescricoes' => [
                                    'label' => 'Prescrições',
                                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>',
                                ],
                                'registros' => [
                                    'label' => 'Registros',
                                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
                                ],
                            ];
                        @endphp

                        @foreach ($abas as $key => $aba)
                            <button
                                type="button"
                                @click="aba = '{{ $key }}'"
                                :class="aba === '{{ $key }}'
                                    ? 'border-brand-500 text-brand-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="flex items-center gap-2 px-4 py-4 border-b-2 text-sm font-medium whitespace-nowrap transition-colors"
                            >
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $aba['icon'] !!}
                                </svg>
                                {{ $aba['label'] }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                {{-- Conteúdo das abas --}}
                <div class="p-6 min-h-96">

                    <div x-show="aba === 'ficha'" x-cloak>
                        @livewire('internas.ficha-medica', ['pacienteId' => $paciente->id], key('ficha-'.$paciente->id))
                    </div>

                    <div x-show="aba === 'diario'" x-cloak>
                        @livewire('internas.prontuario-diario', ['pacienteId' => $paciente->id], key('diario-'.$paciente->id))
                    </div>

                    <div x-show="aba === 'historico'" x-cloak>
                        @livewire('internas.prontuario-historico', ['pacienteId' => $paciente->id], key('historico-'.$paciente->id))
                    </div>

                    <div x-show="aba === 'prescricoes'" x-cloak>

                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Prescrições Ativas</h3>
                            @can('create', App\Models\Prescricao::class)
                                <a href="{{ route('prescricoes.create', ['paciente_id' => $paciente->id]) }}" wire:navigate
                                    class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-600 hover:text-brand-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Nova prescrição
                                </a>
                            @endcan
                        </div>

                        @php
                            $ativas   = $prescricoes->where('ativa', true);
                            $inativas = $prescricoes->where('ativa', false);
                        @endphp

                        @if ($prescricoes->isEmpty())
                            <div class="text-center py-16 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                                <p class="text-sm">Nenhuma prescrição cadastrada.</p>
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach ($ativas as $prescricao)
                                    <div class="flex items-center gap-3 rounded-lg border border-gray-100 bg-white px-4 py-3 hover:border-gray-200 transition-colors">
                                        <div class="w-10 h-10 rounded-full bg-brand-50 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">{{ $prescricao->medicamento->nome }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $prescricao->dose }} · <span class="capitalize">{{ $prescricao->horario }}</span>
                                                @if ($prescricao->prescritor)
                                                    · Dr(a). {{ $prescricao->prescritor->name }}
                                                @endif
                                            </p>
                                        </div>
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-green-50 text-green-700 ring-1 ring-green-200">Ativa</span>
                                        @can('update', $prescricao)
                                            <a href="{{ route('prescricoes.edit', $prescricao) }}" wire:navigate
                                                class="text-xs text-gray-400 hover:text-brand-600 transition-colors">Editar</a>
                                            <button type="button"
                                                wire:click="desativarPrescricao({{ $prescricao->id }})"
                                                wire:confirm="Desativar esta prescrição? Ela ficará visível na seção de inativas."
                                                class="text-xs text-amber-500 hover:text-amber-700 transition-colors">
                                                Desativar
                                            </button>
                                        @endcan
                                        @can('delete', $prescricao)
                                            <button type="button"
                                                wire:click="excluirPrescricao({{ $prescricao->id }})"
                                                wire:confirm="Excluir permanentemente esta prescrição? Esta ação não pode ser desfeita."
                                                class="text-xs text-red-400 hover:text-red-600 transition-colors">
                                                Excluir
                                            </button>
                                        @endcan
                                    </div>
                                @endforeach

                                @if ($inativas->count())
                                    <details class="mt-4">
                                        <summary class="text-xs text-gray-400 cursor-pointer hover:text-gray-600 select-none">
                                            Ver {{ $inativas->count() }} prescrição(ões) inativa(s)
                                        </summary>
                                        <div class="space-y-2 mt-2">
                                            @foreach ($inativas as $prescricao)
                                                <div class="flex items-center gap-3 rounded-lg border border-gray-100 bg-gray-50 px-4 py-3">
                                                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-500">{{ $prescricao->medicamento->nome }}</p>
                                                        <p class="text-xs text-gray-400 mt-0.5">{{ $prescricao->dose }} · <span class="capitalize">{{ $prescricao->horario }}</span></p>
                                                    </div>
                                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">Inativa</span>
                                                    @can('delete', $prescricao)
                                                        <button type="button"
                                                            wire:click="excluirPrescricao({{ $prescricao->id }})"
                                                            wire:confirm="Excluir permanentemente esta prescrição?"
                                                            class="text-xs text-red-400 hover:text-red-600 transition-colors">
                                                            Excluir
                                                        </button>
                                                    @endcan
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div x-show="aba === 'registros'" x-cloak>
                        @livewire('internas.registros-panel', ['pacienteId' => $paciente->id], key('registros-'.$paciente->id))
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
