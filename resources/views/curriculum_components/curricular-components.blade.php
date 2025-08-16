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
                    <tr>
                        <td>1 </td>
                        <td>Turma 101 – Manhã</td>
                        <td>João Silva</td>
                        <td>Matemática</td>
                        <td>
                            <a href="" class="btn-edit">✏️ Editar</a>
                            <form action="" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️ Excluir</button>
                            </form>
                        </td>
                    </tr>
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