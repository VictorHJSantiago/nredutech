<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório NREduTech</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; }
        @page { margin: 25px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 18px; color: #0169b4; margin: 0; }
        .header p { font-size: 10px; color: #777; margin: 0; }
        
        .kpi-section { margin-bottom: 20px; page-break-inside: avoid; } 
        .kpi-section h2 { font-size: 14px; color: #333; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        .kpi-table, .chart-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .kpi-table td, .chart-table th, .chart-table td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        .kpi-table td.label { font-weight: bold; width: 60%; }
        .kpi-table td.value { text-align: right; width: 40%; }
        .chart-table th { background-color: #f4f4f4; font-size: 10px; padding: 5px; }

        .report-section { margin-bottom: 25px; page-break-inside: avoid; }
        .report-section h2 { font-size: 16px; color: #0169b4; margin-bottom: 10px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 5px; text-align: left; word-wrap: break-word; }
        .data-table th { background-color: #f4f4f4; font-size: 9px; text-transform: uppercase; }
        .data-table tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Dados – NREduTech</h1>
        <p>Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @if(isset($stats) && !empty(array_filter($stats)))
    <section class="kpi-section">
        <h2>Resumo de Indicadores (KPIs)</h2>
        <table class="kpi-table">
            @foreach($stats as $key => $value)
            <tr>
                @php
                    $formattedKey = preg_replace('/(?<=\\w)(?=[A-Z])/', " $1", Str::ucfirst(Str::camel($key)));
                    $formattedKey = str_replace('Total ', 'Total de ', $formattedKey);
                @endphp
                <td class="label">{{ $formattedKey }}</td>
                <td class="value">{{ $value }}</td>
            </tr>
            @endforeach
        </table>
    </section>
    @endif

    @if(isset($chartData) && !empty(array_filter($chartData, fn($c) => $c->isNotEmpty())))
    <section class="kpi-section">
        <h2>Dados dos Gráficos</h2>
        @foreach($chartData as $chartKey => $data)
            @if($data->isNotEmpty())
                @php
                    $title = match($chartKey) {
                        'recursosPorStatus' => 'Recursos por Status',
                        'usuariosPorTipo' => 'Usuários por Tipo',
                        'usuariosPorMunicipio' => 'Usuários por Localização',
                        'turmasPorTurno' => 'Turmas por Turno',
                        'componentesPorStatus' => 'Disciplinas por Status',
                        default => Str::ucfirst($chartKey)
                    };
                @endphp
                <table class="chart-table">
                    <thead>
                        <tr><th colspan="2">{{ $title }}</th></tr>
                        <tr>
                            <th>Categoria</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $value)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ $value }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    </section>
    @endif

    @if(isset($reports) && !empty($reports))
        @foreach($reports as $report)
        <section class="report-section" style="page-break-before: auto;">
            <h2>Relatório de {{ $report['title'] }}</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        @foreach($report['columns'] as $column)
                        <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['data'] as $row)
                    <tr>
                        @foreach($report['columns'] as $key => $column)
                        <td>{{ data_get($row, $key) }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        @endforeach
    @endif

</body>
</html>