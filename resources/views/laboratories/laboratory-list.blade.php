@extends('layouts.app')

@section('title', 'Laboratórios – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Uso de Laboratórios</h1>
        <p class="subtitle">Visualize a disponibilidade e uso dos laboratórios em cada instituição</p>
    </header>

    <section class="table-section">
        <table class="laboratorios-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Instituição</th>
                    <th>Laboratório</th>
                    <th>Status</th>
                    <th>Última Limpeza</th>
                    <th>Observações</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laboratories as $laboratory)
                    <tr>
                        <td>{{ $laboratory->id }}</td>
                        <td>{{ $laboratory->institution->name }}</td>
                        <td>{{ $laboratory->name }}</td>
                        <td>
                            <span class="status status-{{ Str::slug($laboratory->status) }}">
                                {{ $laboratory->status }}
                            </span>
                        </td>
                        <td>{{ $laboratory->last_cleaned_at->format('d/m/Y') }}</td>
                        <td>{{ $laboratory->observations }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('laboratories.edit', $laboratory) }}" class="btn-edit">✏️ Editar</a>
                            
                            <form action="{{ route('laboratories.destroy', $laboratory) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este laboratório?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️ Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Nenhum laboratório encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection