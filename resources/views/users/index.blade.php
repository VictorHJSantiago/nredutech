@extends('layouts.app')

@section('title', 'Usuários – NREduTech')

@section('content')
<div class="main-content">
    <header class="header-section">
        <h1>Gestão de Usuários</h1>
        <p class="subtitle">Aprovar, editar ou remover cadastros de usuários do sistema.</p>
    </header>

    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="page-actions-container">
        <a href="{{ route('usuarios.create') }}" class="btn-primary">+ Cadastrar Usuário</a>
    </div>

    <section class="filter-bar">
        <form action="{{ route('usuarios.index') }}" method="GET" class="filter-form">
            
            <div class="filter-group search-main">
                <label for="search">Buscar por Nome ou E-mail</label>
                <input type="text" id="search" name="search" placeholder="Buscar por nome ou e-mail..." value="{{ request('search') }}" />
            </div>
            <div class="filter-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">Todos os Status</option>
                    <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="bloqueado" {{ request('status') == 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="search_doc">CPF, RG ou Registro</label>
                <input type="text" id="search_doc" name="search_doc" placeholder="Digite um documento..." value="{{ request('search_doc') }}" />
            </div>
            
            <div class="filter-group">
                <label for="search_edu">Formação ou Área</label>
                <input type="text" id="search_edu" name="search_edu" placeholder="Busca por formação..." value="{{ request('search_edu') }}" />
            </div>

            <div class="filter-group">
                <label for="search_date">Data (Registro ou Nasc.)</label>
                <input type="date" id="search_date" name="search_date" value="{{ request('search_date') }}" />
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

    <div class="table-section-wrapper">
        <table class="usuarios-table">
            <thead>
                <tr>
                    @php
                        function sort_link($coluna, $titulo, $sortBy, $order) {
                            $newOrder = ($sortBy == $coluna && $order == 'asc') ? 'desc' : 'asc';
                            $icon = $sortBy == $coluna 
                                ? ($order == 'asc' ? 'fa-arrow-up-short-wide' : 'fa-arrow-down-wide-short')
                                : 'fa-sort';
                            $isActive = $sortBy == $coluna ? 'active' : '';
                            $url = route('usuarios.index', array_merge(request()->except(['page']), [
                                'sort_by' => $coluna,
                                'order' => $newOrder
                            ]));
                            return "<th><a href=\"$url\" class=\"$isActive\">$titulo <i class=\"fas $icon sort-icon\"></i></a></th>";
                        }
                    @endphp

                    {!! sort_link('id_usuario', 'ID', $sortBy, $order) !!}
                    {!! sort_link('nome_completo', 'Nome Completo', $sortBy, $order) !!}
                    {!! sort_link('email', 'E-mail', $sortBy, $order) !!}
                    {!! sort_link('data_registro', 'Data Registro', $sortBy, $order) !!}
                    {!! sort_link('tipo_usuario', 'Tipo', $sortBy, $order) !!}
                    {!! sort_link('status_aprovacao', 'Status', $sortBy, $order) !!}                   
                    <th class="hide-on-mobile">Data Nasc.</th>
                    <th class="hide-on-mobile">CPF</th>
                    <th class="hide-on-mobile">RG</th>
                    <th class="hide-on-mobile">Registro</th>
                    <th class="hide-on-mobile">Telefone</th>
                    <th class="hide-on-mobile">Formação</th>
                    <th class="hide-on-mobile">Área</th>
                    
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id_usuario }}</td>
                        <td>{{ $usuario->nome_completo }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ \Carbon\Carbon::parse($usuario->data_registro)->format('d/m/Y H:i') }}</td> 
                        <td>{{ ucfirst($usuario->tipo_usuario) }}</td>
                        <td>
                            @switch($usuario->status_aprovacao)
                                @case('ativo')
                                    <span class="status-aprovado">Aprovado</span>
                                    @break
                                @case('pendente')
                                    <span class="status-pendente">Pendente</span>
                                    @break
                                @case('bloqueado')
                                    <span class="status-rejeitado">Bloqueado</span>
                                    @break
                            @endswitch
                        </td>
                        <td class="hide-on-mobile">{{ $usuario->data_nascimento ? \Carbon\Carbon::parse($usuario->data_nascimento)->format('d/m/Y') : 'N/A' }}</td>
                        <td class="hide-on-mobile">{{ $usuario->cpf ?? 'N/A' }}</td>
                        <td class="hide-on-mobile">{{ $usuario->rg ?? 'N/A' }}</td>
                        <td class="hide-on-mobile">{{ $usuario->rco_siape ?? 'N/A' }}</td>
                        <td class="hide-on-mobile">{{ $usuario->telefone ?? 'N/A' }}</td>
                        <td class="hide-on-mobile">{{ $usuario->formacao ?? 'N/A' }}</td>
                        <td class="hide-on-mobile">{{ $usuario->area_formacao ?? 'N/A' }}</td>

                        <td class="acao-cell">
                            @if ($usuario->status_aprovacao == 'pendente')
                                <form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status_aprovacao" value="ativo">
                                    <button type="submit" class="btn-approve" title="Aprovar Cadastro">✅</button>
                                </form>
                                <form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status_aprovacao" value="bloqueado">
                                    <button type="submit" class="btn-reject" title="Rejeitar Cadastro">❌</button>
                                </form>
                            @else
                                <a href="{{ route('usuarios.edit', $usuario) }}" class="btn-edit" title="Editar Usuário">✏️</a>
                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Excluir Usuário">🗑️</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="15">Nenhum usuário encontrado com os filtros aplicados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div> 
    <div class="pagination-container">
        {{ $usuarios->links() }}
    </div>
</div>
@endsection