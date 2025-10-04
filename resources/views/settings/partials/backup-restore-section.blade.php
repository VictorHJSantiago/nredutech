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
                Clique no botão abaixo para gerar um backup instantâneo <strong>do banco de dados e de todo o sistema</strong>.
            </p>
            <button type="submit" class="btn btn-info">
                Executar Backup Agora
            </button>
        </form>

        <div>
            <h3 class="section-subtitle-sm">Restaurar de um Arquivo</h3>
            <p class="help-text">
                Para restaurar o banco de dados a partir de um arquivo <code>.sql</code>, você precisará confirmar sua senha.
                <br>
                <strong>Nota:</strong> Esta ação é irreversível e substituirá todos os dados atuais.
            </p>
            <a href="{{ route('settings.backup.restore') }}" class="btn btn-danger">
                Iniciar Restauração
            </a>
        </div>

        <div class="backup-list-wrapper">
            <h3 class="section-subtitle-sm">Backups Salvos no Servidor</h3>

            @if(empty($backups) || $backups->isEmpty())
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
                <div class="pagination-container">
                    {{ $backups->links() }}
                </div>
            @endif
        </div>
    </div>
</section>
@endif