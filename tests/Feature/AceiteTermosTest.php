<?php

namespace Tests\Feature;

use App\Models\LogAuditoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fluxo de aceite do termo de uso/confidencialidade (LGPD).
 */
class AceiteTermosTest extends TestCase
{
    use RefreshDatabase;

    public function test_paginas_legais_sao_publicas(): void
    {
        $this->get(route('privacidade'))->assertOk();
        $this->get(route('termos'))->assertOk();
    }

    public function test_usuario_sem_aceite_e_redirecionado(): void
    {
        $user = User::factory()->create(['ativo' => true, 'termos_aceitos_em' => null]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('termos.aceite'));
    }

    public function test_aceitar_grava_data_audita_e_libera_acesso(): void
    {
        $user = User::factory()->create(['ativo' => true, 'termos_aceitos_em' => null]);

        $this->actingAs($user)
            ->post(route('termos.aceitar'))
            ->assertRedirect(route('dashboard'));

        $this->assertNotNull($user->fresh()->termos_aceitos_em);
        $this->assertDatabaseHas('logs_auditoria', ['acao' => 'termos.aceitos']);

        // Agora o dashboard abre normalmente.
        $this->actingAs($user->fresh())->get('/dashboard')->assertOk();
    }

    public function test_usuario_que_ja_aceitou_versao_vigente_nao_e_redirecionado(): void
    {
        $user = User::factory()->create([
            'ativo' => true,
            'termos_aceitos_em' => now(),
        ]);

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_aceite_antigo_antes_da_versao_vigente_reexige(): void
    {
        $user = User::factory()->create([
            'ativo' => true,
            // Aceitou antes da publicacao da versao vigente (config termos.publicado_em).
            'termos_aceitos_em' => config('termos.publicado_em')->copy()->subDay(),
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('termos.aceite'));
    }
}
