@props(['titulo'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $titulo }} — {{ config('app.name') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-brand-50/40 text-gray-800">
        <a href="#conteudo"
            class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:px-4 focus:py-2 focus:rounded-md focus:bg-brand-600 focus:text-white focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
            Pular para o conteúdo
        </a>

        <div class="min-h-screen">
            <header class="bg-white shadow-sm">
                <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex items-center gap-3">
                    <x-application-logo class="w-8 h-8" />
                    <span class="text-sm font-semibold uppercase tracking-widest text-brand-700">INAMEX</span>
                </div>
            </header>

            <main id="conteudo" tabindex="-1" class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="bg-white shadow-sm rounded-xl p-6 sm:p-10">
                    <h1 class="text-2xl font-semibold text-gray-900 mb-2">{{ $titulo }}</h1>
                    <p class="text-sm text-gray-600 mb-8">Versão {{ config('termos.versao') }} · atualizado em {{ config('termos.publicado_em')->format('d/m/Y') }}</p>

                    <div class="prose prose-sm max-w-none text-gray-800 space-y-4 [&_h2]:text-lg [&_h2]:font-semibold [&_h2]:text-gray-900 [&_h2]:mt-6 [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:space-y-1 [&_a]:text-brand-700 [&_a]:underline">
                        {{ $slot }}
                    </div>
                </div>

                <div class="mt-6 text-sm">
                    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('login') }}"
                        class="text-brand-700 underline rounded-md focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">
                        ← Voltar
                    </a>
                </div>
            </main>
        </div>
    </body>
</html>
