<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório NREduTech</title>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.5; background-color: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0169b4; padding-bottom: 20px; }
        .header h1 { font-size: 28px; color: #0169b4; margin: 0; }
        .header p { font-size: 14px; color: #777; margin: 0; }
        
        .kpi-section { margin-bottom: 30px; }
        .kpi-section h2 { font-size: 20px; color: #333; border-bottom: 1px solid #ccc; padding-bottom: 8px; margin-bottom: 15px; }
        .kpi-table, .chart-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .kpi-table td, .chart-table th, .chart-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .kpi-table td.label { font-weight: bold; width: 60%; }
        .kpi-table td.value { text-align: right; width: 40%; font-weight: 500; font-size: 1.1em; }
        .chart-table th { background-color: #f4f4f4; font-size: 12px; text-transform: uppercase; padding: 8px 10px; }

        .report-section { margin-bottom: 30px; }
        .report-section h2 { font-size: 22px; color: #0169b4; margin-bottom: 15px; border-bottom: 1px solid #ccc; padding-bottom: 8px; }
        .table-wrapper { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px 12px; text-align: left; vertical-align: top; }
        .data-table th { background-color: #f4f4f4; font-size: 12px; text-transform: uppercase; white-space: nowrap; }
        .data-table tr:nth-child(even) { background-color: #f9f9f9; }
        .data-table tr:hover { background-color: #f1f1f1; }

        .charts-section-export {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .chart-container-export {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
        }
        .chart-container-export h3 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 16px;
            color: #333;
        }
        .chart-canvas-container-export {
            position: relative;
            height: 300px; 
        }
    </style>
</head>
<body>
    <div class="container">
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
        <section classs="kpi-section">
            <h2>Gráficos Visuais</h2>
            <div class="charts-section-export">
                
                @if(!empty($chartData['recursosPorStatus']) && $chartData['recursosPorStatus']->isNotEmpty())
                <div class="chart-container-export">
                    <h3>Recursos por Status</h3>
                    <div class="chart-canvas-container-export"><canvas id="recursosStatusChart"></canvas></div>
                </div>
                @endif
                
                @if(!empty($chartData['usuariosPorMunicipio']) && $chartData['usuariosPorMunicipio']->isNotEmpty())
                <div class="chart-container-export">
                    <h3>Usuários por Localização</h3>
                    <div class="chart-canvas-container-export"><canvas id="usuariosMunicipioChart"></canvas></div>
                </div>
                @endif

                @if(!empty($chartData['usuariosPorTipo']) && $chartData['usuariosPorTipo']->isNotEmpty())
                <div class="chart-container-export">
                    <h3>Usuários por Tipo</h3>
                    <div class="chart-canvas-container-export"><canvas id="usuariosTipoChart"></canvas></div>
                </div>
                 @endif

                @if(!empty($chartData['turmasPorTurno']) && $chartData['turmasPorTurno']->isNotEmpty())
                 <div class="chart-container-export">
                    <h3>Turmas por Turno</h3>
                    <div class="chart-canvas-container-export"><canvas id="turmasTurnoChart"></canvas></div>
                </div>
                @endif

                @if(!empty($chartData['componentesPorStatus']) && $chartData['componentesPorStatus']->isNotEmpty())
                 <div class="chart-container-export">
                    <h3>Disciplinas por Status</h3>
                    <div class="chart-canvas-container-export"><canvas id="componentesStatusChart"></canvas></div>
                </div>
                @endif
            </div>
        </section>
        @endif

        @if(isset($reports) && !empty($reports))
            @foreach($reports as $report)
            <section class="report-section">
                <h2>Relatório de {{ $report['title'] }}</h2>
                <div class="table-wrapper">
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
                </div>
            </section>
            @endforeach
        @endif
    </div>

    <script>
        const chartData = @json($chartData ?? []);

        function renderChart(canvasId, chartType, chartData, chartOptions) {
            const ctx = document.getElementById(canvasId);
            if (!ctx) return;
            
            const labels = chartData.labels || [];
            const dataValues = chartData.datasets?.[0]?.data || [];
            const hasValidData = labels.length > 0 && dataValues.length > 0 && dataValues.some(v => v > 0);

            if (hasValidData) {
                new Chart(ctx, {
                    type: chartType,
                    data: chartData,
                    options: chartOptions
                });
            } else {
                 if(ctx.closest('.chart-container-export')) {
                    ctx.closest('.chart-container-export').style.display = 'none';
                 }
            }
        }

        function renderRecursosChart(data) {
            if(!data) return;
            const labels = Object.keys(data).map(status => {
                switch(status) {
                    case 'funcionando': return 'Funcionando';
                    case 'em_manutencao': return 'Em Manutenção';
                    case 'quebrado': return 'Quebrado';
                    case 'descartado': return 'Descartado';
                    default: return status.charAt(0).toUpperCase() + status.slice(1);
                }
            });
            const values = Object.values(data);
            renderChart('recursosStatusChart', 'doughnut', {
                labels: labels,
                datasets: [{ data: values, backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d'] }]
            }, { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } });
        }

        function renderUsuariosMunicipioChart(data) {
            if(!data) return;
            const labels = Object.keys(data);
            const values = Object.values(data);
            renderChart('usuariosMunicipioChart', 'bar', {
                labels: labels,
                datasets: [{ label: 'Total de Usuários', data: values, backgroundColor: 'rgba(1, 105, 180, 0.7)' }]
            }, { indexAxis: 'y', responsive: true, maintainAspectRatio: false, scales: { x: { beginAtZero: true } }, plugins: { legend: { display: false } } });
        }

        function renderUsuariosTipoChart(data) {
            if(!data) return;
            const labels = Object.keys(data).map(tipo => tipo.charAt(0).toUpperCase() + tipo.slice(1));
            const values = Object.values(data);
            renderChart('usuariosTipoChart', 'pie', {
                labels: labels,
                datasets: [{ data: values, backgroundColor: ['#0169b4', '#5fb13b', '#ffc107'] }]
            }, { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } });
        }
        
        function renderTurmasTurnoChart(data) {
            if(!data) return;
            const labels = Object.keys(data).map(t => t.charAt(0).toUpperCase() + t.slice(1));
            const values = Object.values(data);
            renderChart('turmasTurnoChart', 'pie', {
                labels: labels,
                datasets: [{ data: values, backgroundColor: ['#ffc107', '#17a2b8', '#6f42c1'] }]
            }, { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } });
        }

        function renderComponentesStatusChart(data) {
             if(!data) return;
             const labels = Object.keys(data).map(s => {
                switch(s) {
                    case 'aprovado': return 'Aprovado';
                    case 'pendente': return 'Pendente';
                    case 'reprovado': return 'Reprovado';
                    default: return s.charAt(0).toUpperCase() + s.slice(1);
                }
            });
            const values = Object.values(data);
            renderChart('componentesStatusChart', 'doughnut', {
                labels: labels,
                datasets: [{ data: values, backgroundColor: ['#28a745', '#ffc107', '#dc3545'] }]
            }, { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } });
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (chartData.recursosPorStatus) renderRecursosChart(chartData.recursosPorStatus);
            if (chartData.usuariosPorMunicipio) renderUsuariosMunicipioChart(chartData.usuariosPorMunicipio);
            if (chartData.usuariosPorTipo) renderUsuariosTipoChart(chartData.usuariosPorTipo);
            if (chartData.turmasPorTurno) renderTurmasTurnoChart(chartData.turmasPorTurno);
            if (chartData.componentesPorStatus) renderComponentesStatusChart(chartData.componentesPorStatus);
        });
    </script>
</body>
</html>