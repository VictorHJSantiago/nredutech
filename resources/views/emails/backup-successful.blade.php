<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Realizado com Sucesso</title>
    <style>
        {!! file_get_contents(resource_path('css/email.css')) !!}
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="header">
                <a href="{{ url('/') }}" class="logo">
                    <span class="logo-icon">📚</span> NREduTech
                </a>
            </div>

            <div class="content">
                <h1>Olá, {{ $userName }}!</h1>
                <p>Boas notícias! Um novo backup da aplicação foi criado com sucesso e armazenado de forma segura.</p>

                <div style="background-color: #f8f9fa; border-radius: 5px; padding: 15px; margin: 20px 0; font-size: 16px;">
                    <p style="margin: 8px 0;"><strong>Aplicação:</strong> {{ $appName ?? 'N/A' }}</p>
                    <p style="margin: 8px 0;"><strong>Nome do Backup:</strong> {{ $backupName ?? 'N/A' }}</p>
                    <p style="margin: 8px 0;"><strong>Disco de Armazenamento:</strong> {{ $diskName ?? 'N/A' }}</p>
                    <p style="margin: 8px 0;"><strong>Executado por:</strong> {{ $backupInitiatedBy ?? 'Sistema' }}</p>
                    <hr style="border: 0; border-top: 1px solid #e8e5ef; margin: 15px 0;">
                    <p style="margin: 8px 0;"><strong>Tamanho do Arquivo:</strong> {{ $latestBackupSize ?? 'N/A' }}</p>
                    <p style="margin: 8px 0;"><strong>Total de Backups:</strong> {{ $backupCount ?? 'N/A' }}</p>
                    <p style="margin: 8px 0;"><strong>Armazenamento Utilizado:</strong> {{ $totalStorageUsed ?? 'N/A' }}</p>
                    <hr style="border: 0; border-top: 1px solid #e8e5ef; margin: 15px 0;">
                    <p style="margin: 8px 0;"><strong>Data do Backup Mais Recente:</strong> {{ $latestBackupDate ?? 'N/A' }}</p>
                    <p style="margin: 8px 0;"><strong>Data do Backup Mais Antigo:</strong> {{ $oldestBackupDate ?? 'N/A' }}</p>
                </div>

                <p>Nenhuma ação é necessária de sua parte. Esta é apenas uma notificação para mantê-lo informado.</p>

                <div class="button-wrapper">
                    <a href="{{ route('settings') }}" class="button">
                        Gerenciar Backups
                    </a>
                </div>

                <p>
                    Atenciosamente,<br>
                    Equipe NREduTech
                </p>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} NREduTech. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>