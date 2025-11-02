@extends('layouts.app')

@section('title', 'Relatórios – NREduTech')

@push('scripts')
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
                    <div class="custom-multiselect">
                        <button type="button" class="multiselect-toggle">
                            <span class="default-text">Selecione...</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="multiselect-dropdown">
                            @foreach ($municipios as $municipio)
                                <div class="multiselect-option">
                                    <input type="checkbox" name="id_municipio[]" id="municipio-{{ $municipio->id_municipio }}" value="{{ $municipio->id_municipio }}" @checked(in_array($municipio->id_municipio, request('id_municipio', [])))>
                                    <label for="municipio-{{ $municipio->id_municipio }}">{{ $municipio->nome }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="id_escola">Instituição</label>
                    <div class="custom-multiselect">
                        <button type="button" class="multiselect-toggle">
                            <span class="default-text">Selecione...</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="multiselect-dropdown">
                            @foreach ($escolas as $escola)
                                <div class="multiselect-option">
                                    <input type="checkbox" name="id_escola[]" id="escola-{{ $escola->id_escola }}" value="{{ $escola->id_escola }}" @checked(in_array($escola->id_escola, request('id_escola', [])))>
                                    <label for="escola-{{ $escola->id_escola }}">{{ $escola->nome }} ({{ $escola->municipio->nome ?? 'N/A' }})</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nivel_ensino">Nível Ensino</label>
                    <div class="custom-multiselect">
                        <button type="button" class="multiselect-toggle">
                            <span class="default-text">Selecione...</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="multiselect-dropdown">
                            <div class="multiselect-option">
                                <input type="checkbox" name="nivel_ensino[]" id="nivel-colegio_estadual" value="colegio_estadual" @checked(in_array('colegio_estadual', request('nivel_ensino', [])))>
                                <label for="nivel-colegio_estadual">Colégio Estadual</label>
                            </div>
                            <div class="multiselect-option">
                                <input type="checkbox" name="nivel_ensino[]" id="nivel-escola_tecnica" value="escola_tecnica" @checked(in_array('escola_tecnica', request('nivel_ensino', [])))>
                                <label for="nivel-escola_tecnica">Escola Técnica</label>
                            </div>
                            <div class="multiselect-option">
                                <input type="checkbox" name="nivel_ensino[]" id="nivel-escola_municipal" value="escola_municipal" @checked(in_array('escola_municipal', request('nivel_ensino', [])))>
                                <label for="nivel-escola_municipal">Escola Municipal</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tipo_escola">Localização</label>
                    <div class="custom-multiselect">
                        <button type="button" class="multiselect-toggle">
                            <span class="default-text">Selecione...</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="multiselect-dropdown">
                            <div class="multiselect-option">
                                <input type="checkbox" name="tipo_escola[]" id="tipo-urbana" value="urbana" @checked(in_array('urbana', request('tipo_escola', [])))>
                                <label for="tipo-urbana">Urbana</label>
                            </div>
                            <div class="multiselect-option">
                                <input type="checkbox" name="tipo_escola[]" id="tipo-rural" value="rural" @checked(in_array('rural', request('tipo_escola', [])))>
                                <label for="tipo-rural">Rural</label>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="form-group">
                <label for="user_type">Tipo de Usuário</label>
                 <div class="custom-multiselect">
                    <button type="button" class="multiselect-toggle">
                        <span class="default-text">Selecione...</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="multiselect-dropdown">
                        @if(Auth::user()->tipo_usuario === 'administrador')
                        <div class="multiselect-option">
                            <input type="checkbox" name="user_type[]" id="user-administrador" value="administrador" @checked(in_array('administrador', request('user_type', [])))>
                            <label for="user-administrador">Administrador</label>
                        </div>
                        @endif
                        <div class="multiselect-option">
                            <input type="checkbox" name="user_type[]" id="user-diretor" value="diretor" @checked(in_array('diretor', request('user_type', [])))>
                            <label for="user-diretor">Diretor</label>
                        </div>
                        <div class="multiselect-option">
                            <input type="checkbox" name="user_type[]" id="user-professor" value="professor" @checked(in_array('professor', request('user_type', [])))>
                            <label for="user-professor">Professor</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="resource_status">Status Recurso</label>
                <div class="custom-multiselect">
                    <button type="button" class="multiselect-toggle">
                        <span class="default-text">Selecione...</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="multiselect-dropdown">
                        <div class="multiselect-option">
                            <input type="checkbox" name="resource_status[]" id="status-funcionando" value="funcionando" @checked(in_array('funcionando', request('resource_status', [])))>
                            <label for="status-funcionando">Funcionando</label>
                        </div>
                        <div class="multiselect-option">
                            <input type="checkbox" name="resource_status[]" id="status-em_manutencao" value="em_manutencao" @checked(in_array('em_manutencao', request('resource_status', [])))>
                            <label for="status-em_manutencao">Em Manutenção</label>
                        </div>
                        <div class="multiselect-option">
                            <input type="checkbox" name="resource_status[]" id="status-quebrado" value="quebrado" @checked(in_array('quebrado', request('resource_status', [])))>
                            <label for="status-quebrado">Quebrado</label>
                        </div>
                         <div class="multiselect-option">
                            <input type="checkbox" name="resource_status[]" id="status-descartado" value="descartado" @checked(in_array('descartado', request('resource_status', [])))>
                            <label for="status-descartado">Descartado</label>
                        </div>
                    </div>
                </div>
            </div>

             <div class="form-group">
                <label for="id_disciplina">Disciplina</label>
                <div class="custom-multiselect">
                    <button type="button" class="multiselect-toggle">
                        <span class="default-text">Selecione...</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="multiselect-dropdown">
                        @foreach ($disciplinas as $disciplina)
                            <div class="multiselect-option">
                                <input type="checkbox" name="id_disciplina[]" id="disciplina-{{ $disciplina->id_componente }}" value="{{ $disciplina->id_componente }}" @checked(in_array($disciplina->id_componente, request('id_disciplina', [])))>
                                <label for="disciplina-{{ $disciplina->id_componente }}">{{ $disciplina->nome }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="recurso_marca">Marca do Recurso</label>
                 <div class="custom-multiselect">
                    <button type="button" class="multiselect-toggle">
                        <span class="default-text">Selecione...</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="multiselect-dropdown">
                        @foreach ($marcas as $marca)
                            <div class="multiselect-option">
                                <input type="checkbox" name="recurso_marca[]" id="marca-{{ $loop->index }}" value="{{ $marca }}" @checked(in_array($marca, request('recurso_marca', [])))>
                                <label for="marca-{{ $loop->index }}">{{ $marca }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="turma_serie">Série/Letra da Turma</label>
                <input type="text" id="turma_serie" name="turma_serie" value="{{ request('turma_serie') }}" placeholder="Ex: 1° Ano A">
            </div>
             <div class="form-group">
                <label for="recurso_qtd_min">Qtd. Recurso (Mín)</label>
                <input type="number" id="recurso_qtd_min" name="recurso_qtd_min" value="{{ request('recurso_qtd_min') }}" min="0" placeholder="Ex: 5">
            </div>
             <div class="form-group">
                <label for="recurso_qtd_max">Qtd. Recurso (Máx)</label>
                <input type="number" id="recurso_qtd_max" name="recurso_qtd_max" value="{{ request('recurso_qtd_max') }}" min="0" placeholder="Ex: 20">
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

            <div class="form-actions" style="grid-column: 1 / -1;">
                <button type="submit" class="btn-gerar">
                    <i class="fas fa-filter"></i> Aplicar Filtros e Gerar
                </button>
                <a href="{{ route('reports.index') }}" class="btn-limpar">
                    <i class="fas fa-eraser"></i> Limpar Filtros
                </a>
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