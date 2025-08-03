{{-- resources/views/layouts/app.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  
  {{-- O título pode ser definido pelas views filhas --}}
  <title>@yield('title', 'NREduTech')</title>

  {{-- Usando o helper asset() para os arquivos CSS --}}
  <link rel="stylesheet" href="{{ asset('css/index.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/disciplinas.css') }}" />
  
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
    rel="stylesheet"
  />
</head>
<body>
  <div class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-icon">📚</div>
      <span class="logo-text">NREduTech</span>
    </div>
    

<nav class="sidebar-nav">
  <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">🏠 Início</a>
  <a href="{{ route('disciplines.index') }}" class="nav-item {{ request()->routeIs('disciplines.*') ? 'active' : '' }}">📂 Disciplinas</a>
  <a href="{{ route('professors.index') }}" class="nav-item {{ request()->routeIs('professors.*') ? 'active' : '' }}">👩‍🏫 Professores</a>
  <a href="{{ route('resources.index') }}" class="nav-item {{ request()->routeIs('resources.*') ? 'active' : '' }}">📖 Recursos</a>
  <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">👥 Usuários</a>
  <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">📊 Relatórios</a>
  <a href="{{ route('laboratories.index') }}" class="nav-item {{ request()->routeIs('laboratories.*') ? 'active' : '' }}">🔬 Laboratórios</a>
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
  </div>

  <div class="main-content">
    @yield('content')
  </div>
</body>
</html>