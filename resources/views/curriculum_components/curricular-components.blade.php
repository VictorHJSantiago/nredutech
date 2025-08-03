@extends('layouts.app')

@section('title', 'Componentes Curriculares')

@section('content')
    <header class="header-section">
        <h1>Componentes Curriculares</h1>
        <p class="subtitle">
            Turmas, professores responsáveis e disciplinas associadas
        </p>
    </header>

    <section class="table-section">
        <table class="componentes-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Turma</th>
                    <th>Professor</th>
                    <th>Matéria</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                {{-- 
                    Em um projeto real, os dados da tabela viriam de um Controller
                    e seriam exibidos com um loop @foreach, como no exemplo abaixo:
                --}}
                {{-- @foreach ($componentes as $componente) --}}
                    <tr>
                        <td>1 {{-- {{ $componente->id }} --}}</td>
                        <td>Turma 101 – Manhã {{-- {{ $componente->turma }} --}}</td>
                        <td>João Silva {{-- {{ $componente->professor->nome }} --}}</td>
                        <td>Matemática {{-- {{ $componente->materia->nome }} --}}</td>
                        <td>
                            <a href="{{-- route('components.edit', $componente->id) --}}" class="btn-edit">✏️ Editar</a>
                            <form action="{{-- route('components.destroy', $componente->id) --}}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️ Excluir</button>
                            </form>
                        </td>
                    </tr>
                {{-- @endforeach --}}

                {{-- Dados estáticos para visualização --}}
                <tr>
                    <td>2</td>
                    <td>Turma 102 – Tarde</td>
                    <td>Mariana Souza</td>
                    <td>Português</td>
                    <td>
                        <button class="btn-edit">✏️ Editar</button>
                        <button class="btn-delete">🗑️ Excluir</button>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Turma 201 – Noite</td>
                    <td>Carlos Pereira</td>
                    <td>História</td>
                    <td>
                        <button class="btn-edit">✏️ Editar</button>
                        <button class="btn-delete">🗑️ Excluir</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
@endsection