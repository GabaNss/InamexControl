<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Aplica cabecalhos de seguranca a toda resposta HTTP e impede que paginas
 * autenticadas (com dados sensiveis de pacientes) sejam retidas em cache por
 * navegadores ou proxies.
 *
 * O Content-Security-Policy e deliberadamente compativel com Livewire e Alpine
 * (ambos precisam de 'unsafe-eval'/'unsafe-inline') e com o Vite — em ambiente
 * local o dev server do Vite (HMR via websocket) e liberado para nao quebrar o
 * `npm run dev`. Em producao os assets sao servidos pela propria origem.
 */
class SegurancaHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
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

        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy());

        return $response;
    }

    /**
     * Monta o CSP. Em local, inclui o dev server do Vite (http + websocket) para
     * que o hot-reload funcione; em producao fica restrito a propria origem.
     */
    private function contentSecurityPolicy(): string
    {
        // Alpine avalia expressoes (unsafe-eval) e Livewire injeta script inline.
        $script = "'self' 'unsafe-inline' 'unsafe-eval'";
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
