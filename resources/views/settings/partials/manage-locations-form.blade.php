<div class="config-group">
    <h2>Cadastro de Municípios e Instituições</h2>
    <form class="config-form" method="POST" action="{{ route('municipios.store') }}" style="margin-bottom: 2rem;">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="novoMunicipio">Novo Município</label>
                <input type="text" id="novoMunicipio" name="nome" placeholder="Ex: Nova Cidade" required value="{{ old('nome') }}" />
                 @error('nome')<span class="error-message" style="color:red; font-size: 0.8em;">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="tipoMunicipio">Tipo de Município</label>
                <select id="tipoMunicipio" name="tipo" required>
                    <option value="" disabled selected>Selecione</option>
                    <option value="urbano" {{ old('tipo') == 'urbano' ? 'selected' : '' }}>Urbano</option>
                    <option value="rural" {{ old('tipo') == 'rural' ? 'selected' : '' }}>Rural</option>
                </select>
                @error('tipo')<span class="error-message" style="color:red; font-size: 0.8em;">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Cadastrar Município</button>
        </div>
    </form>

    <div class="list-preview">
        <h3>Municípios Cadastrados ({{ $municipios->count() }}):</h3>
        <ul class="preview-list">
            @forelse($municipios as $municipio)
                <li>
                    <span>{{ $municipio->nome }} ({{ ucfirst($municipio->tipo) }})</span>
                </li>
            @empty
                <li>Nenhum município cadastrado.</li>
            @endforelse
        </ul>

        <h3>Instituições Cadastradas ({{ $escolas->count() }}):</h3>
        <ul class="preview-list">
             @forelse($escolas as $escola)
                <li>
                    <span>{{ $escola->nome }} - {{ $escola->municipio->nome ?? 'N/A' }}</span>
                </li>
            @empty
                <li>Nenhuma instituição cadastrada.</li>
            @endforelse
        </ul>
    </div>
</div>