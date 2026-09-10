<div class="space-y-5">
    @if (session('erro-registros'))
        <div class="flex items-center gap-2 bg-red-50 text-red-700 text-sm rounded-lg px-4 py-3 border border-red-200">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('erro-registros') }}
        </div>
    @endif

    {{-- Filtro de data + status --}}
    <div class="flex flex-wrap items-center gap-4">
        <div>
            <x-input-label for="data-registros" value="Data" />
            <input id="data-registros" type="date" wire:model.live="data"
                class="mt-1 block rounded-lg border-gray-200 shadow-sm text-sm focus:border-brand-400 focus:ring-brand-400"
                max="{{ now()->toDateString() }}"
            >
        </div>
        <div class="mt-5">
            @if ($diaAberto)
                <span class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full bg-green-50 text-green-700 ring-1 ring-green-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    Dia aberto
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full bg-gray-100 text-gray-600 ring-1 ring-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Somente leitura
                </span>
            @endif
        </div>

        {{-- Contador de progresso --}}
        @php
            $todos         = collect($registrosPorTurno->values()->flatten());
            $administrados = $todos->where('administrado', true)->count();
            $total         = $todos->count();
        @endphp
        @if ($total > 0)
            <div class="mt-5 flex items-center gap-2">
                <div class="text-xs text-gray-500">
                    <span class="font-semibold text-gray-800">{{ $administrados }}</span> / {{ $total }} administrado(s)
                </div>
                <div class="w-24 h-1.5 rounded-full bg-gray-200 overflow-hidden">
                    <div class="h-full rounded-full bg-green-500 transition-all"
                        style="width: {{ $total > 0 ? round($administrados/$total*100) : 0 }}%"></div>
                </div>
            </div>
        @endif
    </div>

    {{-- Turnos --}}
    @foreach (['manha' => 'Manhã', 'tarde' => 'Tarde'] as $turno => $rotulo)
        @php $registros = $registrosPorTurno->get($turno, collect()); @endphp
        <div class="rounded-xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if ($turno === 'manha')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        @endif
                    </svg>
                    <span class="text-sm font-medium text-gray-700">{{ $rotulo }}</span>
                </div>
                @if ($registros->count())
                    @php $adm = $registros->where('administrado', true)->count(); @endphp
                    <span class="text-xs text-gray-400">{{ $adm }}/{{ $registros->count() }}</span>
                @endif
            </div>

            @if ($registros->isEmpty())
                <div class="px-4 py-5 text-sm text-gray-400 text-center italic">
                    Nenhum registro para este turno.
                </div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach ($registros as $registro)
                        <div wire:key="reg-{{ $registro->id }}" class="flex items-center gap-3 px-4 py-3 {{ $registro->administrado ? 'bg-green-50/30' : '' }}">

                            {{-- Checkbox --}}
                            <label class="flex items-center cursor-pointer {{ !$diaAberto ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <input type="checkbox"
                                    class="w-5 h-5 rounded border-gray-300 text-green-500 focus:ring-green-400 {{ !$diaAberto ? 'cursor-not-allowed' : 'cursor-pointer' }}"
                                    {{ $registro->administrado ? 'checked' : '' }}
                                    {{ !$diaAberto ? 'disabled' : '' }}
                                    wire:click="{{ $diaAberto ? 'marcar('.$registro->id.', '.($registro->administrado ? 'false' : 'true').')' : '' }}"
                                >
                            </label>

                            {{-- Medicamento --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium {{ $registro->administrado ? 'text-gray-500 line-through' : 'text-gray-800' }}">
                                    {{ $registro->prescricao->medicamento->nome }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $registro->prescricao->dose }}</p>
                            </div>

                            {{-- Observação --}}
                            <input type="text"
                                class="w-44 rounded-lg border-gray-200 text-xs shadow-sm focus:border-brand-400 focus:ring-brand-400 {{ !$diaAberto ? 'bg-gray-50 text-gray-400' : '' }}"
                                placeholder="Observação..."
                                value="{{ $registro->observacao }}"
                                {{ !$diaAberto ? 'disabled' : '' }}
                                wire:change="{{ $diaAberto ? 'atualizarObservacao('.$registro->id.', $event.target.value)' : '' }}"
                            >

                            {{-- Responsável --}}
                            <span class="hidden sm:block text-xs text-gray-400 w-28 truncate text-right">
                                {{ $registro->usuario?->name ?? '' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
