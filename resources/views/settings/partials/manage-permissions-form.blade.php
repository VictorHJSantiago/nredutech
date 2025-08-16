<div class="config-group">
    <h2>Gerenciar Permissões de Usuários</h2>
    <form class="config-form" method="POST" action="{{-- route('settings.permissions.update') --}}">
        @csrf
        @method('PATCH')
        <div class="form-group full-width">
            <label for="selecaoUsuario">Selecione Usuário</label>
            <select id="selecaoUsuario" name="user_id">
                <option value="">Escolha um usuário</option>
                <option value="usuario1">victorhenriquedejesussantiago@gmail.com</option>
                <option value="usuario2">thalitascharrrodriguespimenta@escola.pr.gov.br</option>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="permissaoRole">Permissão (Role)</label>
                <select id="permissaoRole" name="role">
                    <option value="">Selecione</option>
                    <option value="admin">Administrador</option>
                    <option value="gestor">Gestor</option>
                    <option value="professor">Professor</option>
                    <option value="aluno">Aluno</option>
                </select>
            </div>
            <div class="form-group">
                <label for="statusUsuario">Status de Acesso</label>
                <select id="statusUsuario" name="status">
                    <option value="">Selecione</option>
                    <option value="ativo">Ativo</option>
                    <option value="bloqueado">Bloqueado</option>
                    <option value="pendente">Pendente</option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Salvar Permissão</button>
        </div>
    </form>
</div>