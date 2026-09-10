<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Status do Diario') }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4">{{ session('status') }}</div>
            @endif

            @can('encerrar-diario-manual')
                <div class="bg-white shadow-sm sm:rounded-lg p-6 flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        O encerramento normal e automatico, todo dia a meia-noite. Use a opcao abaixo apenas em situacoes excepcionais.
                    </p>
                    <button
                        type="button"
                        wire:click="encerrarHoje"
                        wire:confirm="Encerrar o diario de hoje agora? Os registros de hoje ficarao imutaveis."
                        wire:loading.attr="disabled"
                        wire:target="encerrarHoje"
                        class="shrink-0 ml-4 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm text-white hover:bg-red-700 disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="encerrarHoje">Encerrar hoje manualmente</span>
                        <span wire:loading wire:target="encerrarHoje">Encerrando...</span>
                    </button>
                </div>
            @endcan

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
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
