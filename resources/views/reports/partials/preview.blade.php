<div class="report-container">
    @php
        function sort_link($coluna, $titulo, $sortBy, $order, $type = null) {
            $queryParams = request()->query();
            
            if ($type) {
                $pageKey = $type . '_page';
                if(isset($queryParams[$pageKey])) {
                    unset($queryParams[$pageKey]);
                }
            }

            $newOrder = ($sortBy == $coluna && $order == 'asc') ? 'desc' : 'asc';
            $iconClass = $sortBy == $coluna 
                ? ($order == 'asc' ? 'fa-arrow-up-short-wide' : 'fa-arrow-down-wide-short')
                : 'fa-sort';
            
            $url = request()->url() . '?' . http_build_query(array_merge(
                $queryParams,
                ['sort_by' => $coluna, 'order' => $newOrder]
            ));

            $activeClass = $sortBy == $coluna ? 'active' : '';

            return "<th><a href=\"$url\" class=\"$activeClass\">$titulo <i class=\"fas $iconClass sort-icon\"></i></a></th>";
        }
    @endphp

    <div class="report-header">
        <h3>Resultado do Relatório</h3>
        @if(!empty($reportData))
            <div class="download-dropdown">
                <button onclick="toggleDropdown()" class="btn-download-toggle">
                    <i class="fas fa-download"></i> Baixar Relatório
                </button>
                <div id="downloadDropdownMenu" class="dropdown-content">
                    <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'pdf', 'sort_by' => $sortBy, 'order' => $order])) }}">
                        <i class="fas fa-file-pdf"></i> PDF {{ is_array($reportData) ? '(em .zip)' : '' }}
                    </a>
                    <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'xlsx', 'sort_by' => $sortBy, 'order' => $order])) }}">
                        <i class="fas fa-file-excel"></i> Excel (XLSX)
                    </a>
                    <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'csv', 'sort_by' => $sortBy, 'order' => $order])) }}">
                        <i class="fas fa-file-csv"></i> CSV {{ is_array($reportData) ? '(em .zip)' : '' }}
                    </a>
                    <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'ods', 'sort_by' => $sortBy, 'order' => $order])) }}">
                        <i class="fas fa-file-alt"></i> OpenDocument (ODS) {{ is_array($reportData) ? '(em .zip)' : '' }}
                    </a>
                    <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'html', 'sort_by' => $sortBy, 'order' => $order])) }}">
                        <i class="fas fa-file-code"></i> HTML {{ is_array($reportData) ? '(em .zip)' : '' }}
                    </a>
                </div>
            </div>
        @endif
    </div>

    @if(is_array($reportData)) 
        @foreach($reportData as $type => $report)
            @if($report['data']->isNotEmpty())
                <div class="report-section" id="report-section-{{$type}}">
                    <h4>Relatório de {{ \Illuminate\Support\Str::of(str_replace('_', ' ', $type))->ucfirst() }}</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                @foreach($report['columns'] as $key => $value)
                                    {!! sort_link($key, $value, $sortBy, $order, $type) !!}
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
                </div>
            @endif
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