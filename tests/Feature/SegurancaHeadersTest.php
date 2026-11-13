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
        $this->assertStringContainsString("default-src 'self'", $resposta->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString("frame-ancestors 'none'", $resposta->headers->get('Content-Security-Policy'));
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
