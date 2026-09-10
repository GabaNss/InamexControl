<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatorio de Registros de Medicacao</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 16px; margin-bottom: 0; }
        p.periodo { color: #555; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>InamexControl</h1>
    <p class="periodo">
        Relatorio de registros de medicacao de {{ $inicio->format('d/m/Y') }} a {{ $fim->format('d/m/Y') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Medicamento</th>
                <th>Dose</th>
                <th>Data</th>
                <th>Turno</th>
                <th>Administrado</th>
                <th>Observacao</th>
                <th>Responsavel</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($registros as $registro)
                <tr>
                    <td>{{ $registro->prescricao->paciente->nome }}</td>
                    <td>{{ $registro->prescricao->medicamento->nome }}</td>
                    <td>{{ $registro->prescricao->dose }}</td>
                    <td>{{ $registro->data->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($registro->turno) }}</td>
                    <td>{{ $registro->administrado ? 'Sim' : 'Nao' }}</td>
                    <td>{{ $registro->observacao }}</td>
                    <td>{{ $registro->usuario?->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
