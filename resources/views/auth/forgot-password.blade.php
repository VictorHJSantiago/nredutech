@extends('layouts.guest')

@section('title', 'Redefinir Senha')

@section('content')

<div class="box">
  <h2>Redefinir Senha</h2>
  
  <p class="info-text">
    Esqueceu sua senha? Sem problemas. Apenas nos informe seu e-mail e enviaremos um link para você criar uma nova.
  </p>

  @if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      @foreach ($errors->all() as $error)
        {{ $error }}
      @endforeach
    </div>
  @endif


  <form method="POST" action="{{ route('password.email') }}">
    @csrf  

    <label for="email">E-mail</label>
    <input
      type="email"
      id="email"
      name="email"
      placeholder="Digite seu e-mail de cadastro"
      value="{{ old('email') }}"
      required
      autofocus
    />

    <button type="submit" class="btn">Enviar Link de Redefinição</button>
  </form>

  <p>
    Lembrou a senha?
    <a href="{{ route('login') }}">Voltar para o login</a>
  </p>
</div>

@endsection