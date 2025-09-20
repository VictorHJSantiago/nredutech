<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinição de Senha</title>
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
                <p>Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para a sua conta.</p>

                <div class="button-wrapper">
                    <a href="{{ $resetUrl }}" class="button">
                        Redefinir Senha
                    </a>
                </div>

                <p>Este link de redefinição de senha expirará em 60 minutos.</p>
                <p>Se você não solicitou uma redefinição de senha, nenhuma ação adicional é necessária.</p>
                <p>
                    Atenciosamente,<br>
                    Equipe NREduTech
                </p>
            </div>
        </div>
        <div class="footer">
            <p>Se estiver com problemas para clicar no botão "Redefinir Senha", copie e cole o URL abaixo no seu navegador:</p>
            <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
            <p>&copy; {{ date('Y') }} NREduTech. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>