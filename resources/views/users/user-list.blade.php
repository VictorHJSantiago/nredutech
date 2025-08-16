@extends('layouts.app')

@section('title', 'Usuários – NREduTech')

@section('content')
    <div class="main-content">
        <header class="header-section">
            <h1>Gestão de Usuários</h1>
            <p class="subtitle">Aprovar, editar ou remover cadastros (Somente Admin)</p>
        </header>

        <div class="form-actions">
            <a href="/users/create" class="btn-primary">+ Cadastrar Usuário</a>
        </div>

        <section class="filter-bar">
            <input type="text" id="buscaUsuario" placeholder="Buscar por nome ou e-mail..." />
            <select id="filtroPapel">
                <option value="">Todos os papéis</option>
                <option value="aluno">Aluno</option>
                <option value="professor">Professor</option>
                <option value="gestor">Gestor</option>
                <option value="administrador">Administrador</option>
            </select>
            <button class="btn-search">🔍 Filtrar</button>
        </section>

        <section class="table-section">
            <table class="usuarios-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome de Usuário</th>
                        <th>E-mail</th>
                        <th>Data de Registro</th>
                        <th>Papel</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>ana.pereira</td>
                        <td>ana.pereira@example.com</td>
                        <td>22/08/2025</td>
                        <td>Professor</td>
                        <td><span class="status-pendente">Pendente</span></td>
                        <td class="acao-cell">
                            <button class="btn-approve">✅</button>
                            <button class="btn-reject">❌</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>bruno.souza</td>
                        <td>bruno.souza@example.com</td>
                        <td>23/08/2025</td>
                        <td>Aluno</td>
                        <td><span class="status-pendente">Pendente</span></td>
                        <td class="acao-cell">
                            <button class="btn-approve">✅</button>
                            <button class="btn-reject">❌</button>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>carla.ribeiro</td>
                        <td>carla.ribeiro@example.com</td>
                        <td>20/08/2025</td>
                        <td>Gestor</td>
                        <td><span class="status-aprovado">Aprovado</span></td>
                        <td class="acao-cell">
                            <a href="/users/3/edit" class="btn-edit">✏️</a>
                            <form action="/users/3" method="POST" onsubmit="return confirm('Deseja excluir?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
@endsection
