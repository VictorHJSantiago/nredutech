@extends('layouts.app')

@section('title', 'Relatórios – NREduTech')

@push('scripts')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <header class="header-section">
        <h1>Central de Relatórios e Análises</h1>
        <p class="subtitle">
            Explore dados do sistema com filtros avançados, visualize insights em gráficos dinâmicos e exporte nos formatos que precisar.
        </p>
    </header>

    @if (session('success'))
        <div class="alert alert-success" style="max-width: 1200px; margin: 0 auto 1.5rem;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" style="max-width: 1200px; margin: 0 auto 1.5rem;">{{ session('error') }}</div>
    @endif

    <section class="filter-form-container">
        <form action="{{ route('reports.index') }}" method="GET">
            @if(Auth::user()->tipo_usuario === 'administrador')
                <div class="form-group">
                    <label for="id_municipio">Município</label>
                    <select id="id_municipio" name="id_municipio">
                        <option value="">Todos</option>
                        @foreach ($municipios as $municipio)
                            <option value="{{ $municipio->id_municipio }}" @selected(request('id_municipio') == $municipio->id_municipio)>{{ $municipio->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_escola">Instituição</label>
                    <select id="id_escola" name="id_escola">
                        <option value="">Todas</option>
                        @foreach ($escolas as $escola)
                            <option value="{{ $escola->id_escola }}" @selected(request('id_escola') == $escola->id_escola)>
                                {{ $escola->nome }} ({{ $escola->municipio->nome ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="nivel_ensino">Nível Ensino (Escola)</label>
                    <select id="nivel_ensino" name="nivel_ensino">
                        <option value="">Todos</option>
                        <option value="colegio_estadual" @selected(request('nivel_ensino') == 'colegio_estadual')>Colégio Estadual</option>
                        <option value="escola_tecnica" @selected(request('nivel_ensino') == 'escola_tecnica')>Escola Técnica</option>
                        <option value="escola_municipal" @selected(request('nivel_ensino') == 'escola_municipal')>Escola Municipal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tipo_escola">Localização (Escola)</label>
                    <select id="tipo_escola" name="tipo_escola">
                        <option value="">Ambas</option>
                        <option value="urbana" @selected(request('tipo_escola') == 'urbana')>Urbana</option>
                        <option value="rural" @selected(request('tipo_escola') == 'rural')>Rural</option>
                    </select>
                </div>
            @endif

            <div class="form-group">
                <label for="user_type">Tipo de Usuário</label>
                <select id="user_type" name="user_type">
                    <option value="">Todos</option>
                    @if(Auth::user()->tipo_usuario === 'administrador')
                        <option value="administrador" @selected(request('user_type') == 'administrador')>Administrador</option>
                    @endif
                    <option value="diretor" @selected(request('user_type') == 'diretor')>Diretor</option>
                    <option value="professor" @selected(request('user_type') == 'professor')>Professor</option>
                </select>
            </div>
            <div class="form-group">
                <label for="resource_status">Status do Recurso</label>
                <select id="resource_status" name="resource_status">
                    <option value="">Todos</option>
                    <option value="funcionando" @selected(request('resource_status') == 'funcionando')>Funcionando</option>
                    <option value="em_manutencao" @selected(request('resource_status') == 'em_manutencao')>Em Manutenção</option>
                    <option value="quebrado" @selected(request('resource_status') == 'quebrado')>Quebrado</option>
                    <option value="descartado" @selected(request('resource_status') == 'descartado')>Descartado</option>
                </select>
            </div>
            <div class="form-group">
                <label for="start_date">Data Início (Agend.)</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="form-group">
                <label for="end_date">Data Fim (Agend.)</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
            </div>

            <div class="form-group">
                <label for="report_type">Visualizar Tabela de:</label>
                <select id="report_type" name="report_type">
                    <option value="" @selected(!$selectedReportType)>Nenhuma (Apenas Dashboards)</option>
                    <option value="usuarios" @selected($selectedReportType == 'usuarios')>Usuários</option>
                    @if(Auth::user()->tipo_usuario === 'administrador')
                        <option value="escolas" @selected($selectedReportType == 'escolas')>Escolas</option>
                    @endif
                    <option value="turmas" @selected($selectedReportType == 'turmas')>Turmas</option>
                    <option value="componentes" @selected($selectedReportType == 'componentes')>Disciplinas</option>
                    <option value="recursos" @selected($selectedReportType == 'recursos')>Recursos</option>
                    <option value="agendamentos" @selected($selectedReportType == 'agendamentos')>Agendamentos</option>
                </select>
            </div>

            @if(request('sort_by')) <input type="hidden" name="sort_by" value="{{ request('sort_by') }}"> @endif
            @if(request('order')) <input type="hidden" name="order" value="{{ request('order') }}"> @endif

            <div class="form-actions">
                <button type="submit" class="btn-gerar">
                    <i class="fas fa-filter"></i> Aplicar Filtros e Gerar
                </button>
            </div>
        </form>
    </section>

    @if(isset($stats))
        <section class="kpi-cards-section">
            <div class="kpi-card">
                <div class="value">{{ $stats['totalUsuarios'] ?? '0' }}</div>
                <div class="label">Usuários <small>(nos filtros)</small></div>
            </div>
            @if(Auth::user()->tipo_usuario === 'administrador')
                <div class="kpi-card">
                    <div class="value">{{ $stats['totalEscolas'] ?? '0' }}</div>
                    <div class="label">Escolas <small>(nos filtros)</small></div>
                </div>
            @endif
            <div class="kpi-card">
                <div class="value">{{ $stats['totalTurmas'] ?? '0' }}</div>
                <div class="label">Turmas <small>(nos filtros)</small></div>
            </div>
            <div class="kpi-card">
                <div class="value">{{ $stats['totalComponentes'] ?? '0' }}</div>
                <div class="label">Disciplinas <small>(nos filtros)</small></div>
            </div>
            <div class="kpi-card">
                <div class="value">{{ $stats['totalRecursos'] ?? '0' }}</div>
                <div class="label">Recursos <small>(nos filtros)</small></div>
            </div>
            <div class="kpi-card">
                <div class="value">{{ $stats['totalAgendamentos'] ?? '0' }}</div>
                <div class="label">Agendamentos <small>(nos filtros)</small></div>
            </div>
        </section>

        @if(isset($chartData) && !empty($chartData))
            <section class="charts-section">
                @if(!empty($chartData['recursosPorStatus']) && $chartData['recursosPorStatus']->isNotEmpty())
                <div class="chart-container">
                    <h3>Recursos por Status</h3>
                    <div class="chart-canvas-container"><canvas id="recursosStatusChart"></canvas></div>
                </div>
                @endif
                @if(Auth::user()->tipo_usuario === 'administrador' && !empty($chartData['usuariosPorMunicipio']) && $chartData['usuariosPorMunicipio']->isNotEmpty())
                <div class="chart-container">
                    <h3>Usuários por Localização</h3>
                    <div class="chart-canvas-container"><canvas id="usuariosMunicipioChart"></canvas></div>
                </div>
                @endif
                @if(!empty($chartData['usuariosPorTipo']) && $chartData['usuariosPorTipo']->isNotEmpty())
                <div class="chart-container">
                    <h3>Usuários por Tipo</h3>
                    <div class="chart-canvas-container"><canvas id="usuariosTipoChart"></canvas></div>
                </div>
                 @endif
                @if(!empty($chartData['turmasPorTurno']) && $chartData['turmasPorTurno']->isNotEmpty())
                 <div class="chart-container">
                    <h3>Turmas por Turno</h3>
                    <div class="chart-canvas-container"><canvas id="turmasTurnoChart"></canvas></div>
                </div>
                @endif
                @if(!empty($chartData['componentesPorStatus']) && $chartData['componentesPorStatus']->isNotEmpty())
                 <div class="chart-container">
                    <h3>Disciplinas por Status</h3>
                    <div class="chart-canvas-container"><canvas id="componentesStatusChart"></canvas></div>
                </div>
                @endif
            </section>
        @endif

        <section class="relatorio-preview">
            @include('reports.partials.preview', [
                'reportData' => $reportData ?? null,
                'columns' => $columns ?? [],         
                'inputs' => $inputs,                
                'sortBy' => $sortBy ?? '',          
                'order' => $order ?? 'asc',          
                'selectedReportType' => $selectedReportType 
            ])
        </section>

        @if(isset($chartData) && !empty($chartData))
            <div id="chart-data"
                 data-recursos-status='@json($chartData["recursosPorStatus"] ?? [])'
                 data-usuarios-tipo='@json($chartData["usuariosPorTipo"] ?? [])'
                 data-usuarios-municipio='@json($chartData["usuariosPorMunicipio"] ?? [])'
                 data-turmas-turno='@json($chartData["turmasPorTurno"] ?? [])'
                 data-componentes-status='@json($chartData["componentesPorStatus"] ?? [])'
                 style="display: none;">
            </div>
        @endif

    @else
        <section class="relatorio-preview" style="text-align: center;">
            <p style="font-size: 1.1rem; color: var(--texto-secundario);">
                <i class="fas fa-info-circle" style="color: var(--azul-principal); margin-right: 5px;"></i>
                Selecione os filtros e clique em "Aplicar Filtros e Gerar" para visualizar os dados.
            </p>
             @include('reports.partials.preview', [
                'reportData' => null, 'columns' => [], 'inputs' => request()->except('page'),
                'sortBy' => '', 'order' => 'asc', 'selectedReportType' => null
            ])
        </section>
    @endif
@endsection