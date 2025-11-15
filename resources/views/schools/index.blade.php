@extends('layouts.app')

@section('title', 'Gestão de Escolas e Municípios')

@push('styles')
    @vite('resources/css/schools.css')
@endpush

@section('content')
    <div class="header-section">
        <h1 class="text-2xl font-semibold text-gray-800">Gestão de Escolas e Municípios</h1>
        <p class="text-gray-600 mt-1">Adicione, visualize e gerencie as instituições e seus respectivos municípios.</p>
    </div>

    <div class="page-content-schools">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card-grid">
            <div class="card">
                <div class="card-header">
                    <h3>Adicionar Novo Município</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('municipios.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="municipio_nome">Nome do Município</label>
                            <input type="text" id="municipio_nome" name="nome" class="form-control"
                                placeholder="Ex: Curitiba" required>
                        </div>
                        <button type="submit" class="button button-primary">Salvar Município</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Adicionar Nova Escola</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('escolas.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="escola_nome">Nome da Escola</label>
                            <input type="text" id="escola_nome" name="nome" class="form-control"
                                placeholder="Ex: Escola Estadual NREduTech" required>
                        </div>
                        <div class="form-group">
                            <label for="escola_municipio">Município</label>
                            <select id="escola_municipio" name="id_municipio" class="form-control" required>
                                <option value="" disabled selected>Selecione um município</option>
                                @foreach ($municipios as $municipio)
                                    <option value="{{ $municipio->id_municipio }}">{{ $municipio->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="escola_nivel_ensino">Nível de Ensino</label>
                            <select id="escola_nivel_ensino" name="nivel_ensino" class="form-control" required>
                                <option value="colegio_estadual">Colégio Estadual</option>
                                <option value="escola_tecnica">Escola Técnica</option>
                                <option value="escola_municipal">Escola Municipal</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="escola_localizacao">Tipo de Localização</label>
                            <select id="escola_localizacao" name="tipo" class="form-control" required>
                                <option value="urbana">Urbana</option>
                                <option value="rural">Rural</option>
                            </select>
                        </div>
                        <button type="submit" class="button button-primary">Salvar Escola</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card card-full municipios-card-lista">
            <div class="card-header">
                <h3>Municípios Cadastrados</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive-wrapper">
                    <table class="data-table">
                        <thead class="table-header">
                            <tr>
                                <th>Nome</th>
                                <th class="actions-header">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($municipios as $municipio)
                                <tr>
                                    <td>{{ $municipio->nome }}</td>
                                    <td class="actions">
                                        <a href="{{ route('municipios.edit', $municipio->id_municipio) }}"
                                            class="button-icon btn-edit" title="Editar">✏️</a>
                                        <button type="button"
                                            class="delete-button button-icon btn-delete"
                                            title="Excluir"
                                            data-item-name="{{ $municipio->nome }}"
                                            data-form-action="{{ route('municipios.destroy', $municipio->id_municipio) }}">
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">Nenhum município cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <section class="filter-bar filter-bar-escolas">
             <form action="{{ route('escolas.index') }}" method="GET" class="filter-form js-clean-get-form">
                <div class="filter-group search-main">
                    <label for="search_nome">Buscar por Nome da Escola ou Diretor</label>
                    <input type="text" id="search_nome" name="search_nome"
                        placeholder="Buscar por nome de escola ou diretor..." value="{{ request('search_nome') }}" />
                </div>
                <div class="filter-group">
                    <label for="id_municipio">Filtrar por Município</label>
                    <select id="id_municipio" name="id_municipio">
                        <option value="">Todos os Municípios</option>
                        @foreach ($municipios as $municipio)
                            <option value="{{ $municipio->id_municipio }}" {{ request('id_municipio') == $municipio->id_municipio ? 'selected' : '' }}>
                                {{ $municipio->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="nivel_ensino">Nível de Ensino</label>
                    <select id="nivel_ensino" name="nivel_ensino">
                        <option value="">Todos</option>
                        <option value="colegio_estadual" {{ request('nivel_ensino') == 'colegio_estadual' ? 'selected' : '' }}>Colégio Estadual</option>
                        <option value="escola_tecnica" {{ request('nivel_ensino') == 'escola_tecnica' ? 'selected' : '' }}>
                            Escola Técnica</option>
                        <option value="escola_municipal" {{ request('nivel_ensino') == 'escola_municipal' ? 'selected' : '' }}>Escola Municipal</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="tipo">Localização</label>
                    <select id="tipo" name="tipo">
                        <option value="">Todas</option>
                        <option value="urbana" {{ request('tipo') == 'urbana' ? 'selected' : '' }}>Urbana</option>
                        <option value="rural" {{ request('tipo') == 'rural' ? 'selected' : '' }}>Rural</option>
                    </select>
                </div>
                @if(request('sort_by'))
                    <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                @endif
                @if(request('order'))
                    <input type="hidden" name="order" value="{{ request('order') }}">
                @endif
                <div class="filter-group search-submit">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn-search">🔍 Filtrar</button>
                </div>
            </form>
        </section>

        <div class="card card-full escolas-card-lista">
            <div class="card-header">
                <h3>Escolas Cadastradas</h3>
            </div>
            <div class="card-body">
                @php
                    if (!function_exists('sort_link')) {
                        function sort_link($coluna, $titulo, $sortBy, $order)
                        {
                            $newOrder = ($sortBy == $coluna && $order == 'asc') ? 'desc' : 'asc';
                            $icon = $sortBy == $coluna
                                ? ($order == 'asc' ? 'fa-arrow-up-short-wide' : 'fa-arrow-down-wide-short')
                                : 'fa-sort';
                            $isActive = $sortBy == $coluna ? 'active' : '';
                            $queryParams = array_filter(request()->except(['page']));
                            $url = route('escolas.index', array_merge($queryParams, [
                                'sort_by' => $coluna,
                                'order' => $newOrder
                            ]));
                            return "<th><a href=\"$url\" class=\"$isActive\">$titulo <i class=\"fas $icon sort-icon\"></i></a></th>";
                        }
                    }
                @endphp
                <div class="table-responsive-wrapper">
                    <table class="data-table">
                        <thead class="table-header">
                            <tr>
                                {!! sort_link('id_escola', 'ID', $sortBy, $order) !!}
                                {!! sort_link('nome', 'Nome da Escola', $sortBy, $order) !!}
                                {!! sort_link('municipio_nome', 'Município', $sortBy, $order) !!}
                                {!! sort_link('nivel_ensino', 'Nível de Ensino', $sortBy, $order) !!}
                                {!! sort_link('tipo', 'Tipo', $sortBy, $order) !!}
                                {!! sort_link('diretor_nome', 'Diretores', $sortBy, $order) !!}
                                <th class="actions-header">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($escolas as $escola)
                                <tr>
                                    <td>{{ $escola->id_escola }}</td>
                                    <td>{{ $escola->nome }}</td>
                                    <td>{{ $escola->municipio->nome ?? 'N/A' }}</td>
                                    <td>
                                        @switch($escola->nivel_ensino)
                                            @case('colegio_estadual')
                                                Colégio Estadual
                                                @break
                                            @case('escola_tecnica')
                                                Escola Técnica
                                                @break
                                            @case('escola_municipal')
                                                Escola Municipal
                                                @break
                                            @default
                                                {{ $escola->nivel_ensino }}
                                        @endswitch
                                    </td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $escola->tipo)) }}</td>
                                    <td>
                                        @php
                                            $count = $escola->usuarios->count();
                                            $class = '';
                                            if ($count == 0) {
                                                $class = 'count-empty';
                                            } elseif ($count == 1) {
                                                $class = 'count-ok';
                                            } elseif ($count >= 2) {
                                                $class = 'count-full';
                                            }
                                        @endphp
                                        <div class="director-count {{ $class }}">({{ $count }}/2)</div>
                                        @forelse($escola->usuarios as $diretor)
                                            <div>{{ $diretor->nome_completo }}</div>
                                        @empty
                                            <span style="color: #888; font-size: 0.9em;">Nenhum diretor ativo</span>
                                        @endforelse
                                    </td>
                                    <td class="actions">
                                        <a href="{{ route('escolas.edit', $escola->id_escola) }}" class="button-icon btn-edit"
                                            title="Editar">✏️</a>
                                        <button type="button"
                                            class="delete-button button-icon btn-delete"
                                            title="Excluir"
                                            data-item-name="{{ $escola->nome }}"
                                            data-form-action="{{ route('escolas.destroy', $escola->id_escola) }}">
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">Nenhuma escola encontrada com os filtros aplicados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="pagination-container">
                    {{ $escolas->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection