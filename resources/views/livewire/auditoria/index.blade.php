<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Auditoria
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Filtros --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap items-end gap-4">
                    <div>
                        <x-input-label for="busca" value="Usuário" />
                        <x-text-input id="busca" type="text" class="mt-1 block w-48" placeholder="Nome do usuário..." wire:model.live.debounce.300ms="busca" />
                    </div>
                    <div>
                        <x-input-label for="acao" value="Ação" />
                        <select id="acao" wire:model.live="acao" class="mt-1 block w-56 border-gray-300 rounded-md shadow-sm text-sm focus:ring-brand-500 focus:border-brand-500">
                            <option value="">Todas</option>
                            @foreach ($acoes as $a)
                                <option value="{{ $a }}">{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="inicio" value="De" />
                        <x-text-input id="inicio" type="date" class="mt-1 block w-40" wire:model.live="inicio" />
                    </div>
                    <div>
                        <x-input-label for="fim" value="Até" />
                        <x-text-input id="fim" type="date" class="mt-1 block w-40" wire:model.live="fim" />
                    </div>
                    @if ($busca || $acao || $inicio || $fim)
                        <div class="pb-0.5">
                            <button wire:click="$set('busca', ''); $set('acao', ''); $set('inicio', ''); $set('fim', '')"
                                class="text-sm text-gray-500 hover:text-gray-700 underline">
                                Limpar filtros
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tabela --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">Data / Hora</th>
                                <th class="px-4 py-3 text-left">Usuário</th>
                                <th class="px-4 py-3 text-left">Ação</th>
                                <th class="px-4 py-3 text-left">Alvo</th>
                                <th class="px-4 py-3 text-left">IP</th>
                                <th class="px-4 py-3 text-left">Antes / Depois</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                        {{ $log->created_at->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">
                                        {{ $log->usuario?->name ?? 'Sistema' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-brand-50 text-brand-700">
                                            {{ $log->acao }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">
                                        @if ($log->alvo_type)
                                            {{ class_basename($log->alvo_type) }} #{{ $log->alvo_id }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-400 text-xs whitespace-nowrap">
                                        {{ $log->ip_address ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500 max-w-xs">
                                        @if ($log->dados_antes || $log->dados_depois)
                                            <details>
                                                <summary class="cursor-pointer text-brand-600 hover:underline">Ver detalhes</summary>
                                                <div class="mt-1 space-y-1">
                                                    @if ($log->dados_antes)
                                                        <div><span class="font-medium text-red-600">Antes:</span> {{ json_encode($log->dados_antes, JSON_UNESCAPED_UNICODE) }}</div>
                                                    @endif
                                                    @if ($log->dados_depois)
                                                        <div><span class="font-medium text-green-600">Depois:</span> {{ json_encode($log->dados_depois, JSON_UNESCAPED_UNICODE) }}</div>
                                                    @endif
                                                </div>
                                            </details>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Nenhum registro encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($logs->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
