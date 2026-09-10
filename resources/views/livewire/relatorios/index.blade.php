<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Exportação') }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">

                <div>
                    <x-input-label for="interna" value="Selecione a interna" />
                    <select id="interna" wire:model.live="pacienteId"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">— Escolha uma interna —</option>
                        @foreach ($internas as $interna)
                            <option value="{{ $interna->id }}">
                                {{ $interna->nome }} ({{ $interna->prontuario }})
                            </option>
                        @endforeach
                    </select>
                </div>

                @if ($pacienteSelecionado)
                    <div class="border-t border-gray-100 pt-6 space-y-6">

                        {{-- Ficha Médica --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-800 text-sm">Ficha Médica</p>
                                <p class="text-xs text-gray-500 mt-0.5">Diagnósticos, medicamentos crônicos e laudos</p>
                            </div>
                            <a href="{{ route('exportacao.ficha-medica', $pacienteSelecionado) }}"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent rounded-md text-sm text-white hover:bg-brand-700 transition-colors">
                                Exportar PDF
                            </a>
                        </div>

                        {{-- Prontuário Diário --}}
                        <div class="border-t border-gray-100 pt-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800 text-sm">Prontuário Diário</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Registros diários do período selecionado</p>
                                    <div class="flex flex-wrap gap-4 mt-3">
                                        <div>
                                            <x-input-label for="inicio" value="De" />
                                            <input id="inicio" type="date" wire:model="inicio"
                                                class="mt-1 block rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                        <div>
                                            <x-input-label for="fim" value="Até" />
                                            <input id="fim" type="date" wire:model="fim"
                                                class="mt-1 block rounded-md border-gray-300 shadow-sm text-sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-7">
                                    <a href="{{ route('exportacao.prontuario-diario', ['paciente' => $pacienteSelecionado, 'inicio' => $inicio, 'fim' => $fim]) }}"
                                        target="_blank"
                                        class="inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent rounded-md text-sm text-white hover:bg-brand-700 transition-colors">
                                        Exportar PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Exportação Completa --}}
                        <div class="border-t border-gray-100 pt-5 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-800 text-sm">Relatório Completo</p>
                                <p class="text-xs text-gray-500 mt-0.5">Ficha médica + todos os prontuários diários + histórico</p>
                            </div>
                            <a href="{{ route('exportacao.completo', $pacienteSelecionado) }}"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-gray-700 border border-transparent rounded-md text-sm text-white hover:bg-gray-800 transition-colors">
                                Exportar Completo
                            </a>
                        </div>

                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
