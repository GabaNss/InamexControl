<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $medicamento ? __('Editar Medicamento') : __('Novo Medicamento') }}
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
                        <x-input-label for="concentracao" value="Concentracao" />
                        <x-text-input id="concentracao" type="text" class="mt-1 block w-full" wire:model.blur="concentracao" placeholder="ex: 500mg" required />
                        <x-input-error :messages="$errors->get('concentracao')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="via_administracao" value="Via de Administracao" />
                        <x-text-input id="via_administracao" type="text" class="mt-1 block w-full" wire:model.blur="via_administracao" placeholder="ex: oral, intramuscular..." required />
                        <x-input-error :messages="$errors->get('via_administracao')" class="mt-2" />
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="ativo" class="rounded border-gray-300">
                        <span class="text-sm text-gray-700">Medicamento ativo</span>
                    </label>

                    <div class="flex items-center gap-4">
                        <x-primary-button type="submit" wire:loading.attr="disabled" wire:target="salvar">
                            <span wire:loading.remove wire:target="salvar">Salvar</span>
                            <span wire:loading wire:target="salvar">Salvando...</span>
                        </x-primary-button>
                        <a href="{{ route('medicamentos.index') }}" wire:navigate class="text-sm text-gray-500 hover:underline">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
