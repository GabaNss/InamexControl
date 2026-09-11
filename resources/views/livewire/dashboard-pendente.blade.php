<div class="flex items-center justify-center min-h-[60vh]">
    <div class="bg-white rounded border border-gray-200 px-10 py-12 max-w-md w-full text-center">
        <div class="w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center mx-auto mb-5">
            <svg class="w-7 h-7 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 mb-2">Acesso pendente</h2>
        <p class="text-sm text-gray-500 mb-1">
            Olá, <strong>{{ explode(' ', $usuario->name)[0] }}</strong>.
        </p>
        <p class="text-sm text-gray-500">
            Sua conta foi criada, mas ainda não tem um cargo atribuído.
            Aguarde o administrador do sistema liberar o seu acesso.
        </p>
    </div>
</div>
