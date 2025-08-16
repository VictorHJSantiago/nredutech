@extends('layouts.app')

@section('title', 'Recursos Didáticos – NREduTech')

@section('content')
    <div class="main-content">
        <header class="header-section">
            <h1>Recursos Didáticos</h1>
            <p class="subtitle">Visualize os materiais disponíveis para uso em sala de aula</p>
        </header>

        <div class="form-actions">
            <a href="/resources/create" class="btn-primary">+ Cadastrar Recurso</a>
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
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Kit de Química Básica</td>
                        <td>Colégio Estadual Irati</td>
                        <td>LabMaster</td>
                        <td>QM-20231001</td>
                        <td><span class="status-funcionando">Funcionando</span></td>
                        <td>Conjunto de reagentes e vidrarias</td>
                        <td class="actions-cell">
                            <a href="/resources/1/edit" class="btn-edit">✏️ Editar</a>
                            <form action="/resources/1" method="POST" onsubmit="return confirm('Deseja excluir?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️ Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Projetor Multimídia</td>
                        <td>Escola Técnica de Irati</td>
                        <td>ViewTech</td>
                        <td>PV-IR-4532</td>
                        <td><span class="status-quebrado">Quebrado</span></td>
                        <td>Projetor para apresentações em sala</td>
                        <td class="actions-cell">
                            <a href="/resources/2/edit" class="btn-edit">✏️ Editar</a>
                            <form action="/resources/2" method="POST" onsubmit="return confirm('Deseja excluir?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️ Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Régua Didática Interativa</td>
                        <td>Colégio Estadual Irati</td>
                        <td>EduTools</td>
                        <td>RD-912345</td>
                        <td><span class="status-descartado">Descartado</span></td>
                        <td>Régua com sensores para demonstrações</td>
                        <td class="actions-cell">
                            <a href="/resources/3/edit" class="btn-edit">✏️ Editar</a>
                            <form action="/resources/3" method="POST" onsubmit="return confirm('Deseja excluir?');">
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