@extends('layouts.app')

@section('title', 'Consultar Disciplinas – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Consulta de Disciplinas</h1>
        <p class="subtitle">Busque, filtre e visualize disciplinas cadastradas</p>
    </header>

    <div class="form-actions">
        {{-- CORREÇÃO APLICADA AQUI --}}
        <a href="/disciplines/create" class="btn-primary">+ Cadastrar Nova Disciplina</a>
    </div>

    <form method="GET" action="#" class="filter-bar">
        <input type="text" name="search" placeholder="Buscar por nome...">
        
        <select name="institution">
            <option value="">Todas as instituições</option>
            <option value="1">Instituição A</option>
            <option value="2">Instituição B</option>
        </select>
        
        <select name="shift">
            <option value="">Todos os turnos</option>
            <option value="manha">Manhã</option>
            <option value="tarde">Tarde</option>
            <option value="noite">Noite</option>
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
                <tr>
                    <td>1</td>
                    <td>Cálculo I</td>
                    <td>T-01</td>
                    <td>Universidade Federal</td>
                    <td>Manhã</td>
                    <td>10/08/2024</td>
                    <td class="actions-cell">
                        <a href="#" class="btn-edit">✏️ Editar</a>
                        <a href="#" class="btn-detail">🔍 Detalhar</a>
                        <form action="#" method="POST">
                            <button type="submit" class="btn-delete">🗑️ Excluir</button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Programação Orientada a Objetos</td>
                    <td>T-02</td>
                    <td>Instituto de Tecnologia</td>
                    <td>Noite</td>
                    <td>11/08/2024</td>
                    <td class="actions-cell">
                        <a href="#" class="btn-edit">✏️ Editar</a>
                        <a href="#" class="btn-detail">🔍 Detalhar</a>
                        <form action="#" method="POST">
                            <button type="submit" class="btn-delete">🗑️ Excluir</button>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td colspan="7">Nenhuma disciplina encontrada.</td>
                </tr>
            </tbody>
        </table>
    </section>
@endsection
