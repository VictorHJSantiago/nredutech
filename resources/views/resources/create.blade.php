@extends('layouts.app')

@section('title', 'Cadastrar Recurso ou Laboratório – NREduTech')

@section('content') 
    <section class="form-section">
        <form class="material-form" method="POST" action="{{ route('resources.store') }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label for="nome">Nome do Item</label>
                    <input type="text" id="nome" name="nome" placeholder="Ex: Projetor Multimídia ou Laboratório de Química" value="{{ old('nome') }}" required />
                    @error('nome')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                 <div class="form-group">
                    <label for="tipo">Tipo de Item</label>
                    <select id="tipo" name="tipo" required>
                        <option value="didatico" {{ old('tipo', 'didatico') == 'didatico' ? 'selected' : '' }}>Recurso Didático</option>
                        <option value="laboratorio" {{ old('tipo') == 'laboratorio' ? 'selected' : '' }}>Laboratório</option>
                    </select>
                    @error('tipo')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="marca">Marca</label>
                    <input type="text" id="marca" name="marca" placeholder="Ex: Epson" value="{{ old('marca') }}" />
                    @error('marca')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="numero_serie">N.º de Série / Patrimônio</label>
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
                    <label for="data_aquisicao">Data de Aquisição</label>
                    <input type="date" id="data_aquisicao" name="data_aquisicao" value="{{ old('data_aquisicao') }}" />
                    @error('data_aquisicao')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                @if(Auth::user()->tipo_usuario === 'administrador')
                <div class="form-group">
                    <label for="id_escola">Associar à Escola (Opcional)</label>
                    <select id="id_escola" name="id_escola">
                        <option value="">Global (Todas as Escolas)</option>
                        @foreach($escolas as $escola)
                            <option value="{{ $escola->id_escola }}" {{ old('id_escola') == $escola->id_escola ? 'selected' : '' }}>
                                {{ $escola->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_escola')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                @endif
                <div class="form-group full-width">
                    <label for="observacoes">Observações</label>
                    <textarea id="observacoes" name="observacoes" rows="3" placeholder="Qualquer detalhe adicional sobre o item">{{ old('observacoes') }}</textarea>
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