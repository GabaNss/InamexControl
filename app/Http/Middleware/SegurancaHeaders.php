<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Aplica cabecalhos de seguranca a toda resposta HTTP e impede que paginas
 * autenticadas (com dados sensiveis de pacientes) sejam retidas em cache.
 *
 * Content-Security-Policy endurecido com nonce por requisicao: scripts inline
 * (incluindo o que o Livewire injeta) e os assets do Vite so executam se
 * trouxerem o nonce — 'unsafe-inline' NAO e mais aceito em script-src, o que
 * neutraliza o principal vetor de XSS. O nonce e gerado via Vite::useCspNonce()
 * antes de renderizar a view; o Laravel o aplica nas tags @vite e o Livewire 4
 * o reutiliza automaticamente (Vite::cspNonce()).
 *
 * Mantido 'unsafe-eval' porque o Alpine (embarcado no Livewire) avalia suas
 * expressoes via new Function() — so seria removivel migrando para o build
 * @alpinejs/csp, reescrevendo toda expressao Alpine como componente. Em
 * style-src mantem-se 'unsafe-inline' porque o Alpine escreve estilos inline
 * (x-show/x-transition), que o nonce nao cobre.
 */
class SegurancaHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Gera o nonce ANTES de renderizar a view, para que @vite e o Livewire
        // o apliquem nas tags <script>.
        $nonce = Vite::useCspNonce();

        $response = $next($request);

        // Paginas autenticadas nunca devem ser retidas em cache (dados sensiveis).
        if ($request->user()) {
            $response->headers->set('Cache-Control', 'no-store, private');
        }

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'same-origin');
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=(), magnetometer=(), accelerometer=(), gyroscope=(), interest-cohort=()'
        );

        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy($nonce));

        return $response;
    }

    /**
     * Monta o CSP. Em local, libera o dev server do Vite (http + websocket) para
     * o hot-reload; em producao fica restrito a propria origem + nonce.
     */
    private function contentSecurityPolicy(string $nonce): string
    {
        // script-src: sem 'unsafe-inline'; apenas self, o nonce e unsafe-eval
        // (Alpine). style-src mantem unsafe-inline (estilos inline do Alpine).
        $script = "'self' 'nonce-{$nonce}' 'unsafe-eval'";
        $style = "'self' 'unsafe-inline'";
        $connect = "'self'";

        if (app()->isLocal()) {
            // Vite dev server (porta padrao 5173) e seu websocket de HMR.
            $vite = 'http://localhost:5173 http://127.0.0.1:5173';
            $viteWs = 'ws://localhost:5173 ws://127.0.0.1:5173';
            $script .= " {$vite}";
            $style .= " {$vite}";
            $connect .= " {$vite} {$viteWs}";
        }

        $directives = [
            "default-src 'self'",
            "script-src {$script}",
            "style-src {$style}",
            "img-src 'self' data:",
            "font-src 'self' data:",
            "connect-src {$connect}",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ];

        return implode('; ', $directives);
    }
}
