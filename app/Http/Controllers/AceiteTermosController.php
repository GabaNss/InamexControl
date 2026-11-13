<?php

namespace App\Http\Controllers;

use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Exibe e registra o aceite do Termo de Uso/Confidencialidade (LGPD). O acesso
 * ao sistema fica bloqueado pelo middleware AceiteTermos ate que o usuario
 * aceite a versao vigente do termo.
 */
class AceiteTermosController extends Controller
{
    public function show(): View
    {
        return view('legal.aceite');
    }

    public function aceitar(Request $request, AuditoriaService $auditoria): RedirectResponse
    {
        $user = $request->user();

        $user->forceFill(['termos_aceitos_em' => now()])->save();

        $auditoria->registrar(
            usuario: $user,
            acao: 'termos.aceitos',
            dadosDepois: ['versao' => config('termos.versao')],
        );

        return redirect()->intended(route('dashboard'));
    }
}
