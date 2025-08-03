<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'NREduTech')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">📚</div>
            <span class="logo-text">NREduTech</span>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-item">🏠 Início</a>
            <a href="{{ route('disciplines.index') }}" class="nav-item">📂 Disciplinas</a>
            <a href="{{ route('professors.index') }}" class="nav-item">👩‍🏫 Professores</a>
            <a href="{{ route('resources.index') }}" class="nav-item">📖 Recursos</a>
            <a href="{{ route('users.index') }}" class="nav-item">👥 Usuários</a>
            <a href="{{ route('reports.index') }}" class="nav-item">📊 Relatórios</a>
            <a href="{{ route('laboratories.index') }}" class="nav-item">🔬 Laboratórios</a>
            <a href="{{ route('settings') }}" class="nav-item">⚙️ Configurações</a>
            <a href="#" class="nav-item logout">🔒 Sair</a> {{-- A ação de sair geralmente é um formulário POST --}}
        </nav>
    </div>

    <div class="main-content">
        @yield('content')
    </div>
</body>
</html>