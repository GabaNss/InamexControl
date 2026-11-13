<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forca o usuario com senha provisoria (must_change_password = true) a definir
 * uma nova senha e um e-mail de recuperacao antes de usar qualquer outra tela.
 *
 * Qualquer navegacao (GET) e redirecionada para a tela de primeiro acesso. As
 * rotas abaixo sao liberadas para o fluxo funcionar:
 *  - a propria tela de primeiro acesso;
 *  - logout (o usuario pode sempre sair);
 *  - os endpoints XHR do Livewire (a tela de primeiro acesso e um componente
 *    Livewire; sem isso o envio do formulario seria redirecionado e quebraria).
 *
 * Liberar o endpoint do Livewire e seguro: o usuario so consegue um snapshot
 * valido (assinado pelo Livewire) do componente de primeiro acesso, ja que toda
 * outra pagina redireciona no GET antes de renderizar qualquer componente.
 */
class PrimeiroAcesso
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->user()?->must_change_password &&
            ! $request->routeIs('primeiro-acesso') &&
            ! $request->routeIs('logout') &&
            ! $request->routeIs('*livewire.update') &&
            ! $request->routeIs('*livewire.upload-file')
        ) {
            return redirect()->route('primeiro-acesso');
        }

        return $next($request);
    }
}
