
@extends('layouts.guest')

@section('title', 'Login')

@section('content')

<div class="box">
  <h2>Entrar</h2>
      @error('email')
        <div class="alert alert-danger" style="color: red; margin-bottom: 15px;">
            {{ $message }}
        </div>
      @enderror
  <form method="POST" action="{{ route('login') }}">
    @csrf  

    <label for="email">E-mail</label>
    <input
      type="email"
      id="email"
      name="email"
      placeholder="Digite seu e-mail"
      value="{{ old('email') }}"
      required
      autofocus
    />

    <label for="password">Senha</label>
    <input
      type="password"
      id="password"
      name="password"
      placeholder="Digite sua senha"
      required
    />

    <button type="submit" class="btn">Entrar</button>
  </form>

  <p>
    Não tem uma conta?
    <a href="{{ route('register') }}">Cadastre-se</a>
  </p>
</div>

@endsection
