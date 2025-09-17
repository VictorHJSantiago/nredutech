<div class="report-container">
    @php
        function sort_link($coluna, $titulo, $sortBy, $order) {
            $newOrder = ($sortBy == $coluna && $order == 'asc') ? 'desc' : 'asc';
            $iconClass = $sortBy == $coluna 
                ? ($order == 'asc' ? 'fa-arrow-up-short-wide' : 'fa-arrow-down-wide-short')
                : 'fa-sort';
            
            $url = request()->fullUrlWithQuery([
                'sort_by' => $coluna,
                'order' => $newOrder
            ]);

            $activeClass = $sortBy == $coluna ? 'active' : '';

            return "<th><a href=\"$url\" class=\"$activeClass\">$titulo <i class=\"fas $iconClass sort-icon\"></i></a></th>";
        }
    @endphp

    <div class="report-header">
        <h3>Resultado do Relatório</h3>
        @if(!is_array($reportData))
            <div class="download-buttons">
                <form action="{{ route('reports.index', array_merge($inputs, ['format' => 'pdf', 'sort_by' => $sortBy, 'order' => $order])) }}" method="GET" style="display: inline;">
                    <button type="submit" class="btn-download">Baixar PDF</button>
                </form>
                <form action="{{ route('reports.index', array_merge($inputs, ['format' => 'xlsx', 'sort_by' => $sortBy, 'order' => $order])) }}" method="GET" style="display: inline;">
                    <button type="submit" class="btn-download">Baixar XLSX</button>
                </form>
                <form action="{{ route('reports.index', array_merge($inputs, ['format' => 'csv', 'sort_by' => $sortBy, 'order' => $order])) }}" method="GET" style="display: inline;">
                    <button type="submit" class="btn-download">Baixar CSV</button>
                </form>
                <form action="{{ route('reports.index', array_merge($inputs, ['format' => 'ods', 'sort_by' => $sortBy, 'order' => $order])) }}" method="GET" style="display: inline;">
                    <button type="submit" class="btn-download">Baixar ODS</button>
                </form>
                 <form action="{{ route('reports.index', array_merge($inputs, ['format' => 'html', 'sort_by' => $sortBy, 'order' => $order])) }}" method="GET" style="display: inline;">
                    <button type="submit" class="btn-download">Baixar HTML</button>
                </form>
            </div>
        @else
            <div class="download-buttons">
                <p><i>O download para "Todos" os relatórios não está disponível. Por favor, gere um relatório específico para baixar.</i></p>
            </div>
        @endif
    </div>

    @if(is_array($reportData)) 
        @foreach($reportData as $type => $report)
            <div class="report-section">
                <h4>Relatório de {{ ucfirst($type) }}</h4>
                @if($report['data']->isEmpty())
                    <p>Nenhum dado encontrado para os filtros selecionados.</p>
                @else
                    <table class="data-table">
                        <thead>
                            <tr>
                                @foreach($report['columns'] as $key => $value)
                                    {!! sort_link($key, $value, $sortBy, $order) !!}
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report['data'] as $row)
                                <tr>
                                    @foreach($report['columns'] as $key => $value)
                                        <td>{{ data_get($row, $key) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination-container">
                        {{ $report['data']->links() }}
                    </div>
                @endif
            </div>
        @endforeach
    @elseif($reportData instanceof \Illuminate\Pagination\LengthAwarePaginator)
        @if($reportData->isEmpty())
            <p>Nenhum dado encontrado para os filtros selecionados.</p>
        @else
            <table class="data-table">
                 <thead>
                    <tr>
                        @foreach($columns as $key => $value)
                            {!! sort_link($key, $value, $sortBy, $order) !!}
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $row)
                        <tr>
                            @foreach($columns as $key => $value)
                                <td>{{ data_get($row, $key) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination-container">
                {{ $reportData->links() }}
            </div>
        @endif
    @endif
</div>