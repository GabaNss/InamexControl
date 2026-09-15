<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $user ? __('Editar Usuario') : __('Novo Usuario') }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form wire:submit="salvar" class="space-y-6">
                    <div>
                        <x-input-label for="name" value="Nome" />
                        <x-text-input id="name" type="text" class="mt-1 block w-full" wire:model.blur="name" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="E-mail" />
                        <x-text-input id="email" type="email" class="mt-1 block w-full" wire:model.blur="email" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="cargo" value="Cargo" />
                        <select id="cargo" wire:model="cargo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @foreach ($cargos as $opcao)
                                <option value="{{ $opcao }}">{{ App\Models\User::labelCargo($opcao) }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('cargo')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="turno_inicio" value="Início do turno" />
                            <x-text-input id="turno_inicio" type="time" class="mt-1 block w-full" wire:model.blur="turno_inicio" required />
                            <x-input-error :messages="$errors->get('turno_inicio')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="turno_fim" value="Fim do turno" />
                            <x-text-input id="turno_fim" type="time" class="mt-1 block w-full" wire:model.blur="turno_fim" required />
                            <x-input-error :messages="$errors->get('turno_fim')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="password" :value="$user ? 'Nova senha (deixe em branco para manter)' : 'Senha'" />
                        <x-text-input id="password" type="password" class="mt-1 block w-full" wire:model.blur="password" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="Confirmar senha" />
                        <x-text-input id="password_confirmation" type="password" class="mt-1 block w-full" wire:model.blur="password_confirmation" autocomplete="new-password" />
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="ativo" class="rounded border-gray-300">
                        <span class="text-sm text-gray-700">Usuario ativo</span>
                    </label>

                    <div class="flex items-center gap-4">
                        <x-primary-button type="submit" wire:loading.attr="disabled" wire:target="salvar">
                            <span wire:loading.remove wire:target="salvar">Salvar</span>
                            <span wire:loading wire:target="salvar">Salvando...</span>
                        </x-primary-button>
                        <a href="{{ route('usuarios.index') }}" wire:navigate class="text-sm text-gray-500 hover:underline">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
