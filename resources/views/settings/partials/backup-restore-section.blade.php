@if(Auth::user()->tipo_usuario === 'administrador')
<section class="settings-card backup-restore-section">
    <div class="card-header">
        <h2 class="section-title">Backup e Restauração</h2>
        <p class="section-subtitle">
            Crie, baixe e restaure backups do banco de dados da aplicação.
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

        <form method="GET" action="{{ route('settings.backup.initiate') }}" class="mb-4">
            <p class="help-text">
                Clique no botão abaixo para gerar um backup instantâneo <strong>apenas do banco de dados</strong>.
            </p>
            <button type="submit" class="btn btn-primary">
                Executar Backup do Banco de Dados Agora
            </button>
        </form>

        <form method="POST" action="{{ route('settings.backup.restore') }}" enctype="multipart/form-data">
            @csrf
            <h3 class="section-subtitle-sm">Restaurar de um Arquivo</h3>
            <p class="help-text">
                Envie um arquivo <code>.sql</code> para restaurar o banco de dados. A senha será solicitada para confirmar.
                <br>
                <strong>Nota:</strong> Se você baixou um backup <code>.zip</code>, você deve <strong>extrair o arquivo <code>.sql</code></strong> de dentro dele primeiro (geralmente fica numa pasta <code>db-dumps</code>).
            </p>

            <div class="form-group">
                <label for="backup_file" class="form-label">Arquivo de Backup (.sql):</label>
                <input type="file" name="backup_file" id="backup_file" required accept=".sql" class="form-input">
            </div>

            <button type="submit" class="btn btn-danger"
                    onclick="return confirm('ATENÇÃO! Você tem certeza que deseja restaurar este backup? TODOS OS DADOS ATUAIS SERÃO APAGADOS E SUBSTITUÍDOS PERMANENTEMENTE. Esta ação não pode ser desfeita.')">
                Restaurar Banco de Dados
            </button>
        </form>

        <div class="backup-list-wrapper">
            <h3 class="section-subtitle-sm">Backups Salvos no Servidor</h3>

            @if(empty($backups) || count($backups) === 0)
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
                                        <a href="{{ route('settings.backup.download', ['filename' => $backup['name']]) }}" class="btn btn-secondary btn-sm">
                                            Baixar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</section>
@endif