<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Confia no(s) proxy(s)/load balancer da hospedagem para que o Laravel
        // reconheca corretamente o esquema (https) e o IP original do cliente
        // a partir dos headers X-Forwarded-* — essencial para
        // URL::forceScheme('https') e Request::isSecure() funcionarem quando
        // o TLS e terminado no proxy (Nginx/Cloudflare/load balancer) antes
        // de chegar no PHP-FPM. Dados de pacientes exigem HTTPS de ponta a
        // ponta (ver App\Providers\AppServiceProvider::boot()).
        // Em producao: defina TRUSTED_PROXIES no .env com o IP/CIDR do proxy
        // (ex: IP do load balancer ou range do Cloudflare). Deixar em branco
        // desativa a confianca em proxies, o que e mais seguro caso nao haja um.
        $proxies = env('TRUSTED_PROXIES');
        if ($proxies) {
            $middleware->trustProxies(at: $proxies);
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
