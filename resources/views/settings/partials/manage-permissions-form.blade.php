<div class="config-group">
    <h2>Gerenciar Permissões de Usuários</h2>
    <form class="config-form" method="POST" action=""> 
        @csrf
        @method('PUT')
        <div class="form-group full-width">
            <label for="selecaoUsuario">Selecione um Usuário</label>
            <select id="selecaoUsuario" name="user_id" onchange="this.form.action = '/usuarios/' + this.value">
                <option value="">Escolha um usuário para editar</option>
                @foreach($usuarios as $user)
                    <option value="{{ $user->id_usuario }}">{{ $user->nome_completo }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="permissaoRole">Tipo de Usuário (Permissão)</label>
                <select id="permissaoRole" name="tipo_usuario">
                    <option value="">Selecione para alterar</option>
                    <option value="administrador">Administrador</option>
                    <option value="diretor">Diretor</option>
                    <option value="professor">Professor</option>
                </select>
            </div>
            <div class="form-group">
                <label for="statusUsuario">Status de Acesso</label>
                <select id="statusUsuario" name="status_aprovacao">
                    <option value="">Selecione para alterar</option>
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

<script>
    document.getElementById('selecaoUsuario').addEventListener('change', function() {
        if (this.value) {
            this.form.action = `{{ url('usuarios') }}/${this.value}`;
        } else {
            this.form.action = '';
        }
    });
</script>