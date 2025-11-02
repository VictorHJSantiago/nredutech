<div class="report-header">
    <h3>Resultados Gerais</h3>
    
    <div class="download-dropdown">
        <button class="btn-download-toggle" onclick="toggleDropdown()">
            <i class="fas fa-download"></i>
            Exportar Relatório
        </button>
        <div class="dropdown-content" id="downloadDropdownMenu">
            <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'pdf'])) }}" class="download-link">
                <i class="fas fa-file-pdf"></i> PDF (.pdf)
            </a>
            <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'xlsx'])) }}" class="download-link">
                <i class="fas fa-file-excel"></i> Excel (.xlsx)
            </a>
            <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'ods'])) }}" class="download-link">
                <i class="fas fa-file-alt"></i> OpenDocument (.ods)
            </a>
            <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'csv'])) }}" class="download-link">
                <i class="fas fa-file-csv"></i> CSV (.zip)
            </a>
            <a href="{{ route('reports.index', array_merge($inputs, ['format' => 'html'])) }}" class="download-link">
                <i class="fas fa-file-code"></i> HTML (.html)
            </a>
        </div>
    </div>
</div>

<div id="download-message" class="alert alert-info" style="display: none;">
    <i class="fas fa-spinner fa-spin"></i>
    Seu download está sendo preparado. Isso pode levar alguns minutos. Por favor, aguarde...
</div>

@if(isset($reportData) && $reportData->count() > 0)
    <div class="table-responsive-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    @foreach ($columns as $key => $column)
                        <th>
                            @php
                                $currentOrder = request('order', 'asc');
                                $nextOrder = $currentOrder === 'asc' ? 'desc' : 'asc';
                                $isActive = request('sort_by') == $key;
                            @endphp
                            <a href="{{ route('reports.index', array_merge($inputs, ['sort_by' => $key, 'order' => $nextOrder])) }}"
                               class="{{ $isActive ? 'active' : '' }}">
                                {{ $column }}
                                @if ($isActive)
                                    <i class="sort-icon fas {{ $currentOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                                @else
                                    <i class="sort-icon fas fa-sort" style="opacity: 0.3;"></i>
                                @endif
                            </a>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($reportData as $row)
                    <tr>
                        @foreach ($columns as $key => $column)
                            <td>
                                {{ data_get($row, $key) }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($reportData->hasPages())
        <div class="pagination-container">
            {{ $reportData->links() }}
        </div>
    @endif

@elseif($selectedReportType)
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Nenhum dado encontrado para o relatório de <strong>{{ $selectedReportType }}</strong> com os filtros aplicados.
    </div>
@else
    <div class="alert alert-secondary">
        <i class="fas fa-filter"></i>
        Selecione um tipo de relatório no filtro "Visualizar Tabela de" para ver os dados aqui.
    </div>
@endif