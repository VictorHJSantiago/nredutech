@extends('layouts.app')

@section('title', 'Cadastro de Disciplina – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Cadastro de Disciplina</h1>
        <p class="subtitle">Preencha os dados para cadastrar uma nova disciplina</p>
    </header>

    <section class="form-section">
        <form class="disciplina-form" method="POST" action="{{ route('disciplines.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="nomeDisciplina">Nome da Disciplina</label>
                    <input type="text" id="nomeDisciplina" name="nomeDisciplina" placeholder="Digite o nome da disciplina" value="{{ old('nomeDisciplina') }}" required />
                    @error('nomeDisciplina')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="descricaoDisciplina">Descrição</label>
                    <textarea id="descricaoDisciplina" name="descricaoDisciplina" rows="3" placeholder="Descreva brevemente a disciplina" required>{{ old('descricaoDisciplina') }}</textarea>
                    @error('descricaoDisciplina')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="horarioDisciplina">Horário</label>
                    <input type="text" id="horarioDisciplina" name="horarioDisciplina" placeholder="Ex: Seg/Qua 14:00–16:00" value="{{ old('horarioDisciplina') }}" required />
                    @error('horarioDisciplina')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="dataCadastro">Data de Cadastro</label>
                    <input type="date" id="dataCadastro" name="dataCadastro" value="{{ old('dataCadastro', date('Y-m-d')) }}" required />
                    @error('dataCadastro')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="instituicaoDisciplina">Instituição</label>
                    <select id="instituicaoDisciplina" name="instituicaoDisciplina" required>
                        <option value="" disabled selected>Selecione a instituição</option>
                        @foreach ($institutions as $institution)
                            <option value="{{ $institution->id }}" {{ old('instituicaoDisciplina') == $institution->id ? 'selected' : '' }}>
                                {{ $institution->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('instituicaoDisciplina')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="serieTurma">Série / Turma</label>
                    <input type="text" id="serieTurma" name="serieTurma" placeholder="Ex: 1º Ano Médio" value="{{ old('serieTurma') }}" required />
                    @error('serieTurma')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group full-width">
                    <label for="listaAlunos">Lista de Alunos (separados por vírgula)</label>
                    <textarea id="listaAlunos" name="listaAlunos" rows="3" placeholder="Ex: João Silva, Maria Souza, Pedro Santos">{{ old('listaAlunos') }}</textarea>
                    @error('listaAlunos')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="totalAlunos">Total de Alunos</label>
                    <input type="number" id="totalAlunos" name="totalAlunos" min="1" placeholder="Informe o total de alunos" value="{{ old('totalAlunos') }}" required />
                    @error('totalAlunos')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Cadastrar Disciplina</button>
            </div>
        </form>
    </section>
@endsection