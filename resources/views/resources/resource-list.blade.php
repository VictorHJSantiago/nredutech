@extends('layouts.app')

@section('title', 'Recursos Didáticos – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Recursos Didáticos</h1>
        <p class="subtitle">Visualize os materiais disponíveis para uso em sala de aula</p>
    </header>

    <div class="form-actions" style="text-align: right; margin: 20px auto; max-width: 1000px;">
        <a href="{{ route('resources.create') }}" class="btn-primary">+ Cadastrar Recurso</a>
    </div>

    <section class="table-section">
        <table class="recursos-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Material</th>
                    <th>Instituição</th>
                    <th>Marca</th>
                    <th>N.º de Série</th>
                    <th>Estado</th>
                    <th>Descrição (Opcional)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($resources as $resource)
                    <tr>
                        <td>{{ $resource->id }}</td>
                        <td>{{ $resource->name }}</td>
                        <td>{{ $resource->institution->name ?? 'N/A' }}</td>
                        <td>{{ $resource->brand ?? 'N/A' }}</td>
                        <td>{{ $resource->serial_number ?? 'N/A' }}</td>
                        <td>
                            <span class="status-{{ Str::slug($resource->status) }}">
                                {{ ucfirst($resource->status) }}
                            </span>
                        </td>
                        <td>{{ $resource->observations ?? 'Nenhuma' }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('resources.edit', $resource) }}" class="btn-edit">✏️ Editar</a>
                            <form action="{{ route('resources.destroy', $resource) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este recurso?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️ Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Nenhum recurso didático encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection