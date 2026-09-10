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
        .section { margin-bottom: 20px; }
        .section h2 { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #bd3a26; border-bottom: 1px solid #eee; padding-bottom: 4px; margin-bottom: 8px; }
        .content { background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 3px; padding: 10px; min-height: 40px; white-space: pre-wrap; line-height: 1.6; }
        .empty { color: #999; font-style: italic; }
        .footer { margin-top: 30px; border-top: 1px solid #eee; padding-top: 8px; font-size: 9px; color: #999; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Ficha Médica</h1>
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

    <div class="section">
        <h2>Diagnósticos</h2>
        <div class="content">
            @if ($ficha?->diagnosticos)
                {{ $ficha->diagnosticos }}
            @else
                <span class="empty">Nenhum diagnóstico registrado.</span>
            @endif
        </div>
    </div>

    <div class="section">
        <h2>Medicamentos Crônicos</h2>
        <div class="content">
            @if ($ficha?->medicamentos_cronicos)
                {{ $ficha->medicamentos_cronicos }}
            @else
                <span class="empty">Nenhum medicamento crônico registrado.</span>
            @endif
        </div>
    </div>

    <div class="section">
        <h2>Laudos e Exames</h2>
        <div class="content">
            @if ($ficha?->laudos)
                {{ $ficha->laudos }}
            @else
                <span class="empty">Nenhum laudo registrado.</span>
            @endif
        </div>
    </div>

    @if ($ficha?->atualizadoPor)
    <p style="font-size: 9px; color: #888;">Última atualização por {{ $ficha->atualizadoPor->name }} em {{ $ficha->updated_at->format('d/m/Y H:i') }}</p>
    @endif

    <div class="footer">Documento gerado pelo InamexControl</div>
</body>
</html>
