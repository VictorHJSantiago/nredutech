@extends('layouts.app')

@section('title', 'Editar Disciplina – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Editar Disciplina</h1>
        <p class="subtitle">Altere os dados da disciplina selecionada</p>
    </header>

    <section class="form-section">
        <form class="disciplina-form" method="POST" action="{{ route('disciplines.update', $discipline) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="nomeDisciplina">Nome da Disciplina</label>
                    <input type="text" id="nomeDisciplina" name="nomeDisciplina" value="{{ old('nomeDisciplina', $discipline->name) }}" required />
                    @error('nomeDisciplina')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="descricaoDisciplina">Descrição</label>
                    <textarea id="descricaoDisciplina" name="descricaoDisciplina" rows="3" required>{{ old('descricaoDisciplina', $discipline->description) }}</textarea>
                    @error('descricaoDisciplina')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="horarioDisciplina">Horário</label>
                    <input type="text" id="horarioDisciplina" name="horarioDisciplina" value="{{ old('horarioDisciplina', $discipline->schedule) }}" required />
                    @error('horarioDisciplina')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="dataCadastro">Data de Cadastro</label>
                    <input type="date" id="dataCadastro" name="dataCadastro" value="{{ old('dataCadastro', $discipline->created_at->format('Y-m-d')) }}" required />
                    @error('dataCadastro')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="instituicaoDisciplina">Instituição</label>
                    <select id="instituicaoDisciplina" name="instituicaoDisciplina" required>
                        <option value="" disabled>Selecione a instituição</option>
                        @foreach ($institutions as $institution)
                            <option value="{{ $institution->id }}" {{ old('instituicaoDisciplina', $discipline->institution_id) == $institution->id ? 'selected' : '' }}>
                                {{ $institution->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('instituicaoDisciplina')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="serieTurma">Série / Turma</label>
                    <input type="text" id="serieTurma" name="serieTurma" value="{{ old('serieTurma', $discipline->class_level) }}" required />
                    @error('serieTurma')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group full-width">
                    <label for="listaAlunos">Lista de Alunos (separados por vírgula)</label>
                    <textarea id="listaAlunos" name="listaAlunos" rows="3">{{ old('listaAlunos', $discipline->student_list) }}</textarea>
                    @error('listaAlunos')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="totalAlunos">Total de Alunos</label>
                    <input type="number" id="totalAlunos" name="totalAlunos" value="{{ old('totalAlunos', $discipline->total_students) }}" required />
                    @error('totalAlunos')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </section>
@endsection