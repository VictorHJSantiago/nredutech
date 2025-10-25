@if(Auth::user()->tipo_usuario === 'administrador')
<section class="settings-card backup-restore-section">
    <div class="card-header">
        <h2 class="section-title">Backup e Restauração</h2>
        <p class="section-subtitle">
            Crie, baixe e restaure backups do banco de dados e arquivos da aplicação.
        </p>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-3">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <strong>Ocorreram erros:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-4 border-bottom pb-4">
            <h3 class="section-subtitle-sm">Agendamento Automático de Backup</h3>
            <form method="POST" action="{{ route('settings.backup.schedule.update') }}">
                @csrf
                @method('PATCH')
                <div class="form-group mb-3">
                    <label for="backup_frequency" class="form-label">Frequência do Backup Automático:</label>
                    <select id="backup_frequency" name="backup_frequency" class="form-input">
                        <option value="daily" @selected(old('backup_frequency', $backupFrequency ?? 'daily') == 'daily')>Diário (madrugada, ~02:00)</option>
                        <option value="weekly" @selected(old('backup_frequency', $backupFrequency ?? 'daily') == 'weekly')>Semanal (Domingo, madrugada, ~02:00)</option>
                    </select>
                    @error('backup_frequency')<span class="text-danger" style="font-size: 0.8em; color: red;">{{ $message }}</span>@enderror
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Salvar Agendamento</button>
            </form>
             <p class="help-text mt-2">
                O backup automático inclui banco de dados e arquivos do sistema. As notificações de sucesso/falha serão enviadas aos administradores. O horário exato pode variar ligeiramente dependendo da carga do servidor.
            </p>
        </div>

        <form method="GET" action="{{ route('settings.backup.initiate') }}" class="mb-4">
            <h3 class="section-subtitle-sm">Backup Manual</h3>
            <p class="help-text">
                Clique no botão abaixo para gerar um backup instantâneo <strong>do banco de dados e de todo o sistema</strong>. Você precisará confirmar sua senha.
            </p>
            <button type="submit" class="btn btn-info">
                 <i class="fas fa-play icon-left-sm"></i> Executar Backup Manual Agora
            </button>
        </form>

        <div class="mt-4 border-t pt-4">
            <h3 class="section-subtitle-sm">Restaurar de um Arquivo</h3>
            <p class="help-text">
                Para restaurar o sistema a partir de um arquivo <code>.sql</code> (contido no <code>.zip</code> do backup), você precisará confirmar sua senha.
                <br>
                <strong>Atenção:</strong> Esta ação é irreversível e substituirá todos os dados atuais do banco de dados. Os arquivos do sistema não são restaurados por este método.
            </p>
            <a href="{{ route('settings.backup.restore') }}" class="btn btn-danger">
                 <i class="fas fa-history icon-left-sm"></i> Iniciar Restauração
            </a>
        </div>
        <div class="backup-list-wrapper mt-4 border-t pt-4">
            <h3 class="section-subtitle-sm">Backups Salvos no Servidor</h3>

            @if(!isset($backups) || $backups->isEmpty())
                <p class="help-text">Nenhum backup encontrado no servidor.</p>
            @else
                <div class="table-responsive">
                    <table class="backup-list table">
                        <thead>
                            <tr>
                                <th>Arquivo (Nome do Zip)</th>
                                <th>Data</th>
                                <th>Tamanho</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                                <tr>
                                    <td>{{ $backup['name'] }}</td>
                                    <td>{{ $backup['date'] }}</td>
                                    <td>{{ $backup['size'] }}</td>
                                    <td>
                                        <a href="{{ route('settings.backup.download', ['filename' => $backup['name']]) }}" class="btn btn-download">
                                            <i class="fas fa-download"></i> Baixar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($backups->hasPages())
                 <div class="pagination-container" style="margin-top: 1rem;">
                    {{ $backups->links() }}
                 </div>
                @endif
            @endif
        </div>
    </div>
</section>
@endif