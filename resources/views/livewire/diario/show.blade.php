<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Status do Diario') }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4">{{ session('status') }}</div>
            @endif

            @can('encerrar-diario-manual')
                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center gap-4">
                    <p class="text-sm text-gray-600 flex-1">
                        O encerramento normal e automatico, todo dia a meia-noite. Use as opcoes abaixo apenas em situacoes excepcionais.
                    </p>
                    @if ($diaHojeEncerrado)
                        <button
                            type="button"
                            wire:click="reabrirHoje"
                            wire:confirm="Reabrir o diario de hoje? Os registros voltarao a ser editaveis."
                            wire:loading.attr="disabled"
                            wire:target="reabrirHoje"
                            class="w-full sm:w-auto shrink-0 inline-flex items-center justify-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md text-sm text-white hover:bg-yellow-700 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="reabrirHoje">Reabrir hoje</span>
                            <span wire:loading wire:target="reabrirHoje">Reabrindo...</span>
                        </button>
                    @else
                        <button
                            type="button"
                            wire:click="encerrarHoje"
                            wire:confirm="Encerrar o diario de hoje agora? Os registros de hoje ficarao imutaveis."
                            wire:loading.attr="disabled"
                            wire:target="encerrarHoje"
                            class="w-full sm:w-auto shrink-0 inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm text-white hover:bg-red-700 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="encerrarHoje">Encerrar hoje manualmente</span>
                            <span wire:loading wire:target="encerrarHoje">Encerrando...</span>
                        </button>
                    @endif
                </div>
            @endcan

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                {{-- Mobile: cards --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @forelse ($historico as $status)
                        <div class="p-4 flex items-center justify-between gap-2">
                            <div>
                                <div class="font-medium text-gray-900 text-sm">{{ $status->data->format('d/m/Y') }}</div>
                                @if ($status->encerrado && $status->encerrado_em)
                                    <div class="text-xs text-gray-400 mt-0.5">Encerrado em {{ $status->encerrado_em->format('d/m/Y H:i') }}</div>
                                @endif
                            </div>
                            <span class="shrink-0 px-2 py-1 rounded-full text-xs font-medium {{ $status->encerrado ? 'bg-gray-200 text-gray-700' : 'bg-green-100 text-green-800' }}">
                                {{ $status->encerrado ? 'Encerrado' : 'Aberto' }}
                            </span>
                        </div>
                    @empty
                        <div class="p-6 text-sm text-gray-500 text-center">Nenhum diario registrado ainda.</div>
                    @endforelse
                </div>

                {{-- Desktop: tabela --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Encerrado em</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($historico as $status)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $status->data->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $status->encerrado ? 'bg-gray-200 text-gray-700' : 'bg-green-100 text-green-800' }}">
                                            {{ $status->encerrado ? 'Encerrado' : 'Aberto' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $status->encerrado_em?->format('d/m/Y H:i') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-sm text-gray-500 text-center">Nenhum diario registrado ainda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
