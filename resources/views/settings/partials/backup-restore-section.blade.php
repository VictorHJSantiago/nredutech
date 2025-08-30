<div class="config-group">
    <h2>Backup e Restauração</h2>
    <div class="backup-container">
        <p>Realize backups periódicos do banco de dados do sistema. A restauração deve ser feita manualmente por um administrador do sistema por segurança.</p>
        <div class="backup-actions">
            <form method="POST" action="{{ route('settings.backup.run') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-secondary">Fazer Backup Agora</button>
            </form>
        </div>
    </div>
</div>