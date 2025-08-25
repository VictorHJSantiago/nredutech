@extends('layouts.app')

@section('title', 'Consulta de Disciplinas')

@section('content')
<div class="page-header">
    <div>
        <h2>Consulta de Disciplinas</h2>
        <p>Busque, filtre e visualize disciplinas cadastradas</p>
    </div>
    <a href="{{ route('componentes.create') }}" class="btn btn-primary">+ Cadastrar Nova Disciplina</a>
</div>

<div class="filter-bar">
    <form action="{{ route('componentes.index') }}" method="GET">
        <input type="text" name="search" placeholder="Buscar por nome..." value="{{ request('search') }}">
        <select name="instituicao">
            <option value="">Todas as instituições</option>
            {{-- As opções de instituição seriam carregadas dinamicamente --}}
        </select>
        <select name="turno">
            <option value="">Todos os turnos</option>
            {{-- As opções de turno seriam carregadas dinamicamente --}}
        </select>
        <button type="submit" class="btn btn-secondary">🔍 Buscar</button>
    </form>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Turma</th>
                <th>Instituição</th>
                <th>Turno</th>
                <th>Data Cadastro</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($componentes as $componente)
                <tr>
                    <td>{{ $componente->id_componente }}</td>
                    <td>{{ $componente->nome }}</td>
                    
                    {{-- OBS: Os campos a seguir são placeholders. --}}
                    {{-- Você precisa adicionar essas colunas/relações ao seu Model 'ComponenteCurricular'. --}}
                    <td>1º Ano Médio</td>
                    <td>Escola Técnica Irati</td>
                    <td>Noite</td>
                    
                    <td>{{ $componente->created_at->format('d/m/Y') }}</td>
                    <td class="actions">
                        <a href="{{ route('componentes.edit', $componente) }}" class="btn btn-edit">✏️ Editar</a>
                        <form action="{{ route('componentes.destroy', $componente) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-delete" onclick="return confirm('Tem certeza?')">🗑️ Excluir</button>
                        </form>
                        <a href="{{ route('componentes.show', $componente) }}" class="btn btn-detail">🔍 Detalhar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Nenhuma disciplina encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination-container">
    {{ $componentes->links() }}
</div>
@endsection