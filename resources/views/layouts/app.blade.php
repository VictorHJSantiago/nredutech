<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title', 'NREduTech')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
</head>
<body>
  @php
    $userType = Auth::user()->tipo_usuario ?? null;
  @endphp
  <div class="container">
    <aside class="sidebar">
      <div class="sidebar-logo">
        <div class="logo-icon">📚</div>
        <span class="logo-text">NREduTech</span>
      </div>
      
      <nav class="sidebar-nav">
        <a href="{{ route('index') }}" class="nav-item {{ request()->routeIs('index') ? 'active' : '' }}">🏠 Início</a>
        
        @if ($userType != 'professor') 
          <a href="{{ route('escolas.index') }}" class="nav-item {{ request()->routeIs('escolas.*') ? 'active' : '' }}">🏫 Escolas</a>
          <a href="{{ route('turmas.index') }}" class="nav-item {{ request()->routeIs('turmas.*') ? 'active' : '' }}">👨‍🎓 Turmas</a>
        @endif

        <a href="{{ route('componentes.index') }}" class="nav-item {{ request()->routeIs('componentes.*') ? 'active' : '' }}">📂 Disciplinas</a>
        <a href="{{ route('resources.index') }}" class="nav-item {{ request()->routeIs('resources.*') ? 'active' : '' }}">📖 Recursos</a>

        @if ($userType != 'professor') 
          <a href="{{ route('usuarios.index') }}" class="nav-item {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">👥 Usuários</a>
        @endif
        
        <a href="{{ route('agendamentos.index') }}" class="nav-item {{ request()->routeIs('agendamentos.*') ? 'active' : '' }}">📅 Agendamentos</a>
        
        @if ($userType == 'administrador' || $userType == 'diretor')
            <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">📊 Relatórios</a>
        @endif

        <a href="{{ route('notifications.index') }}" class="nav-item {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
            <span>🔔 Notificações</span>
            @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                <span class="notification-badge">{{ $unreadNotificationsCount }}</span>
            @endif
        </a>
        
        @if ($userType == 'administrador') 
          <a href="{{ route('settings') }}" class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }}">⚙️ Configurações</a>
        @endif
        
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <a href="{{ route('logout') }}" 
            class="nav-item logout" 
            onclick="event.preventDefault(); this.closest('form').submit();">
            🔒 Sair
          </a>
        </form>
      </nav>
    </aside>

    <main class="main-content">
      @yield('content')
    </main>
  </div>
  @stack('scripts')
</body>
</html>