@extends('layouts.app')

@section('title', 'Cadastro de Componente Curricular – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Cadastro de Componente Curricular</h1>
        <p class="subtitle">Preencha os dados para cadastrar um novo componente</p>
    </header>

    <section class="form-section">
        <form class="disciplina-form" method="POST" action="{{ route('componentes.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="nome">Nome do Componente</label>
                    <input type="text" id="nome" name="nome" placeholder="Ex: Cálculo I" value="{{ old('nome') }}" required />
                    @error('nome')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="carga_horaria">Carga Horária</label>
                    <input type="text" id="carga_horaria" name="carga_horaria" placeholder="Ex: 60h" value="{{ old('carga_horaria') }}" required />
                    @error('carga_horaria')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group full-width">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4" placeholder="Descreva brevemente o componente curricular">{{ old('descricao') }}</textarea>
                    @error('descricao')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                @if(Auth::user()->tipo_usuario === 'administrador')
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="pendente" {{ old('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="aprovado" {{ old('status') == 'aprovado' ? 'selected' : '' }}>Aprovado</option>
                            <option value="reprovado" {{ old('status') == 'reprovado' ? 'selected' : '' }}>Reprovado</option>
                        </select>
                        @error('status')<span class="error-message">{{ $message }}</span>@enderror
                    </div>
                @else
                    <input type="hidden" name="status" value="pendente">
                @endif
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Cadastrar Componente</button>
                <a href="{{ route('componentes.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </section>
@endsection