<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Usuarios') }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    <div>
                        <x-input-label for="busca" value="Buscar" />
                        <x-text-input id="busca" type="text" class="mt-1 block w-full sm:w-64" placeholder="Nome ou e-mail..." wire:model.live.debounce.300ms="busca" />
                    </div>
                    @can('create', App\Models\User::class)
                        <a href="{{ route('usuarios.create') }}" wire:navigate class="w-full sm:w-auto">
                            <x-primary-button class="w-full sm:w-auto justify-center">Novo Usuario</x-primary-button>
                        </a>
                    @endcan
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                {{-- Mobile: cards --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @forelse ($usuarios as $usuario)
                        <div class="p-4 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <span class="font-medium text-gray-900">{{ $usuario->name }}</span>
                                <span class="shrink-0 px-2 py-1 rounded-full text-xs font-medium {{ $usuario->ativo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $usuario->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500 space-y-0.5">
                                <div class="truncate">{{ $usuario->email }}</div>
                                <div><span class="font-medium text-gray-600">Cargo:</span> <span class="capitalize">{{ $usuario->cargo }}</span></div>
                            </div>
                            @canany(['update', 'delete'], $usuario)
                                <div class="flex gap-4 pt-1 border-t border-gray-50">
                                    @can('update', $usuario)
                                        <a href="{{ route('usuarios.edit', $usuario) }}" wire:navigate class="text-sm text-blue-600 font-medium hover:underline py-1">Editar</a>
                                    @endcan
                                    @if ($usuario->ativo)
                                        @can('delete', $usuario)
                                            <button type="button" wire:click="desativar({{ $usuario->id }})" wire:confirm="Desativar este usuario?" class="text-sm text-red-600 font-medium hover:underline py-1">Desativar</button>
                                        @endcan
                                    @else
                                        @can('update', $usuario)
                                            <button type="button" wire:click="reativar({{ $usuario->id }})" class="text-sm text-green-600 font-medium hover:underline py-1">Reativar</button>
                                        @endcan
                                    @endif
                                </div>
                            @endcanany
                        </div>
                    @empty
                        <div class="p-6 text-sm text-gray-500 text-center">Nenhum usuario encontrado.</div>
                    @endforelse
                </div>

                {{-- Desktop: tabela --}}
                <div class="hidden md:block overflow-x-auto">
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
