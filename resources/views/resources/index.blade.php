@extends('layouts.app')

@section('title', 'Recursos e Laboratórios – NREduTech')

@section('content')
    <div class="main-content">
        <header class="header-section">
            <h1>Recursos e Laboratórios</h1>
            <p class="subtitle">Visualize e gerencie os recursos didáticos e laboratórios disponíveis</p>
        </header>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
         @if (session('error')) 
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="form-actions">
            <a href="{{ route('resources.create') }}" class="btn-primary">+ Cadastrar Item</a>
        </div>

        <section class="filter-bar">
            <form action="{{ route('resources.index') }}" method="GET" class="filter-form">
                <input type="text"
                       name="search_nome"
                       placeholder="Buscar por nome..."
                       value="{{ request('search_nome') }}"
                       class="filter-input-text" />
                <input type="text"
                       name="search_marca"
                       placeholder="Buscar por marca..."
                       value="{{ request('search_marca') }}"
                       class="filter-input-text" />
                <select name="status" class="filter-select">
                    <option value="">Todos os Status</option>
                    <option value="funcionando"   {{ request('status') == 'funcionando' ? 'selected' : '' }}>Funcionando</option>
                    <option value="em_manutencao" {{ request('status') == 'em_manutencao' ? 'selected' : '' }}>Em manutenção</option>
                    <option value="quebrado"      {{ request('status') == 'quebrado' ? 'selected' : '' }}>Quebrado</option>
                    <option value="descartado"    {{ request('status') == 'descartado' ? 'selected' : '' }}>Descartado</option>
                </select>
                <button type="submit" class="btn-search">🔍 Filtrar</button>
            </form>
        </section>

        <section class="table-section">
            <table class="recursos-table">
                <thead>
                    <tr>
                        @php
                            function sort_link($coluna, $titulo, $sortBy, $order) {
                                $newOrder = ($sortBy == $coluna && $order == 'asc') ? 'desc' : 'asc';
                                $icon = $sortBy == $coluna
                                    ? ($order == 'asc' ? 'fa-arrow-up-short-wide' : 'fa-arrow-down-wide-short')
                                    : 'fa-sort';
                                $isActive = $sortBy == $coluna ? 'active' : '';
                                $url = route('resources.index', array_merge(request()->except(['page']), [
                                    'sort_by' => $coluna,
                                    'order' => $newOrder
                                ]));
                                return "<th><a href=\"$url\" class=\"$isActive\">$titulo <i class=\"fas $icon sort-icon\"></i></a></th>";
                            }
                        @endphp
                        {!! sort_link('id_recurso', 'ID', $sortBy, $order) !!}
                        {!! sort_link('nome', 'Nome do Item', $sortBy, $order) !!}
                        <th>Observações</th>
                        {!! sort_link('marca', 'Marca', $sortBy, $order) !!}
                        {!! sort_link('numero_serie', 'N.º de Série', $sortBy, $order) !!}
                        {!! sort_link('quantidade', 'Qtd', $sortBy, $order) !!}
                        {!! sort_link('tipo', 'Tipo', $sortBy, $order) !!}
                        {!! sort_link('escola_nome', 'Escola', $sortBy, $order) !!}           
                        {!! sort_link('criador_nome', 'Cadastrado Por', $sortBy, $order) !!} 
                        {!! sort_link('status', 'Status', $sortBy, $order) !!}
                        {!! sort_link('data_aquisicao', 'Data Aquisição', $sortBy, $order) !!}
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recursos as $recurso)
                        <tr>
                            <td>{{ $recurso->id_recurso }}</td>
                            <td>{{ $recurso->nome }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($recurso->observacoes, 50) ?? 'N/A' }}</td>
                            <td>{{ $recurso->marca ?? 'N/A' }}</td>
                            <td>{{ $recurso->numero_serie ?? 'N/A' }}</td>
                            <td>{{ $recurso->quantidade }}</td>
                            <td>{{ $recurso->tipo === 'didatico' ? 'Recurso Didático' : 'Laboratório' }}</td>
                            <td>{{ $recurso->escola_nome ?? 'Global' }}</td>           
                            <td>{{ $recurso->criador_nome ?? 'N/A' }}</td>               
                            <td><span class="status-{{ \Illuminate\Support\Str::slug($recurso->status) }}">{{ ucfirst(str_replace('_', ' ', $recurso->status)) }}</span></td>
                            <td>{{ $recurso->data_aquisicao ? \Carbon\Carbon::parse($recurso->data_aquisicao)->format('d/m/Y') : 'N/A' }}</td>
                            
                            <td class="actions-cell">
                                @php
                                    $user = Auth::user();
                                    $isCreator = $user->id_usuario == $recurso->id_usuario_criador;
                                    $canManage = $user->tipo_usuario == 'administrador' ||
                                                 $user->tipo_usuario == 'diretor' ||
                                                 ($user->tipo_usuario == 'professor' && $isCreator);
                                @endphp

                                @if($canManage)
                                    <a href="{{ route('resources.edit', $recurso->id_recurso) }}" class="btn-edit" title="Editar Recurso">✏️ Editar</a>
                                    <button type="button"
                                        class="delete-button btn-delete"
                                        style="display:inline;"
                                        title="Excluir Recurso"
                                        data-item-name="{{ $recurso->nome }}"
                                        data-form-action="{{ route('resources.destroy', $recurso->id_recurso) }}">
                                        🗑️ Excluir
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12">Nenhum recurso ou laboratório encontrado com os filtros aplicados.</td> 
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-links">
                {{ $recursos->links() }}
            </div>
        </section>
    </div>
@endsection