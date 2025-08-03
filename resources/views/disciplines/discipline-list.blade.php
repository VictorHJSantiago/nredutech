@extends('layouts.app')

@section('title', 'Consultar Disciplinas – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Consulta de Disciplinas</h1>
        <p class="subtitle">Busque, filtre e visualize disciplinas cadastradas</p>
    </header>

    <div class="form-actions" style="text-align: right; margin-bottom: 20px;">
        <a href="{{ route('disciplines.create') }}" class="btn-primary">+ Cadastrar Nova Disciplina</a>
    </div>

    <form method="GET" action="{{ route('disciplines.index') }}" class="filter-bar">
        <input type="text" name="search" placeholder="Buscar por nome..." value="{{ request('search') }}">
        
        <select name="institution">
            <option value="">Todas as instituições</option>
            @foreach ($institutions as $institution)
                <option value="{{ $institution->id }}" {{ request('institution') == $institution->id ? 'selected' : '' }}>
                    {{ $institution->name }}
                </option>
            @endforeach
        </select>
        
        <select name="shift">
            <option value="">Todos os turnos</option>
            <option value="manha" {{ request('shift') == 'manha' ? 'selected' : '' }}>Manhã</option>
            <option value="tarde" {{ request('shift') == 'tarde' ? 'selected' : '' }}>Tarde</option>
            <option value="noite" {{ request('shift') == 'noite' ? 'selected' : '' }}>Noite</option>
        </select>

        <button type="submit" class="btn-search">🔍 Buscar</button>
    </form>

    <section class="table-section">
        <table class="disciplinas-table">
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
                @forelse ($disciplines as $discipline)
                    <tr>
                        <td>{{ $discipline->id }}</td>
                        <td>{{ $discipline->name }}</td>
                        <td>{{ $discipline->turma->name ?? 'N/A' }}</td>
                        <td>{{ $discipline->institution->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($discipline->shift) }}</td>
                        <td>{{ $discipline->created_at->format('d/m/Y') }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('disciplines.edit', $discipline) }}" class="btn-edit">✏️ Editar</a>
                            <a href="{{ route('disciplines.show', $discipline) }}" class="btn-detail">🔍 Detalhar</a>
                            <form action="{{ route('disciplines.destroy', $discipline) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️ Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Nenhuma disciplina encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection