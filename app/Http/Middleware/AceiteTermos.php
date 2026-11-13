<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Obriga o usuario autenticado a aceitar a versao vigente do Termo de Uso/
 * Confidencialidade antes de usar qualquer tela do sistema (LGPD). Reexige o
 * aceite quando o termo e republicado (config('termos.publicado_em') mais
 * recente que o termos_aceitos_em do usuario).
 *
 * Liberadas para o fluxo funcionar: as proprias rotas de aceite, as paginas
 * publicas do termo/privacidade, o logout e os endpoints XHR do Livewire.
 */
class AceiteTermos
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $this->precisaAceitar($user) && ! $this->rotaLiberada($request)) {
            return redirect()->guest(route('termos.aceite'));
        }

        return $next($request);
    }

    private function precisaAceitar($user): bool
    {
        return $user->termos_aceitos_em === null
            || $user->termos_aceitos_em->lt(config('termos.publicado_em'));
    }

    private function rotaLiberada(Request $request): bool
    {
        return $request->routeIs('termos.aceite')
            || $request->routeIs('termos.aceitar')
            || $request->routeIs('termos')
            || $request->routeIs('privacidade')
            || $request->routeIs('logout')
            || $request->routeIs('*livewire.update')
            || $request->routeIs('*livewire.upload-file');
    }
}
