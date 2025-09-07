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

        <div class="form-actions">
            <a href="{{ route('resources.create') }}" class="btn-primary">+ Cadastrar Item</a>
        </div>

        <section class="table-section">
            <table class="recursos-table">
                <thead>
                    <tr>
                        @php 
                            $sortParamsId = ['sort_by' => 'id_recurso', 'direction' => ($currentSortBy == 'id_recurso' && $currentDirection == 'asc') ? 'desc' : 'asc'];
                        @endphp
                        <th>
                            <a href="{{ route('resources.index', array_merge(request()->query(), $sortParamsId)) }}">
                                ID
                                @if($currentSortBy == 'id_recurso')
                                    <i class="fas fa-sort-{{ $currentDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>

                        @php 
                            $sortParamsNome = ['sort_by' => 'nome', 'direction' => ($currentSortBy == 'nome' && $currentDirection == 'asc') ? 'desc' : 'asc'];
                        @endphp
                        <th>
                            <a href="{{ route('resources.index', array_merge(request()->query(), $sortParamsNome)) }}">
                                Nome do Item
                                @if($currentSortBy == 'nome')
                                    <i class="fas fa-sort-{{ $currentDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>

                        @php 
                            $sortParamsMarca = ['sort_by' => 'marca', 'direction' => ($currentSortBy == 'marca' && $currentDirection == 'asc') ? 'desc' : 'asc'];
                        @endphp
                        <th>
                            <a href="{{ route('resources.index', array_merge(request()->query(), $sortParamsMarca)) }}">
                                Marca
                                @if($currentSortBy == 'marca')
                                    <i class="fas fa-sort-{{ $currentDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>

                        @php 
                            $sortParamsSN = ['sort_by' => 'numero_serie', 'direction' => ($currentSortBy == 'numero_serie' && $currentDirection == 'asc') ? 'desc' : 'asc'];
                        @endphp
                        <th>
                             <a href="{{ route('resources.index', array_merge(request()->query(), $sortParamsSN)) }}">
                                N.º de Série
                                @if($currentSortBy == 'numero_serie')
                                    <i class="fas fa-sort-{{ $currentDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>

                        @php 
                            $sortParamsQtd = ['sort_by' => 'quantidade', 'direction' => ($currentSortBy == 'quantidade' && $currentDirection == 'asc') ? 'desc' : 'asc'];
                        @endphp
                        <th>
                            <a href="{{ route('resources.index', array_merge(request()->query(), $sortParamsQtd)) }}">
                                Quantidade
                                @if($currentSortBy == 'quantidade')
                                    <i class="fas fa-sort-{{ $currentDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        
                        @php 
                            $sortParamsTipo = ['sort_by' => 'tipo', 'direction' => ($currentSortBy == 'tipo' && $currentDirection == 'asc') ? 'desc' : 'asc'];
                        @endphp
                        <th>
                            <a href="{{ route('resources.index', array_merge(request()->query(), $sortParamsTipo)) }}">
                                Tipo
                                @if($currentSortBy == 'tipo')
                                    <i class="fas fa-sort-{{ $currentDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>

                        @php 
                            $sortParamsStatus = ['sort_by' => 'status', 'direction' => ($currentSortBy == 'status' && $currentDirection == 'asc') ? 'desc' : 'asc'];
                        @endphp
                        <th>
                            <a href="{{ route('resources.index', array_merge(request()->query(), $sortParamsStatus)) }}">
                                Status
                                @if($currentSortBy == 'status')
                                    <i class="fas fa-sort-{{ $currentDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        
                        @php
                            $sortParamsData = ['sort_by' => 'data_aquisicao', 'direction' => ($currentSortBy == 'data_aquisicao' && $currentDirection == 'asc') ? 'desc' : 'asc'];
                        @endphp
                        <th>
                            <a href="{{ route('resources.index', array_merge(request()->query(), $sortParamsData)) }}">
                                Data de Aquisição
                                @if($currentSortBy == 'data_aquisicao')
                                    <i class="fas fa-sort-{{ $currentDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Ações</th> </tr>
                </thead>
                <tbody>
                    @forelse ($recursos as $recurso)
                        <tr>
                            <td>{{ $recurso->id_recurso }}</td>
                            <td>{{ $recurso->nome }}</td>
                            <td>{{ $recurso->marca ?? 'N/A' }}</td>
                            <td>{{ $recurso->numero_serie ?? 'N/A' }}</td>
                            <td>{{ $recurso->quantidade }}</td>
                            <td>{{ $recurso->tipo === 'didatico' ? 'Recurso Didático' : 'Laboratório' }}</td>
                            <td><span class="status-{{ \Illuminate\Support\Str::slug($recurso->status) }}">{{ ucfirst(str_replace('_', ' ', $recurso->status)) }}</span></td>
                            <td>{{ $recurso->data_aquisicao ? \Carbon\Carbon::parse($recurso->data_aquisicao)->format('d/m/Y') : 'N/A' }}</td>
                            <td class="actions-cell">
                                <a href="{{ route('resources.edit', $recurso->id_recurso) }}" class="btn-edit">✏️ Editar</a>
                                <form action="{{ route('resources.destroy', $recurso->id_recurso) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este recurso?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">🗑️ Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">Nenhum recurso ou laboratório encontrado.</td>
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

