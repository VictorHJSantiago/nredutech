@extends('layouts.app')

@section('title', 'Disciplinas – NREduTech')

@section('content')
    <div class="main-content">
        <header class="header-section">
            <h1>Disciplinas</h1>
            <p class="subtitle">Visualize e gerencie os componentes curriculares cadastrados</p>
        </header>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="form-actions">
            <a href="{{ route('componentes.create') }}" class="btn-primary">+ Cadastrar Nova Disciplina</a>
        </div>

        <section class="table-section">
            <table class="disciplinas-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Carga Horária</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($componentes as $componente)
                        <tr>
                            <td>{{ $componente->id_componente }}</td>
                            <td>{{ $componente->nome }}</td>
                            <td>{{ $componente->carga_horaria }}</td>
                            <td><span class="status-{{ \Illuminate\Support\Str::slug($componente->status) }}">{{ ucfirst($componente->status) }}</span></td>
                            <td class="actions-cell">
                                <a href="{{ route('componentes.edit', $componente) }}" class="btn-edit">✏️ Editar</a>
                                <form action="{{ route('componentes.destroy', $componente) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta disciplina?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">🗑️ Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Nenhuma disciplina encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="pagination-links">
                {{ $componentes->links() }}
            </div>
        </section>
    </div>
@endsection
