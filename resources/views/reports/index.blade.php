@extends('layouts.app')

@section('title', 'Relatórios – NREduTech')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/js/reports.js')
@endpush

@section('content')
    <header class="header-section">
        <h1>Central de Relatórios e Análises</h1>
        <p class="subtitle">
            Filtre os dados do sistema, visualize em gráficos e exporte em múltiplos formatos.
        </p>
    </header>

    <section class="filter-form">
        <form action="{{ route('reports.index') }}" method="GET">
            @if(Auth::user()->tipo_usuario === 'administrador')
            <div class="form-group">
                <label for="id_municipio">Município:</label>
                <select id="id_municipio" name="id_municipio">
                    <option value="">Todos</option>
                    @foreach ($municipios as $municipio)
                        <option value="{{ $municipio->id_municipio }}" {{ request('id_municipio') == $municipio->id_municipio ? 'selected' : '' }}>
                            {{ $municipio->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="id_escola">Instituição:</label>
                <select id="id_escola" name="id_escola">
                    <option value="">Todas</option>
                     @foreach ($escolas as $escola)
                        <option value="{{ $escola->id_escola }}" {{ request('id_escola') == $escola->id_escola ? 'selected' : '' }}>
                            {{ $escola->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="form-group">
                <label for="nivel_ensino">Nível de Ensino</label>
                <select id="nivel_ensino" name="nivel_ensino">
                    <option value="">Todos</option>
                    <option value="colegio_estadual" {{ request('nivel_ensino') == 'colegio_estadual' ? 'selected' : '' }}>Colégio Estadual</option>
                    <option value="escola_tecnica" {{ request('nivel_ensino') == 'escola_tecnica' ? 'selected' : '' }}>Escola Técnica</option>
                    <option value="escola_municipal" {{ request('nivel_ensino') == 'escola_municipal' ? 'selected' : '' }}>Escola Municipal</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tipo_escola">Localização</label>
                <select id="tipo_escola" name="tipo_escola">
                    <option value="">Ambas</option>
                    <option value="urbana" {{ request('tipo_escola') == 'urbana' ? 'selected' : '' }}>Urbana</option>
                    <option value="rural" {{ request('tipo_escola') == 'rural' ? 'selected' : '' }}>Rural</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="report_type">Tipo de Relatório:</label>
                <select id="report_type" name="report_type" required>
                    <option value="all" {{ request('report_type', 'all') == 'all' ? 'selected' : '' }}>Completo (Todos os Dados)</option>
                    <option value="usuarios" {{ request('report_type') == 'usuarios' ? 'selected' : '' }}>Usuários</option>
                    <option value="recursos" {{ request('report_type') == 'recursos' ? 'selected' : '' }}>Recursos Didáticos</option>
                    <option value="componentes" {{ request('report_type') == 'componentes' ? 'selected' : '' }}>Disciplinas</option>
                    <option value="turmas" {{ request('report_type') == 'turmas' ? 'selected' : '' }}>Turmas</option>
                    <option value="agendamentos" {{ request('report_type') == 'agendamentos' ? 'selected' : '' }}>Agendamentos</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-gerar">📄 Gerar Relatório</button>
            </div>
        </form>
    </section>

 @if(isset($reportData) || isset($chartData))
        <section class="kpi-cards-section">
            <div class="kpi-card">
                <div class="value">{{ $stats['totalUsuarios'] ?? 'N/A' }}</div>
                <div class="label">Usuários Cadastrados</div>
            </div>
            <div class="kpi-card">
                <div class="value">{{ $stats['totalRecursos'] ?? 'N/A' }}</div>
                <div class="label">Recursos Disponíveis</div>
            </div>
            @if(Auth::user()->tipo_usuario === 'administrador')
            <div class="kpi-card">
                <div class="value">{{ $stats['totalEscolas'] ?? 'N/A' }}</div>
                <div class="label">Escolas Gerenciadas</div>
            </div>
            <div class="kpi-card">
                <div class="value">{{ $stats['totalAgendamentos'] ?? 'N/A' }}</div>
                <div class="label">Agendamentos Futuros</div>
            </div>
            @endif
        </section>

        <section class="charts-section">
            <div class="chart-container">
                <h3>Recursos por Status</h3>
                <canvas id="recursosStatusChart"></canvas>
            </div>
            @if(Auth::user()->tipo_usuario === 'administrador' && !empty($chartData['usuariosPorMunicipio']))
            <div class="chart-container">
                <h3>Usuários por Município</h3>
                <canvas id="usuariosMunicipioChart"></canvas>
            </div>
            @endif
             <div class="chart-container">
                <h3>Usuários por Tipo</h3>
                <canvas id="usuariosTipoChart"></canvas>
            </div>
        </section>

        <section class="relatorio-preview">
            @include('reports.partials.preview', ['reportData' => $reportData, 'columns' => $columns, 'inputs' => $inputs, 'sortBy' => $sortBy ?? '', 'order' => $order ?? ''])
        </section>
        
    @else
        <section class="relatorio-preview">
            <p><strong>A pré-visualização dos dados aparecerá aqui após a geração.</strong></p>
        </section>
    @endif

    @if(isset($chartData))
        <div id="chart-data" 
             data-recursos-status='@json($chartData["recursosPorStatus"])'
             data-usuarios-tipo='@json($chartData["usuariosPorTipo"])'
             @if(Auth::user()->tipo_usuario === 'administrador')
             data-usuarios-municipio='@json($chartData["usuariosPorMunicipio"])'
             @endif
             style="display: none;">
        </div>
    @endif
@endsection