<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Prescricoes') }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-3 sm:justify-between">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div>
                            <x-input-label for="busca" value="Buscar por paciente" />
                            <x-text-input id="busca" type="text" class="mt-1 block w-full sm:w-64" wire:model.live.debounce.300ms="busca" />
                        </div>
                        <div>
                            <x-input-label for="status" value="Status" />
                            <select id="status" wire:model.live="status" class="mt-1 block w-full sm:w-40 rounded-md border-gray-300 shadow-sm">
                                <option value="ativas">Ativas</option>
                                <option value="inativas">Inativas</option>
                                <option value="todas">Todas</option>
                            </select>
                        </div>
                    </div>
                    @can('create', App\Models\Prescricao::class)
                        <a href="{{ route('prescricoes.create') }}" wire:navigate class="w-full sm:w-auto">
                            <x-primary-button class="w-full sm:w-auto justify-center">Nova Prescrição</x-primary-button>
                        </a>
                    @endcan
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                {{-- Mobile: cards --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @forelse ($prescricoes as $prescricao)
                        <div class="p-4 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <span class="font-medium text-gray-900">{{ $prescricao->paciente->nome }}</span>
                                <span class="shrink-0 px-2 py-1 rounded-full text-xs font-medium {{ $prescricao->ativa ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $prescricao->ativa ? 'Ativa' : 'Inativa' }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500 space-y-0.5">
                                <div><span class="font-medium text-gray-600">Medicamento:</span> {{ $prescricao->medicamento->nome }}</div>
                                <div><span class="font-medium text-gray-600">Dose:</span> {{ $prescricao->dose }}</div>
                                <div><span class="font-medium text-gray-600">Horário:</span> <span class="capitalize">{{ $prescricao->horario }}</span></div>
                            </div>
                            @canany(['update', 'delete'], $prescricao)
                                <div class="flex gap-4 pt-1 border-t border-gray-50">
                                    @can('update', $prescricao)
                                        <a href="{{ route('prescricoes.edit', $prescricao) }}" wire:navigate class="text-sm text-blue-600 font-medium hover:underline py-1">Editar</a>
                                    @endcan
                                    @can('delete', $prescricao)
                                        @if ($prescricao->ativa)
                                            <button type="button" wire:click="inativar({{ $prescricao->id }})" wire:confirm="Inativar esta prescricao?" class="text-sm text-red-600 font-medium hover:underline py-1">Inativar</button>
                                        @endif
                                    @endcan
                                </div>
                            @endcanany
                        </div>
                    @empty
                        <div class="p-6 text-sm text-gray-500 text-center">Nenhuma prescricao encontrada.</div>
                    @endforelse
                </div>

                {{-- Desktop: tabela --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicamento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dose</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($prescricoes as $prescricao)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $prescricao->paciente->nome }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $prescricao->medicamento->nome }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $prescricao->dose }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 capitalize">{{ $prescricao->horario }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $prescricao->ativa ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $prescricao->ativa ? 'Ativa' : 'Inativa' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right space-x-2">
                                        @can('update', $prescricao)
                                            <a href="{{ route('prescricoes.edit', $prescricao) }}" wire:navigate class="text-blue-600 hover:underline">Editar</a>
                                        @endcan
                                        @can('delete', $prescricao)
                                            @if ($prescricao->ativa)
                                                <button type="button" wire:click="inativar({{ $prescricao->id }})" wire:confirm="Inativar esta prescricao?" class="text-red-600 hover:underline">Inativar</button>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-sm text-gray-500 text-center">Nenhuma prescricao encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">{{ $prescricoes->links() }}</div>
            </div>
        </div>
    </div>
</div>
