<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Internas') }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-3 sm:justify-between">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div>
                            <x-input-label for="busca" value="Buscar" />
                            <x-text-input
                                id="busca"
                                type="text"
                                class="mt-1 block w-full sm:w-64"
                                placeholder="Nome ou prontuario..."
                                wire:model.live.debounce.300ms="busca"
                            />
                        </div>
                        <div>
                            <x-input-label for="status" value="Status" />
                            <select id="status" wire:model.live="status" class="mt-1 block w-full sm:w-40 rounded-md border-gray-300 shadow-sm">
                                <option value="ativos">Ativos</option>
                                <option value="inativos">Inativos</option>
                                <option value="todos">Todos</option>
                            </select>
                        </div>
                    </div>

                    @can('create', App\Models\Paciente::class)
                        <a href="{{ route('pacientes.create') }}" wire:navigate class="w-full sm:w-auto">
                            <x-primary-button class="w-full sm:w-auto justify-center">Nova Interna</x-primary-button>
                        </a>
                    @endcan
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                {{-- Mobile: cards --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @forelse ($pacientes as $paciente)
                        <div class="p-4 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <a href="{{ route('pacientes.show', $paciente) }}" wire:navigate class="font-medium text-gray-900 hover:underline leading-tight">
                                    {{ $paciente->nome }}
                                </a>
                                <span class="shrink-0 px-2 py-1 rounded-full text-xs font-medium {{ $paciente->ativa ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $paciente->ativa ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500 space-y-0.5">
                                <div><span class="font-medium text-gray-600">Prontuário:</span> {{ $paciente->prontuario }}</div>
                                <div><span class="font-medium text-gray-600">Quarto:</span> {{ $paciente->quarto ?? '—' }}</div>
                            </div>
                            @canany(['update', 'delete'], $paciente)
                                <div class="flex gap-4 pt-1 border-t border-gray-50">
                                    @can('update', $paciente)
                                        <a href="{{ route('pacientes.edit', $paciente) }}" wire:navigate class="text-sm text-blue-600 font-medium hover:underline py-1">Editar</a>
                                    @endcan
                                    @can('delete', $paciente)
                                        <button
                                            type="button"
                                            wire:click="excluir({{ $paciente->id }})"
                                            wire:confirm="Tem certeza que deseja remover este paciente?"
                                            class="text-sm text-red-600 font-medium hover:underline py-1"
                                        >Remover</button>
                                    @endcan
                                </div>
                            @endcanany
                        </div>
                    @empty
                        <div class="p-6 text-sm text-gray-500 text-center">Nenhuma interna encontrada.</div>
                    @endforelse
                </div>

                {{-- Desktop: tabela --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prontuário</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quarto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pacientes as $paciente)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <a href="{{ route('pacientes.show', $paciente) }}" wire:navigate class="hover:underline">
                                            {{ $paciente->nome }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $paciente->prontuario }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $paciente->quarto ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $paciente->ativa ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $paciente->ativa ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right space-x-2">
                                        @can('update', $paciente)
                                            <a href="{{ route('pacientes.edit', $paciente) }}" wire:navigate class="text-blue-600 hover:underline">Editar</a>
                                        @endcan
                                        @can('delete', $paciente)
                                            <button
                                                type="button"
                                                wire:click="excluir({{ $paciente->id }})"
                                                wire:confirm="Tem certeza que deseja remover este paciente?"
                                                class="text-red-600 hover:underline"
                                            >Remover</button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-sm text-gray-500 text-center">Nenhuma interna encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $pacientes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
