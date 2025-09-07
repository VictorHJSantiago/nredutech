@extends('layouts.app')

@section('title', 'Editar Recurso ou Laboratório – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Editar: {{ $recursoDidatico->nome }}</h1>
        <p class="subtitle">Altere os dados do item selecionado</p>
    </header>

    <section class="form-section">
        <form class="material-form" method="POST" action="{{ route('resources.update', $recursoDidatico) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="nome">Nome do Item</label>
                    <input type="text" id="nome" name="nome" value="{{ old('nome', $recursoDidatico->nome) }}" required />
                    @error('nome')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="tipo">Tipo de Item</label>
                    <select id="tipo" name="tipo" required>
                        <option value="didatico" {{ old('tipo', $recursoDidatico->tipo) == 'didatico' ? 'selected' : '' }}>Recurso Didático</option>
                        <option value="laboratorio" {{ old('tipo', $recursoDidatico->tipo) == 'laboratorio' ? 'selected' : '' }}>Laboratório</option>
                    </select>
                    @error('tipo')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="marca">Marca</label>
                    <input type="text" id="marca" name="marca" value="{{ old('marca', $recursoDidatico->marca) }}" />
                    @error('marca')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="numero_serie">Número de Série / Patrimônio</label>
                    <input type="text" id="numero_serie" name="numero_serie" value="{{ old('numero_serie', $recursoDidatico->numero_serie) }}" />
                    @error('numero_serie')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="quantidade">Quantidade</label>
                    <input type="number" id="quantidade" name="quantidade" value="{{ old('quantidade', $recursoDidatico->quantidade) }}" required min="1" />
                    @error('quantidade')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="funcionando" {{ old('status', $recursoDidatico->status) == 'funcionando' ? 'selected' : '' }}>Funcionando</option>
                        <option value="em_manutencao" {{ old('status', $recursoDidatico->status) == 'em_manutencao' ? 'selected' : '' }}>Em Manutenção</option>
                        <option value="quebrado" {{ old('status', $recursoDidatico->status) == 'quebrado' ? 'selected' : '' }}>Quebrado</option>
                        <option value="descartado" {{ old('status', $recursoDidatico->status) == 'descartado' ? 'selected' : '' }}>Descartado</option>
                    </select>
                    @error('status')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                 <div class="form-group">
                    <label for="data_aquisicao">Data de Aquisição</label>
                    <input type="date" id="data_aquisicao" name="data_aquisicao" value="{{ old('data_aquisicao', $recursoDidatico->data_aquisicao) }}" />
                    @error('data_aquisicao')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                 <div class="form-group full-width">
                    <label for="observacoes">Observações</label>
                    <textarea id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $recursoDidatico->observacoes) }}</textarea>
                    @error('observacoes')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions-footer">
                <button type="submit" class="btn-primary">Salvar Alterações</button>
                <a href="{{ route('resources.index') }}" class="btn-secondary">Cancelar</a>
            </div>
        </form>
    </section>
@endsection
