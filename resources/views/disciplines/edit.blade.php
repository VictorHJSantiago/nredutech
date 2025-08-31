@extends('layouts.app')

@section('title', 'Editar Componente Curricular – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Editar Componente Curricular</h1>
        <p class="subtitle">Altere os dados do componente selecionado</p>
    </header>

    <section class="form-section">
        <form class="disciplina-form" method="POST" action="{{ route('componentes.update', $componenteCurricular) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="nome">Nome do Componente</label>
                    <input type="text" id="nome" name="nome" value="{{ old('nome', $componenteCurricular->nome) }}" required />
                    @error('nome')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="carga_horaria">Carga Horária</label>
                    <input type="text" id="carga_horaria" name="carga_horaria" value="{{ old('carga_horaria', $componenteCurricular->carga_horaria) }}" required />
                    @error('carga_horaria')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group full-width">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4">{{ old('descricao', $componenteCurricular->descricao) }}</textarea>
                    @error('descricao')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="pendente" {{ old('status', $componenteCurricular->status) == 'pendente' ? 'selected' : '' }}>
                            Pendente
                        </option>
                        <option value="aprovado" {{ old('status', $componenteCurricular->status) == 'aprovado' ? 'selected' : '' }}>
                            Aprovado
                        </option>
                        <option value="reprovado" {{ old('status', $componenteCurricular->status) == 'reprovado' ? 'selected' : '' }}>
                            Reprovado
                        </option>
                    </select>
                    @error('status')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Salvar Alterações</button>
                <a href="{{ route('componentes.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </section>
@endsection
