<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Usuarios') }}
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
                    <div>
                        <x-input-label for="busca" value="Buscar" />
                        <x-text-input id="busca" type="text" class="mt-1 block w-64" placeholder="Nome ou e-mail..." wire:model.live.debounce.300ms="busca" />
                    </div>
                    @can('create', App\Models\User::class)
                        <a href="{{ route('usuarios.create') }}" wire:navigate>
                            <x-primary-button>Novo Usuario</x-primary-button>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mail</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cargo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($usuarios as $usuario)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $usuario->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $usuario->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 capitalize">{{ $usuario->cargo }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs {{ $usuario->ativo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $usuario->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-right space-x-2">
                                    @can('update', $usuario)
                                        <a href="{{ route('usuarios.edit', $usuario) }}" wire:navigate class="text-blue-600 hover:underline">Editar</a>
                                    @endcan
                                    @if ($usuario->ativo)
                                        @can('delete', $usuario)
                                            <button type="button" wire:click="desativar({{ $usuario->id }})" wire:confirm="Desativar este usuario?" class="text-red-600 hover:underline">Desativar</button>
                                        @endcan
                                    @else
                                        @can('update', $usuario)
                                            <button type="button" wire:click="reativar({{ $usuario->id }})" class="text-green-600 hover:underline">Reativar</button>
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-sm text-gray-500 text-center">Nenhum usuario encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <div class="p-4">{{ $usuarios->links() }}</div>
            </div>
        </div>
    </div>
</div>
