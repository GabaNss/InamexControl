<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Sistema fechado: visitante anonimo e redirecionado para o login,
     * nao ha landing page publica.
     */
    public function test_a_raiz_redireciona_visitante_para_o_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }
}
