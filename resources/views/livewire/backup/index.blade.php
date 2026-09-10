<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Backup') }}
    </h2>
</x-slot>

<div>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">

                <div>
                    <p class="font-medium text-gray-800 text-sm">Backup manual do banco de dados</p>
                    <p class="text-xs text-gray-500 mt-1">
                        Gera uma cópia completa de todos os dados do sistema (internas, prescrições,
                        registros, prontuários, usuários etc.) em um único arquivo <code>.dump</code>,
                        que é baixado direto para o seu computador. Guarde esse arquivo em local seguro —
                        ele contém dados sensíveis de saúde.
                    </p>
                </div>

                @if ($erro)
                    <div class="border border-red-200 bg-red-50 text-red-700 text-sm rounded-md p-3">
                        {{ $erro }}
                    </div>
                @endif

                <div class="border-t border-gray-100 pt-6">
                    <button wire:click="baixar" wire:loading.attr="disabled" wire:target="baixar"
                        class="inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent rounded-md text-sm text-white hover:bg-brand-700 transition-colors disabled:opacity-60">
                        <span wire:loading.remove wire:target="baixar">Baixar backup agora</span>
                        <span wire:loading wire:target="baixar">Gerando backup...</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
