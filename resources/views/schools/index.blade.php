@extends('layouts.app')

@section('title', 'Gestão de Escolas e Municípios')

@push('styles')
@endpush

@section('content')
    <div class="header-section">
        <h1 class="text-2xl font-semibold text-gray-800">Gestão de Escolas e Municípios</h1>
        <p class="text-gray-600 mt-1">Adicione, visualize e gerencie as instituições e seus respectivos municípios.</p>
    </div>

    <div class="page-content-schools">
        @if (session('success'))
            <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">{{ session('success') }}</div>
        @endif
         @if (session('error'))
            <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card-grid">
            <div class="card">
                <div class="card-header">
                    <h3>Adicionar Novo Município</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('municipios.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="municipio_nome">Nome do Município</label>
                            <input type="text" id="municipio_nome" name="nome" class="form-control" placeholder="Ex: Curitiba" required>
                        </div>
                        <button type="submit" class="button button-primary">Salvar Município</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Adicionar Nova Escola</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('escolas.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="escola_nome">Nome da Escola</label>
                            <input type="text" id="escola_nome" name="nome" class="form-control" placeholder="Ex: Escola Estadual NREduTech" required>
                        </div>
                        <div class="form-group">
                            <label for="escola_municipio">Município</label>
                            <select id="escola_municipio" name="id_municipio" class="form-control" required>
                                <option value="" disabled selected>Selecione um município</option>
                                @foreach ($municipios as $municipio)
                                    <option value="{{ $municipio->id_municipio }}">{{ $municipio->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="escola_tipo">Tipo de Escola</label>
                            <select id="escola_tipo" name="tipo" class="form-control" required>
                                <option value="colegio_estadual">Colégio Estadual</option>
                                <option value="escola_tecnica">Escola Técnica</option>
                                <option value="escola_municipal">Escola Municipal</option>
                            </select>
                        </div>
                        <button type="submit" class="button button-primary">Salvar Escola</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-grid">
            <div class="card">
                <div class="card-header">
                    <h3>Municípios Cadastrados</h3>
                </div>
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th class="actions-header">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($municipios as $municipio)
                                <tr>
                                    <td>{{ $municipio->nome }}</td>
                                    <td class="actions">
                                        <a href="{{ route('municipios.edit', $municipio->id_municipio) }}" class="button-icon" title="Editar">✏️</a>
                                        <form action="{{ route('municipios.destroy', $municipio->id_municipio) }}" method="POST" onsubmit="return confirm('Tem certeza?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button-icon" title="Excluir">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2">Nenhum município cadastrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Escolas Cadastradas</h3>
                </div>
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nome da Escola</th>
                                <th>Município</th>
                                <th>Tipo</th>
                                <th class="actions-header">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($escolas as $escola)
                                <tr>
                                    <td>{{ $escola->nome }}</td>
                                    <td>{{ $escola->municipio->nome ?? 'N/A' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $escola->tipo)) }}</td>
                                    <td class="actions">
                                       <a href="{{ route('escolas.edit', $escola->id_escola) }}" class="button-icon" title="Editar">✏️</a>
                                        <form action="{{ route('escolas.destroy', $escola->id_escola) }}" method="POST" onsubmit="return confirm('Tem certeza?');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button-icon" title="Excluir">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4">Nenhuma escola cadastrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection