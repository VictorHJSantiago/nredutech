{{-- resources/views/layouts/app.blade.php --}}

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

</head>
<body class="bg-gray-100 font-sans">
  <div class="flex h-screen">
    <aside class="w-64 bg-white shadow-md">
      <div class="sidebar-logo p-4 border-b">
        <div class="flex items-center">
            <div class="logo-icon text-2xl mr-2">📚</div>
            <span class="logo-text text-xl font-bold text-gray-700">NREduTech</span>
        </div>
      </div>
      
      <nav class="sidebar-nav p-2">
        <a href="{{ route('index') }}" class="nav-item {{ request()->routeIs('index') ? 'active' : '' }}">🏠 Início</a>
        <a href="{{ route('discipline-list') }}" class="nav-item {{ request()->routeIs('discipline.*') ? 'active' : '' }}">📂 Disciplinas</a>
        <a href="{{ route('professor-list') }}" class="nav-item {{ request()->routeIs('professor.*') ? 'active' : '' }}">👩‍🏫 Professores</a>
        <a href="{{ route('resource-list') }}" class="nav-item {{ request()->routeIs('resource.*') ? 'active' : '' }}">📖 Recursos</a>
        <a href="{{ route('user-list') }}" class="nav-item {{ request()->routeIs('user.*') ? 'active' : '' }}">👥 Usuários</a>
        <a href="{{ route('reports') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">📊 Relatórios</a>
        <a href="{{ route('laboratory-list') }}" class="nav-item {{ request()->routeIs('laboratory.*') ? 'active' : '' }}">🔬 Laboratórios</a>
        <a href="{{ route('settings') }}" class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }}">⚙️ Configurações</a>
        
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
          @csrf
          <a href="{{ route('logout') }}" 
            class="nav-item logout" 
            onclick="event.preventDefault(); this.closest('form').submit();">
            🔒 Sair
          </a>
        </form>
      </nav>
    </aside>

    <main class="main-content flex-1 p-8 overflow-y-auto">
      @yield('content')
    </main>
  </div>
</body>
</html>
