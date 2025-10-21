<div class="report-container">
    @php
        function sort_link($coluna, $titulo, $sortBy, $order, $type = null) {
            $queryParams = request()->query();
            $pageKey = $type ? $type . '_page' : 'page'; 
            unset($queryParams[$pageKey], $queryParams['page']);

            $newOrder = ($sortBy == $coluna && $order == 'asc') ? 'desc' : 'asc';
            $iconClass = $sortBy == $coluna ? ($order == 'asc' ? 'fa-arrow-up-short-wide' : 'fa-arrow-down-wide-short') : 'fa-sort';

            $urlParams = array_merge($queryParams, ['sort_by' => $coluna, 'order' => $newOrder]);
            $url = request()->url() . '?' . http_build_query($urlParams);
            $activeClass = $sortBy == $coluna ? 'active' : ''; 
            return "<th><a href=\"$url\" class=\"$activeClass\">$titulo <i class=\"fas $iconClass sort-icon\"></i></a></th>";
        }
        $reportTypeLabel = isset($selectedReportType) ? Str::ucfirst(str_replace('_', ' ', $selectedReportType)) : 'Geral';
    @endphp

    <div class="report-header">
        @if(isset($selectedReportType) && $selectedReportType)
            <h3>Resultado do Relatório: Tabela de {{ $reportTypeLabel }}</h3>
        @else
            <h3>Resultados Gerais</h3>
        @endif

        <div class="download-dropdown">
            <button onclick="toggleDropdown()" class="btn-download-toggle">
                <i class="fas fa-download"></i> Baixar Relatório Completo
            </button>
            <div id="downloadDropdownMenu" class="dropdown-content">
                <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'xlsx'])) }}"><i class="fas fa-file-excel"></i> Excel (XLSX)</a>
                <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'pdf'])) }}"><i class="fas fa-file-pdf"></i> PDF (ZIP)</a>
                <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'csv'])) }}"><i class="fas fa-file-csv"></i> CSV (ZIP)</a>
                <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'ods'])) }}"><i class="fas fa-file-alt"></i> ODS (ZIP)</a>
                <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'html'])) }}"><i class="fas fa-file-code"></i> HTML (ZIP)</a>
            </div>
        </div>
    </div>

    @if(isset($selectedReportType) && $selectedReportType && isset($reportData) && $reportData instanceof \Illuminate\Pagination\LengthAwarePaginator && $reportData->isNotEmpty())
        <div class="table-responsive-wrapper"> 
            <table class="data-table">
                <thead>
                   <tr>
                        @foreach($columns as $key => $value)
                            {!! sort_link($key, $value, $sortBy ?? '', $order ?? 'asc', $selectedReportType) !!}
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $row)
                        <tr>
                            @foreach($columns as $key => $value)
                                <td>{!! nl2br(e(data_get($row, $key))) !!}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> 
        @if ($reportData->hasPages())
            <div class="pagination-container">
                {{ $reportData->appends(request()->except('page'))->links() }}
            </div>
        @endif
    @elseif(isset($selectedReportType) && $selectedReportType)
         <p>Nenhum dado encontrado para a tabela de <strong>{{ $reportTypeLabel }}</strong> com os filtros selecionados.</p>
    @endif
</div>