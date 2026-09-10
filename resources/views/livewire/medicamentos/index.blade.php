<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Medicamentos') }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4">{{ session('status') }}</div>
            @endif
            @if (session('erro'))
                <div class="bg-red-50 text-red-700 text-sm rounded-md p-4">{{ session('erro') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap items-end gap-4 justify-between">
                    <div>
                        <x-input-label for="busca" value="Buscar" />
                        <x-text-input id="busca" type="text" class="mt-1 block w-64" placeholder="Nome do medicamento..." wire:model.live.debounce.300ms="busca" />
                    </div>
                    @can('create', App\Models\Medicamento::class)
                        <a href="{{ route('medicamentos.create') }}" wire:navigate>
                            <x-primary-button>Novo Medicamento</x-primary-button>
                        </a>
                    @endcan
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Concentracao</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Via de Administracao</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($medicamentos as $medicamento)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $medicamento->nome }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $medicamento->concentracao }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $medicamento->via_administracao }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs {{ $medicamento->ativo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $medicamento->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-right space-x-2">
                                    @can('update', $medicamento)
                                        <a href="{{ route('medicamentos.edit', $medicamento) }}" wire:navigate class="text-blue-600 hover:underline">Editar</a>
                                    @endcan
                                    @can('delete', $medicamento)
                                        <button type="button" wire:click="excluir({{ $medicamento->id }})" wire:confirm="Remover este medicamento?" class="text-red-600 hover:underline">Remover</button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-sm text-gray-500 text-center">Nenhum medicamento encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <div class="p-4">{{ $medicamentos->links() }}</div>
            </div>
        </div>
    </div>
</div>
