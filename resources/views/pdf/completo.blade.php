<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; margin: 0; padding: 20px; }
        .header { border-bottom: 2px solid #bd3a26; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 20px; color: #bd3a26; margin: 0 0 4px; }
        .header .sub { font-size: 11px; color: #555; }
        .meta { display: flex; gap: 40px; margin-bottom: 24px; font-size: 10px; color: #555; }
        .meta span { display: block; }
        .meta strong { color: #1a1a1a; font-size: 11px; }
        .secao-titulo { font-size: 14px; font-weight: bold; color: #bd3a26; border-bottom: 2px solid #bd3a26; padding-bottom: 5px; margin: 24px 0 14px; page-break-before: auto; }
        .section { margin-bottom: 16px; }
        .section h3 { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #666; margin: 0 0 5px; }
        .content { background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 3px; padding: 10px; min-height: 30px; white-space: pre-wrap; line-height: 1.6; }
        .dia { margin-bottom: 14px; page-break-inside: avoid; }
        .dia-header { background: #bd3a26; color: white; padding: 4px 10px; border-radius: 3px 3px 0 0; font-size: 10px; font-weight: bold; }
        .dia-content { background: #f9f9f9; border: 1px solid #e5e5e5; border-top: 0; border-radius: 0 0 3px 3px; padding: 10px; white-space: pre-wrap; line-height: 1.6; }
        .empty { color: #999; font-style: italic; }
        .footer { margin-top: 30px; border-top: 1px solid #eee; padding-top: 8px; font-size: 9px; color: #999; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório Completo</h1>
        <div class="sub">INAMEX — Instituto de Amparo ao Excepcional</div>
    </div>

    <div class="meta">
        <div>
            <span>Interna</span>
            <strong>{{ $paciente->nome }}</strong>
        </div>
        <div>
            <span>Prontuário</span>
            <strong>{{ $paciente->prontuario }}</strong>
        </div>
        @if ($paciente->quarto)
        <div>
            <span>Quarto</span>
            <strong>{{ $paciente->quarto }}</strong>
        </div>
        @endif
        <div>
            <span>Gerado em</span>
            <strong>{{ now()->format('d/m/Y H:i') }}</strong>
        </div>
    </div>

    {{-- Ficha Médica --}}
    <div class="secao-titulo">Ficha Médica</div>

    <div class="section">
        <h3>Diagnósticos</h3>
        <div class="content">
            @if ($ficha?->diagnosticos)
                {{ $ficha->diagnosticos }}
            @else
                <span class="empty">Nenhum diagnóstico registrado.</span>
            @endif
        </div>
    </div>

    <div class="section">
        <h3>Medicamentos Crônicos</h3>
        <div class="content">
            @if ($ficha?->medicamentos_cronicos)
                {{ $ficha->medicamentos_cronicos }}
            @else
                <span class="empty">Nenhum medicamento crônico registrado.</span>
            @endif
        </div>
    </div>

    <div class="section">
        <h3>Laudos e Exames</h3>
        <div class="content">
            @if ($ficha?->laudos)
                {{ $ficha->laudos }}
            @else
                <span class="empty">Nenhum laudo registrado.</span>
            @endif
        </div>
    </div>

    {{-- Prontuário Histórico --}}
    <div class="secao-titulo">Prontuário Histórico</div>

    <div class="content">
        @if ($historico?->conteudo)
            {{ $historico->conteudo }}
        @else
            <span class="empty">Nenhum prontuário histórico registrado.</span>
        @endif
    </div>

    {{-- Prontuários Diários --}}
    <div class="secao-titulo">Prontuários Diários ({{ $prontuarios->count() }} registros)</div>

    @forelse ($prontuarios as $prontuario)
        <div class="dia">
            <div class="dia-header">{{ $prontuario->data->format('d/m/Y') }}</div>
            <div class="dia-content">
                @if ($prontuario->conteudo)
                    {{ $prontuario->conteudo }}
                @else
                    <span class="empty">Sem registro para este dia.</span>
                @endif
            </div>
        </div>
    @empty
        <p class="empty">Nenhum prontuário diário registrado.</p>
    @endforelse

    <div class="footer">Documento gerado pelo InamexControl</div>
</body>
</html>
