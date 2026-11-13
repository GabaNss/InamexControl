<div>
    <div class="mb-6 text-sm text-gray-600">
        Bem-vindo ao <strong>InamexControl</strong>. Antes de continuar, defina sua nova senha
        e cadastre um e-mail de recuperação — ele será usado para redefinir sua senha no futuro
        caso você a esqueça.
    </div>

    @if (session('status'))
        <div class="mb-4 text-sm font-medium text-green-600">{{ session('status') }}</div>
    @endif

    <form wire:submit="salvar" class="space-y-5">
        <div>
            <x-input-label for="recovery_email" value="E-mail de recuperação" />
            <x-text-input
                id="recovery_email"
                type="email"
                class="mt-1 block w-full"
                wire:model.blur="recovery_email"
                required
                autofocus
                autocomplete="email"
            />
            <p class="mt-1 text-xs text-gray-500">
                Diferente do seu login — será usado apenas para redefinir sua senha.
            </p>
            <x-input-error :messages="$errors->get('recovery_email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Nova senha" />
            <x-text-input
                id="password"
                type="password"
                class="mt-1 block w-full"
                wire:model.blur="password"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmar nova senha" />
            <x-text-input
                id="password_confirmation"
                type="password"
                class="mt-1 block w-full"
                wire:model.blur="password_confirmation"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between pt-1">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 underline hover:text-gray-700">
                    Sair
                </button>
            </form>

            <x-primary-button wire:loading.attr="disabled" wire:target="salvar">
                <span wire:loading.remove wire:target="salvar">Confirmar</span>
                <span wire:loading wire:target="salvar">Salvando...</span>
            </x-primary-button>
        </div>
    </form>
</div>
