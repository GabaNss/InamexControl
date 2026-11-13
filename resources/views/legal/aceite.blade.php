<x-legal-layout titulo="Termo de Uso e Confidencialidade">

    <p>Antes de continuar, você precisa <strong>ler e aceitar</strong> o Termo de Uso e
        Confidencialidade. O acesso ao sistema lida com dados pessoais sensíveis de saúde e todos
        os acessos são auditados.</p>

    <h2>Resumo dos seus compromissos</h2>
    <ul>
        <li>Manter <strong>sigilo</strong> sobre todos os dados das pacientes;</li>
        <li>Usar o sistema <strong>apenas para fins assistenciais</strong>;</li>
        <li><strong>Não compartilhar</strong> sua senha nem usar a conta de outra pessoa;</li>
        <li><strong>Não exportar ou divulgar</strong> dados fora da finalidade autorizada;</li>
        <li>Estar ciente de que <strong>todos os acessos são registrados e auditados</strong>.</li>
    </ul>

    <p>Leia o termo completo em
        <a href="{{ route('termos') }}" target="_blank" rel="noopener">Termo de Uso</a>
        e o <a href="{{ route('privacidade') }}" target="_blank" rel="noopener">Aviso de Privacidade</a>.</p>

    <div class="mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 not-prose">
        <form method="POST" action="{{ route('termos.aceitar') }}">
            @csrf
            <button type="submit"
                class="w-full inline-flex items-center justify-center px-5 py-2.5 rounded-md bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 transition">
                Li e aceito o Termo
            </button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full inline-flex items-center justify-center px-5 py-2.5 rounded-md border border-gray-300 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 transition">
                Sair
            </button>
        </form>
    </div>

</x-legal-layout>
