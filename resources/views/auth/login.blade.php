@extends('layouts.guest')

@section('title', 'Login')

@push('scripts')
    @vite('resources/js/password-toogle.js')
@endpush

@section('content')

<div class="box">
  @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
  @endif

  <h2>Entrar</h2>
  
  @error('email')
    <div class="alert alert-danger">
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
    <div class="password-wrapper">
      <input
        type="password"
        id="password"
        name="password"
        placeholder="Digite sua senha"
        required
      />
      <i class="fas fa-eye toggle-password"></i>
    </div>

    <button type="submit" class="btn">Entrar</button>
  </form>

  <p>
    Não tem uma conta?
    <a href="{{ route('register') }}">Cadastre-se</a>
  </p>
</div>

@endsection