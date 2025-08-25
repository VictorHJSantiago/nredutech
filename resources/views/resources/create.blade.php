@extends('layouts.app')

@section('title', 'Cadastrar Recurso – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Cadastrar Recurso Didático</h1>
        <p class="subtitle">Preencha os dados para cadastrar um novo recurso</p>
    </header>

    <section class="form-section">
        <form class="material-form" method="POST" action="{{ route('resources.store') }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label for="nome">Nome do Material</label>
                    <input type="text" id="nome" name="nome" placeholder="Ex: Projetor Multimídia" value="{{ old('nome') }}" required />
                    @error('nome')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="marca">Marca</label>
                    <input type="text" id="marca" name="marca" placeholder="Ex: Epson" value="{{ old('marca') }}" />
                    @error('marca')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="numero_serie">N.º de Série</label>
                    <input type="text" id="numero_serie" name="numero_serie" placeholder="Ex: SN12345678" value="{{ old('numero_serie') }}" />
                    @error('numero_serie')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="quantidade">Quantidade</label>
                    <input type="number" id="quantidade" name="quantidade" placeholder="Ex: 1" value="{{ old('quantidade', 1) }}" min="1" required />
                    @error('quantidade')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="funcionando" {{ old('status', 'funcionando') == 'funcionando' ? 'selected' : '' }}>Funcionando</option>
                        <option value="em_manutencao" {{ old('status') == 'em_manutencao' ? 'selected' : '' }}>Em manutenção</option>
                        <option value="quebrado" {{ old('status') == 'quebrado' ? 'selected' : '' }}>Quebrado</option>
                        <option value="descartado" {{ old('status') == 'descartado' ? 'selected' : '' }}>Descartado</option>
                    </select>
                    @error('status')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="data_ultima_limpeza">Data da Última Limpeza</label>
                    <input type="date" id="data_ultima_limpeza" name="data_ultima_limpeza" value="{{ old('data_ultima_limpeza') }}" />
                    @error('data_ultima_limpeza')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group full-width">
                    <label for="observacoes">Observações</label>
                    <textarea id="observacoes" name="observacoes" rows="3" placeholder="Qualquer detalhe adicional sobre o recurso">{{ old('observacoes') }}</textarea>
                    @error('observacoes')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('resources.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Salvar Cadastro</button>
            </div>
        </form>
    </section>
@endsection