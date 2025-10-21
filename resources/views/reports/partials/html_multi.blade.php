<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Completo NREduTech</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; line-height: 1.4; margin: 20px; }
        .report-block { margin-bottom: 30px; padding-top: 20px; border-top: 2px solid #0169b4; }
        .report-block:first-child { border-top: none; padding-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; border: 1px solid #ccc; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background-color: #e9ecef; font-weight: bold; }
        h2 { text-align: center; margin-bottom: 20px; color: #0169b4; font-size: 16pt; }
        thead { background-color: #f8f9fa; }
        .no-data { text-align: center; font-style: italic; color: #666; padding: 20px; }
        @media print {
            .report-block { page-break-after: always; }
            .report-block:last-child { page-break-after: avoid; }
            thead { display: table-header-group; }
            tr, td, th { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <h1>Relatório Completo NREduTech</h1>
    <p>Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>

    @forelse($reports as $type => $report)
        <section class="report-block">
            <h2>{{ $report['title'] ?? 'Relatório' }}</h2>

            @if(isset($report['data']) && isset($report['columns']))
                @if(!$report['data']->isEmpty() && !empty($report['columns']))
                    <table>
                        <thead>
                            <tr>
                                @foreach($report['columns'] as $label)
                                    <th>{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report['data'] as $row)
                                <tr>
                                    @foreach(array_keys($report['columns']) as $key)
                                        <td>{{ data_get($row, $key) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="no-data">Nenhum dado encontrado para este relatório com os filtros aplicados.</p>
                @endif
             @else
                 <p class="no-data">Erro ao carregar dados ou colunas para este relatório.</p>
            @endif
        </section>
    @empty
        <section class="report-block">
             <h2>Relatório Completo</h2>
             <p class="no-data">Nenhum dado encontrado para gerar o relatório com os filtros aplicados.</p>
        </section>
    @endforelse

</body>
</html>