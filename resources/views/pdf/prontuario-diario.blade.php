<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; margin: 0; padding: 20px; }
        .header { border-bottom: 2px solid #bd3a26; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; color: #bd3a26; margin: 0 0 4px; }
        .header .sub { font-size: 11px; color: #555; }
        .meta { display: flex; gap: 40px; margin-bottom: 20px; font-size: 10px; color: #555; }
        .meta span { display: block; }
        .meta strong { color: #1a1a1a; font-size: 11px; }
        .dia { margin-bottom: 18px; page-break-inside: avoid; }
        .dia-header { background: #bd3a26; color: white; padding: 5px 10px; border-radius: 3px 3px 0 0; font-size: 11px; font-weight: bold; }
        .dia-content { background: #f9f9f9; border: 1px solid #e5e5e5; border-top: 0; border-radius: 0 0 3px 3px; padding: 10px; min-height: 30px; white-space: pre-wrap; line-height: 1.6; }
        .empty { color: #999; font-style: italic; }
        .no-data { text-align: center; color: #999; padding: 30px; font-style: italic; }
        .footer { margin-top: 30px; border-top: 1px solid #eee; padding-top: 8px; font-size: 9px; color: #999; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Prontuário Diário</h1>
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
        <div>
            <span>Período</span>
            <strong>{{ \Carbon\Carbon::parse($inicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($fim)->format('d/m/Y') }}</strong>
        </div>
        <div>
            <span>Gerado em</span>
            <strong>{{ now()->format('d/m/Y H:i') }}</strong>
        </div>
    </div>

    @forelse ($prontuarios as $prontuario)
        <div class="dia">
            <div class="dia-header">{{ $prontuario->data->format('d/m/Y') }} ({{ $prontuario->data->locale('pt_BR')->isoFormat('dddd') }})</div>
            <div class="dia-content">
                @if ($prontuario->conteudo)
                    {{ $prontuario->conteudo }}
                @else
                    <span class="empty">Sem registro para este dia.</span>
                @endif
            </div>
        </div>
    @empty
        <div class="no-data">Nenhum registro no período selecionado.</div>
    @endforelse

    <div class="footer">Documento gerado pelo InamexControl</div>
</body>
</html>
