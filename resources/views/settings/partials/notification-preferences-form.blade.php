<div class="config-group">
    <h2>Preferências de Notificação e Tema</h2>
    <form class="config-form" method="POST" action="{{ route('settings.preferences.update') }}">
        @csrf
        @method('PATCH')

        <div class="form-group-checkbox">
            <input type="hidden" name="notif_email" value="0">
            <input type="checkbox" id="notifEmail" name="notif_email" value="1" {{ old('notif_email', $preferencias->notif_email ?? false) ? 'checked' : '' }} />
            <label for="notifEmail">Receber notificação por e-mail</label>
        </div>
        <div class="form-group-checkbox">
            <input type="hidden" name="notif_popup" value="0">
            <input type="checkbox" id="notifPopup" name="notif_popup" value="1" {{ old('notif_popup', $preferencias->notif_popup ?? false) ? 'checked' : '' }} />
            <label for="notifPopup">Mostrar notificações em pop-up</label>
        </div>

        <div class="form-group">
            <label for="temaSelecionado">Escolha o Tema</label>
            <select id="temaSelecionado" name="tema">
                <option value="claro" {{ old('tema', $preferencias->tema ?? 'claro') == 'claro' ? 'selected' : '' }}>Claro</option>
                <option value="escuro" {{ old('tema', $preferencias->tema ?? 'claro') == 'escuro' ? 'selected' : '' }}>Escuro</option>
            </select>
        </div>
        <div class="form-group">
            <label for="tamanhoFonte">Tamanho da Fonte</label>
            <select id="tamanhoFonte" name="tamanho_fonte">
                <option value="padrao" {{ old('tamanho_fonte', $preferencias->tamanho_fonte ?? 'padrao') == 'padrao' ? 'selected' : '' }}>Padrão</option>
                <option value="medio" {{ old('tamanho_fonte', $preferencias->tamanho_fonte ?? 'padrao') == 'medio' ? 'selected' : '' }}>Médio</option>
                <option value="grande" {{ old('tamanho_fonte', $preferencias->tamanho_fonte ?? 'padrao') == 'grande' ? 'selected' : '' }}>Grande</option>
            </select>
        </div>

        <div class="form-actions" style="flex-basis: 100%; text-align: left; margin-top: 1rem;">
            <button type="submit" class="btn-primary">Salvar Preferências</button>
        </div>
    </form>
</div>