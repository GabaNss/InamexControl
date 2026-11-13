<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Informe o e-mail de recuperação cadastrado no seu primeiro acesso.
        Enviaremos um link para você redefinir sua senha.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="recovery_email" value="E-mail de recuperação" />
            <x-text-input
                id="recovery_email"
                class="block mt-1 w-full"
                type="email"
                name="recovery_email"
                :value="old('recovery_email')"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('recovery_email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Enviar link de redefinição
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
