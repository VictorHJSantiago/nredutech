@extends('layouts.app')

@section('title', 'Editar Escola')

@push('styles')
    {{-- Os estilos para esta página são importados através do app.css --}}
@endpush

@section('content')
    <div class="header-section">
        <h1 class="text-2xl font-semibold text-gray-800">Editar Escola</h1>
        <p class="text-gray-600 mt-1">Altere as informações da escola: {{ $escola->nome }}</p>
    </div>

    <div class="page-content-schools">
        <div class="card" style="max-width: 800px; margin: auto;">
            <div class="card-header">
                <h3>Formulário de Edição</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('escolas.update', $escola->id_escola) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="escola_nome">Nome da Escola</label>
                        <input type="text" id="escola_nome" name="nome" class="form-control" value="{{ old('nome', $escola->nome) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="escola_municipio">Município</label>
                        <select id="escola_municipio" name="id_municipio" class="form-control" required>
                            @foreach ($municipios as $municipio)
                                <option value="{{ $municipio->id_municipio }}" {{ old('id_municipio', $escola->id_municipio) == $municipio->id_municipio ? 'selected' : '' }}>
                                    {{ $municipio->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="escola_nivel_ensino">Nível de Ensino</label>
                        <select id="escola_nivel_ensino" name="nivel_ensino" class="form-control" required>
                            <option value="colegio_estadual" {{ old('nivel_ensino', $escola->nivel_ensino) == 'colegio_estadual' ? 'selected' : '' }}>Colégio Estadual</option>
                            <option value="escola_tecnica" {{ old('nivel_ensino', $escola->nivel_ensino) == 'escola_tecnica' ? 'selected' : '' }}>Escola Técnica</option>
                            <option value="escola_municipal" {{ old('nivel_ensino', $escola->nivel_ensino) == 'escola_municipal' ? 'selected' : '' }}>Escola Municipal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="escola_tipo">Tipo de Localização</label>
                        <select id="escola_tipo" name="tipo" class="form-control" required>
                            <option value="urbana" {{ old('tipo', $escola->tipo) == 'urbana' ? 'selected' : '' }}>Urbana</option>
                            <option value="rural" {{ old('tipo', $escola->tipo) == 'rural' ? 'selected' : '' }}>Rural</option>
                        </select>
                    </div>
                    <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                        <button type="submit" class="button button-primary">Salvar Alterações</button>
                        <a href="{{ route('escolas.index') }}" class="button" style="background-color: #6c757d; color: white;">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
