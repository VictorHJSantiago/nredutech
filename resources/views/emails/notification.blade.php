{{-- resources/views/emails/notification.blade.php --}}

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-text">
    <title>{{ $titulo ?? 'Notificação' }}</title>
</head>
<body>
    <h1>{{ $titulo ?? 'Nova Notificação' }}</h1>
    <p>
        {!! $mensagem ?? 'Você recebeu uma nova notificação.' !!}
    </p>
    <p>
        Atenciosamente,<br>
        Sistema NREduTech
    </p>
</body>
</html>