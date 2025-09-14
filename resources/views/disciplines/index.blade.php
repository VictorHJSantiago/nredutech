@extends('layouts.app')

@section('title', 'Disciplinas – NREduTech')

@section('content')
    <div class="main-content">
        <header class="header-section">
            <h1>Disciplinas</h1>
            <p class="subtitle">Visualize e gerencie os componentes curriculares cadastrados</p>
        </header>

        @if (session('success'))
            <div class="alert alert-success mb-4" style="max-width: 1100px; margin-left: auto; margin-right: auto;">
                {{ session('success') }}
            </div>
        @endif

        <div class="page-actions-container" style="max-width: 1100px; margin-left: auto; margin-right: auto;">
            <a href="{{ route('componentes.create') }}" class="btn-primary">+ Cadastrar Nova Disciplina</a>
        </div>

        <section class="filter-bar" style="max-width: 1100px; margin-left: auto; margin-right: auto;">
        </section>

        <div class="table-section-wrapper" style="max-width: 1100px; margin-left: auto; margin-right: auto;">
            <table class="disciplinas-table">
                <thead>
                    <tr>
                        @php
                            function sort_link($coluna, $titulo, $sortBy, $order) {
                                $newOrder = ($sortBy == $coluna && $order == 'asc') ? 'desc' : 'asc';
                                $icon = $sortBy == $coluna 
                                    ? ($order == 'asc' ? 'fa-arrow-up-short-wide' : 'fa-arrow-down-wide-short')
                                    : 'fa-sort';
                                $isActive = $sortBy == $coluna ? 'active' : '';
                                $url = route('componentes.index', array_merge(request()->except(['page']), [
                                    'sort_by' => $coluna,
                                    'order' => $newOrder
                                ]));
                                return "<th><a href=\"$url\" class=\"$isActive\">$titulo <i class=\"fas $icon sort-icon\"></i></a></th>";
                            }
                        @endphp

                        {!! sort_link('id_componente', 'ID', $sortBy, $order) !!}
                        {!! sort_link('nome', 'Nome', $sortBy, $order) !!}
                        <th>Cadastrado por</th>
                        {!! sort_link('carga_horaria', 'Carga Horária', $sortBy, $order) !!}
                        {!! sort_link('status', 'Status', $sortBy, $order) !!}
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($componentes as $componente)
                        <tr>
                            <td>{{ $componente->id_componente }}</td>
                            <td>{{ $componente->nome }}</td>
                            <td>{{ $componente->criador->nome_completo ?? 'Usuário removido' }}</td>
                            <td>{{ $componente->carga_horaria }}</td>
                            <td><span class="status-{{ \Illuminate\Support\Str::slug($componente->status) }}">{{ ucfirst($componente->status) }}</span></td>
                            
                            <td class="actions-cell">
                                @if ($componente->status == 'pendente')
                                    <form action="{{ route('componentes.update', $componente) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="aprovado">
                                        <button type="submit" class="btn-approve" title="Aprovar Disciplina">✅</button>
                                    </form>
                                    <form action="{{ route('componentes.update', $componente) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="reprovado">
                                        <button type="submit" class="btn-reject" title="Rejeitar Disciplina">❌</button>
                                    </form>
                                @else
                                    <a href="{{ route('componentes.edit', $componente) }}" class="btn-edit" title="Editar Disciplina">✏️ Editar</a>
                                    <form action="{{ route('componentes.destroy', $componente) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta disciplina?');" style="display:inline;" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete" title="Excluir Disciplina">🗑️ Excluir</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">Nenhuma disciplina encontrada com os filtros aplicados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="pagination-container" style="max-width: 1100px; margin-left: auto; margin-right: auto;">
            {{ $componentes->links() }}
        </div>
    </div>
@endsection
