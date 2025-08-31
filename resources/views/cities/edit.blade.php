@extends('layouts.app')

@section('title', 'Editar Município')

@push('styles')
@endpush

@section('content')
    <div class="header-section">
        <h1 class="text-2xl font-semibold text-gray-800">Editar Município</h1>
        <p class="text-gray-600 mt-1">Altere o nome do município: {{ $municipio->nome }}</p>
    </div>

    <div class="page-content-schools">
        <div class="card" style="max-width: 800px; margin: auto;">
            <div class="card-header">
                <h3>Formulário de Edição</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('municipios.update', $municipio->id_municipio) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="municipio_nome">Nome do Município</label>
                        <input type="text" id="municipio_nome" name="nome" class="form-control" value="{{ old('nome', $municipio->nome) }}" required>
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