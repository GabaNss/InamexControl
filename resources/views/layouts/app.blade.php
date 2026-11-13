<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonte Figtree servida localmente via Vite (ver resources/css/app.css) -->
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        {{-- Pular para o conteúdo: visível apenas ao navegar por teclado (acessibilidade). --}}
        <a href="#conteudo"
            class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:px-4 focus:py-2 focus:rounded-md focus:bg-brand-600 focus:text-white focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
            Pular para o conteúdo
        </a>
        <div class="min-h-screen bg-brand-50/40">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main id="conteudo" tabindex="-1">
                {{ $slot }}
            </main>

            <footer class="mt-8 border-t border-gray-100 bg-white">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 text-xs text-gray-600 flex flex-wrap items-center gap-x-2 gap-y-1">
                    <span>Documento confidencial — dados pessoais sensíveis (LGPD).</span>
                    <a href="{{ route('privacidade') }}" class="underline hover:text-brand-700 rounded focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">Aviso de Privacidade</a>
                    <span aria-hidden="true">·</span>
                    <a href="{{ route('termos') }}" class="underline hover:text-brand-700 rounded focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">Termo de Uso</a>
                </div>
            </footer>
        </div>
    </body>
</html>
