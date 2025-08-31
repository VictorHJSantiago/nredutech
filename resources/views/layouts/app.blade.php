<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title', 'NREduTech')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <div class="sidebar-logo">
        <div class="logo-icon">📚</div>
        <span class="logo-text">NREduTech</span>
      </div>
      
      <nav class="sidebar-nav">
        <a href="{{ route('index') }}" class="nav-item {{ request()->routeIs('index') ? 'active' : '' }}">🏠 Início</a>
        {{-- NOVO LINK ADICIONADO AQUI --}}
        <a href="{{ route('escolas.index') }}" class="nav-item {{ request()->routeIs('escolas.*') ? 'active' : '' }}">🏫 Escolas</a>
        <a href="{{ route('componentes.index') }}" class="nav-item {{ request()->routeIs('componentes.*') ? 'active' : '' }}">📂 Disciplinas</a>
        <a href="{{ route('resources.index') }}" class="nav-item {{ request()->routeIs('resources.*') ? 'active' : '' }}">📖 Recursos</a>
        <a href="{{ route('usuarios.index') }}" class="nav-item {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">👥 Usuários</a>
        <a href="{{ route('agendamentos.index') }}" class="nav-item {{ request()->routeIs('agendamentos.*') ? 'active' : '' }}">📅 Agendamentos</a>
        <a href="{{ route('settings') }}" class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }}">⚙️ Configurações</a>
        
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
