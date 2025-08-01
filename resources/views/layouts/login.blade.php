{{-- Usa o esqueleto definido no layout 'guest' --}}
@extends('layouts.guest')

{{-- Define o título específico desta página --}}
@section('title', 'Login')

{{-- Define o bloco de conteúdo que será inserido no layout --}}
@section('content')
<div class="box">
    <h2>Entrar</h2>

    {{-- Formulário corrigido para o padrão Laravel --}}
    <form method="POST" action="{{ route('login') }}">
        {{-- Token de segurança CSRF, obrigatório para formulários POST --}}
        @csrf

        <label for="email">E-mail</label>
        <input
            type="email"
            id="email"
            name="email"
            placeholder="Digite seu e-mail"
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
        {{-- O link agora aponta para a rota nomeada 'register' --}}
        <a href="{{ route('register') }}">Cadastre-se</a>
    </p>
</div>
@endsection