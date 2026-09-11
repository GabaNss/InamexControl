<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $paciente ? __('Editar Interna') : __('Nova Interna') }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form wire:submit="salvar" class="space-y-6">
                    <div>
                        <x-input-label for="nome" value="Nome" />
                        <x-text-input id="nome" type="text" class="mt-1 block w-full" wire:model.blur="nome" required autofocus />
                        <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="prontuario" value="Prontuário" />
                        <x-text-input id="prontuario" type="text" class="mt-1 block w-full" wire:model.blur="prontuario" required />
                        <x-input-error :messages="$errors->get('prontuario')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="quarto" value="Quarto" />
                        <x-text-input id="quarto" type="text" class="mt-1 block w-full" wire:model.blur="quarto" />
                        <x-input-error :messages="$errors->get('quarto')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="foto" value="Foto" />
                        <input id="foto" type="file" wire:model="foto" class="mt-1 block w-full text-sm" accept="image/*" />
                        <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                        @if ($paciente?->foto)
                            <p class="text-xs text-gray-500 mt-1">Foto atual sera substituida apenas se voce enviar uma nova.</p>
                        @endif
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="ativa" class="rounded border-gray-300">
                        <span class="text-sm text-gray-700">Interna ativa</span>
                    </label>

                    <div class="flex items-center gap-4">
                        <x-primary-button type="submit" wire:loading.attr="disabled" wire:target="salvar">
                            <span wire:loading.remove wire:target="salvar">Salvar</span>
                            <span wire:loading wire:target="salvar">Salvando...</span>
                        </x-primary-button>
                        <a href="{{ route('pacientes.index') }}" wire:navigate class="text-sm text-gray-500 hover:underline">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
