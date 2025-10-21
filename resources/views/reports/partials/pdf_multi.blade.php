<!DOCTYPE html>
<html>
<head>
    <title>Relatório Completo NREduTech</title>
    <style>
        @page { margin: 20px 25px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8pt; }
        .report-block { page-break-after: always; margin-bottom: 25px; }
        .report-block:last-child { page-break-after: avoid; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; word-wrap: break-word; vertical-align: top; }
        th { background-color: #e9ecef; font-weight: bold; }
        h2 { text-align: center; margin-bottom: 15px; color: #0169b4; font-size: 14pt; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .no-data { text-align: center; font-style: italic; color: #666; padding: 20px; }
    </style>
</head>
<body>
    @forelse($reports as $type => $report)
        <div class="report-block">
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
        </div>
    @empty
        <h2>Relatório Completo</h2>
        <p class="no-data">Nenhum dado encontrado para gerar o relatório com os filtros aplicados.</p>
    @endforelse
</body>
</html>