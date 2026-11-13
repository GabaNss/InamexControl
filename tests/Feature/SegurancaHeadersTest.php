<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SegurancaHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_cabecalhos_de_seguranca_presentes_no_login(): void
    {
        $resposta = $this->get(route('login'));

        $resposta->assertHeader('X-Frame-Options', 'DENY');
        $resposta->assertHeader('X-Content-Type-Options', 'nosniff');
        $resposta->assertHeader('Referrer-Policy', 'same-origin');
        $csp = $resposta->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);

        // CSP endurecido: script-src usa nonce e NAO aceita 'unsafe-inline'.
        preg_match('/script-src ([^;]+)/', $csp, $m);
        $scriptSrc = $m[1] ?? '';
        $this->assertMatchesRegularExpression('/nonce-[A-Za-z0-9+\/=]+/', $scriptSrc);
        $this->assertStringNotContainsString("'unsafe-inline'", $scriptSrc);
    }

    public function test_nonce_do_csp_e_aplicado_nos_scripts_da_pagina(): void
    {
        $resposta = $this->get(route('login'));

        preg_match('/nonce-([A-Za-z0-9+\/=]+)/', $resposta->headers->get('Content-Security-Policy'), $m);
        $nonce = $m[1] ?? '';

        $this->assertNotEmpty($nonce);
        // O script injetado (Livewire/Vite) deve trazer o mesmo nonce do header.
        $this->assertStringContainsString('nonce="'.$nonce.'"', $resposta->getContent());
    }

    public function test_pagina_autenticada_nao_pode_ser_cacheada(): void
    {
        $user = User::factory()->create(['ativo' => true]);

        $resposta = $this->actingAs($user)->get('/profile');

        $cacheControl = $resposta->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('private', $cacheControl);
    }
}
