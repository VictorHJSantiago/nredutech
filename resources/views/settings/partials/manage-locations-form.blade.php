<div class="config-group">
    <h2>Cadastro de Municípios/Instituições</h2>
    <form class="config-form" method="POST" action="{{-- route('settings.locations.store') --}}">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="novoMunicipio">Novo Município</label>
                <input type="text" id="novoMunicipio" name="municipio_nome" placeholder="Ex: Nova Cidade" required />
            </div>
            <div class="form-group">
                <label for="tipoMunicipio">Tipo de Município</label>
                <select id="tipoMunicipio" name="municipio_tipo">
                    <option value="">Selecione</option>
                    <option value="urbano">Urbano</option>
                    <option value="rural">Rural</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="novaInstituicao">Nova Instituição</label>
                <input type="text" id="novaInstituicao" name="instituicao_nome" placeholder="Ex: Escola Municipal X" required />
            </div>
            <div class="form-group">
                <label for="tipoInstituicao">Tipo de Instituição</label>
                <select id="tipoInstituicao" name="instituicao_tipo">
                    <option value="">Selecione</option>
                    <option value="colegio">Colégio Estadual</option>
                    <option value="escola-tecnica">Escola Técnica</option>
                    <option value="escola-municipal">Escola Municipal</option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Cadastrar</button>
        </div>
    </form>

    <div class="list-preview">
        <h3>Municípios Cadastrados:</h3>
        <ul class="preview-list">
            <li>Fernandes Pinheiro <button class="btn-edit-list">✏️</button> <button class="btn-delete-list">🗑️</button></li>
            <li>Guamiranga <button class="btn-edit-list">✏️</button> <button class="btn-delete-list">🗑️</button></li>
            <li>Irati <button class="btn-edit-list">✏️</button> <button class="btn-delete-list">🗑️</button></li>
        </ul>

        <h3>Instituições Cadastradas:</h3>
        <ul class="preview-list">
            <li>Colégio Estadual Irati <button class="btn-edit-list">✏️</button> <button class="btn-delete-list">🗑️</button></li>
            <li>Escola Técnica de Irati <button class="btn-edit-list">✏️</button> <button class="btn-delete-list">🗑️</button></li>
            <li>Colégio Estadual Inácio Martins <button class="btn-edit-list">✏️</button> <button class="btn-delete-list">🗑️</button></li>
        </ul>
    </div>
</div>