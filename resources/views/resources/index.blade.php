@extends('layouts.app')

@section('title', 'Recursos Didáticos – NREduTech')

@section('content')
    <div class="main-content">
        <header class="header-section">
            <h1>Recursos Didáticos</h1>
            <p class="subtitle">Visualize os materiais disponíveis para uso em sala de aula</p>
        </header>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="form-actions">
            <a href="{{ route('resources.create') }}" class="btn-primary">+ Cadastrar Recurso</a>
        </div>

        <section class="table-section">
            <table class="recursos-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome do Material</th>
                        <th>Marca</th>
                        <th>N.º de Série</th>
                        <th>Quantidade</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recursos as $recurso)
                        <tr>
                            <td>{{ $recurso->id_recurso }}</td>
                            <td>{{ $recurso->nome }}</td>
                            <td>{{ $recurso->marca ?? 'N/A' }}</td>
                            <td>{{ $recurso->numero_serie ?? 'N/A' }}</td>
                            <td>{{ $recurso->quantidade }}</td>
                            <td><span class="status-{{ \Illuminate\Support\Str::slug($recurso->status) }}">{{ ucfirst(str_replace('_', ' ', $recurso->status)) }}</span></td>
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
                            <td colspan="7">Nenhum recurso didático encontrado.</td>
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