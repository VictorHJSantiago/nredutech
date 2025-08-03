<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title') - NREduTech</title>

  @vite(['resources/css/app.css', 'resources/css/login.css'])
</head>
<body>
  <div class="container">
    @yield('content')
  </div>
</body>
</html>