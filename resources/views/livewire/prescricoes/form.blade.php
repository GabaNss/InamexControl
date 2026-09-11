<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $prescricao ? __('Editar Prescrição') : __('Nova Prescrição') }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form wire:submit="salvar" class="space-y-6">
                    <div>
                        <x-input-label for="paciente_id" value="Paciente" />
                        <select id="paciente_id" wire:model="paciente_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Selecione...</option>
                            @foreach ($pacientes as $paciente)
                                <option value="{{ $paciente->id }}">{{ $paciente->nome }} ({{ $paciente->prontuario }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('paciente_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="medicamento_id" value="Medicamento" />
                        <select id="medicamento_id" wire:model="medicamento_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Selecione...</option>
                            @foreach ($medicamentos as $medicamento)
                                <option value="{{ $medicamento->id }}">{{ $medicamento->nome }} ({{ $medicamento->concentracao }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('medicamento_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="dose" value="Dose" />
                        <x-text-input id="dose" type="text" class="mt-1 block w-full" wire:model.blur="dose" placeholder="ex: 1 comprimido, 5ml..." required />
                        <x-input-error :messages="$errors->get('dose')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="horario" value="Horario" />
                        <select id="horario" wire:model="horario" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="manha">Manha</option>
                            <option value="tarde">Tarde</option>
                        </select>
                        <x-input-error :messages="$errors->get('horario')" class="mt-2" />
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="ativa" class="rounded border-gray-300">
                        <span class="text-sm text-gray-700">Prescrição ativa</span>
                    </label>

                    <div class="flex items-center gap-4">
                        <x-primary-button type="submit" wire:loading.attr="disabled" wire:target="salvar">
                            <span wire:loading.remove wire:target="salvar">Salvar</span>
                            <span wire:loading wire:target="salvar">Salvando...</span>
                        </x-primary-button>
                        <a href="{{ route('prescricoes.index') }}" wire:navigate class="text-sm text-gray-500 hover:underline">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
