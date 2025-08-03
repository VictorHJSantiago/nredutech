@extends('layouts.app')

@section('title', 'Professores – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Professores da Instituição</h1>
        <p class="subtitle">
            Lista de docentes cadastrados, com seus dados de contato e disciplinas
        </p>
    </header>

    <div class="form-actions" style="text-align: right; margin: 20px auto; max-width: 900px;">
        <a href="{{ route('professors-create') }}" class="btn-primary">+ Cadastrar Professor</a>
    </div>

    <section class="table-section">
        <table class="professores-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Matrícula</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Disciplina</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($professors as $professor)
                    <tr>
                        <td>{{ $professor->id }}</td>
                        <td>{{ $professor->name }}</td>
                        <td>{{ $professor->registration_id }}</td>
                        <td>{{ $professor->email }}</td>
                        <td>{{ $professor->phone }}</td>
                        <td>{{ $professor->discipline->name ?? 'N/A' }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('professors.edit', $professor) }}" class="btn-edit">✏️ Editar</a>
                            <form action="{{ route('professors.destroy', $professor) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este professor?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️ Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Nenhum professor cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection