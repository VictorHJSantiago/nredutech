@extends('layouts.app')

@section('title', 'Professores – NREduTech')


@section('content')
    <div class="main-content">
        <header class="header-section">
            <h1>Professores da Instituição</h1>
            <p class="subtitle">
                Lista de docentes cadastrados, com seus dados de contato e disciplinas
            </p>
        </header>

        <div class="form-actions">
            <a href="/professor-create" class="btn-primary">+ Cadastrar Professor</a>
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
                        <th>Disciplina Principal</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>João da Silva</td>
                        <td>12345-TS</td>
                        <td>joao.silva@escola.edu.br</td>
                        <td>(42) 99999-1111</td>
                        <td>Matemática</td>
                        <td class="actions-cell">
                            <a href="/professors/1/edit" class="btn-edit">✏️ Editar</a>
                            <form action="/professors/1" method="POST" onsubmit="return confirm('Deseja excluir?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️ Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Mariana Souza</td>
                        <td>23456-TS</td>
                        <td>mariana.souza@escola.edu.br</td>
                        <td>(42) 98888-2222</td>
                        <td>Português</td>
                        <td class="actions-cell">
                            <a href="/professors/2/edit" class="btn-edit">✏️ Editar</a>
                            <form action="/professors/2" method="POST" onsubmit="return confirm('Deseja excluir?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️ Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Carlos Pereira</td>
                        <td>34567-TS</td>
                        <td>carlos.pereira@escola.edu.br</td>
                        <td>(42) 97777-3333</td>
                        <td>História</td>
                        <td class="actions-cell">
                            <a href="/professors/3/edit" class="btn-edit">✏️ Editar</a>
                            <form action="/professors/3" method="POST" onsubmit="return confirm('Deseja excluir?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️ Excluir</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
@endsection