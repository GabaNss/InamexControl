<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Registros de Medicação') }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-50 text-green-700 text-sm rounded-md p-4">{{ session('status') }}</div>
            @endif
            @if (session('erro'))
                <div class="bg-red-50 text-red-700 text-sm rounded-md p-4">{{ session('erro') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <x-input-label for="data" value="Data" />
                    <input id="data" type="date" wire:model.live="data" class="mt-1 block rounded-md border-gray-300 shadow-sm">
                </div>
                <span class="px-3 py-1 rounded-full text-sm {{ $diaAberto ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                    {{ $diaAberto ? 'Dia aberto' : 'Dia encerrado — somente leitura' }}
                </span>
            </div>

            @foreach (['manha' => 'Manha', 'tarde' => 'Tarde'] as $turno => $rotulo)
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-medium text-gray-900">{{ $rotulo }}</h3>
                    </div>
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicamento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dose</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Administrado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Observação</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsável</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($registrosPorTurno->get($turno, []) as $registro)
                                <tr wire:key="registro-{{ $registro->id }}">
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $registro->prescricao->paciente->nome }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $registro->prescricao->medicamento->nome }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $registro->prescricao->dose }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <input
                                            type="checkbox"
                                            class="rounded border-gray-300"
                                            {{ $registro->administrado ? 'checked' : '' }}
                                            {{ $diaAberto ? '' : 'disabled' }}
                                            wire:click="marcar({{ $registro->id }}, {{ $registro->administrado ? 'false' : 'true' }})"
                                        >
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <input
                                            type="text"
                                            class="block w-48 rounded-md border-gray-300 text-sm shadow-sm"
                                            value="{{ $registro->observacao }}"
                                            {{ $diaAberto ? '' : 'disabled' }}
                                            wire:change="atualizarObservacao({{ $registro->id }}, $event.target.value)"
                                        >
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $registro->usuario?->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-sm text-gray-500 text-center">Nenhum registro para este turno.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
