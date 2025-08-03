<div class="config-group">
    <h2>Backup e Restaurar</h2>
    <div class="backup-container">
        <p>Realize backups periódicos do banco de dados e arquivos do sistema.</p>
        <div class="backup-actions">
            <form method="POST" action="{{-- route('settings.backup.run') --}}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-secondary">Fazer Backup Agora</button>
            </form>
            
            <form method="POST" action="{{-- route('settings.backup.restore') --}}" enctype="multipart/form-data" style="display: inline;">
                @csrf
                <input type="file" id="arquivoBackup" name="backup_file" accept=".zip,.sql" />
                <button type="submit" class="btn-secondary">Restaurar Backup</button>
            </form>
        </div>
    </div>
</div>