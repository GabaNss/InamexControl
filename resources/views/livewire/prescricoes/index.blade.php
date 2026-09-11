<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Prescricoes') }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap items-end gap-4 justify-between">
                    <div class="flex flex-wrap gap-4">
                        <div>
                            <x-input-label for="busca" value="Buscar por paciente" />
                            <x-text-input id="busca" type="text" class="mt-1 block w-64" wire:model.live.debounce.300ms="busca" />
                        </div>
                        <div>
                            <x-input-label for="status" value="Status" />
                            <select id="status" wire:model.live="status" class="mt-1 block w-40 rounded-md border-gray-300 shadow-sm">
                                <option value="ativas">Ativas</option>
                                <option value="inativas">Inativas</option>
                                <option value="todas">Todas</option>
                            </select>
                        </div>
                    </div>
                    @can('create', App\Models\Prescricao::class)
                        <a href="{{ route('prescricoes.create') }}" wire:navigate>
                            <x-primary-button>Nova Prescrição</x-primary-button>
                        </a>
                    @endcan
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
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
