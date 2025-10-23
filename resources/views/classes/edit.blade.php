@extends('layouts.app')

@section('title', 'Editar Turma')

@section('content')
<div class="main-content">
    <header class="header-section">
        <h1>Editar Turma: {{ $turma->serie }}</h1>
        <p class="subtitle">Altere as informações da turma abaixo.</p>
    </header>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Opa!</strong> Ocorreram alguns problemas:
            <ul>
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="card edit-turma-card">
        <div class="card-header">
            <h3><i class="fas fa-edit icon-left"></i> Formulário de Edição</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('turmas.update', $turma->id_turma) }}">
                @csrf
                @method('PUT') 
                <div class="form-group">
                    <label for="serie">Série/Nome da Turma</label>
                    <input type="text" id="serie" name="serie" class="form-control" required value="{{ old('serie', $turma->serie) }}">
                </div>

                <div class="form-group">
                    <label for="ano_letivo">Ano Letivo</label>
                    <input type="number" id="ano_letivo" name="ano_letivo" class="form-control" required value="{{ old('ano_letivo', $turma->ano_letivo) }}" min="2000" max="2100">
                </div>

                <div class="form-group">
                    <label for="turno">Turno</label>
                    <select id="turno" name="turno" class="form-control" required>
                        <option value="manha" @selected(old('turno', $turma->turno) == 'manha')>Manhã</option>
                        <option value="tarde" @selected(old('turno', $turma->turno) == 'tarde')>Tarde</option>
                        <option value="noite" @selected(old('turno', $turma->turno) == 'noite')>Noite</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nivel_escolaridade">Nível de Escolaridade</label>
                    <select id="nivel_escolaridade" name="nivel_escolaridade" class="form-control" required>
                        <option value="fundamental_1" @selected(old('nivel_escolaridade', $turma->nivel_escolaridade) == 'fundamental_1')>Fundamental I</option>
                        <option value="fundamental_2" @selected(old('nivel_escolaridade', $turma->nivel_escolaridade) == 'fundamental_2')>Fundamental II</option>
                        <option value="medio" @selected(old('nivel_escolaridade', $turma->nivel_escolaridade) == 'medio')>Ensino Médio</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_escola">Escola</label>
                    <select id="id_escola" name="id_escola" class="form-control" required>
                        <option value="">Selecione a escola</option>
                        @foreach ($escolas as $escola)
                            <option value="{{ $escola->id_escola }}" @selected(old('id_escola', $turma->id_escola) == $escola->id_escola)>
                                {{ $escola->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary"><i class="fas fa-save icon-left-sm"></i> Salvar Alterações</button>
                    <a href="{{ route('turmas.index') }}" class="btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection