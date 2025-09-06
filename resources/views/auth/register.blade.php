@extends('layouts.guest')

@section('title', 'Cadastro')

@section('content')
<div class="box register-box">
  <h2>Cadastrar</h2>

  <form method="POST" action="{{ route('register') }}" class="register-form">
    @csrf

    <div class="form-grid">
      
      <div class="form-group">
        <label for="nome_completo">Nome Completo</label>
        <input type="text" id="nome_completo" name="nome_completo" placeholder="Digite seu nome completo" value="{{ old('nome_completo') }}" required autofocus />
        @error('nome_completo')<span class="error-message">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="data_nascimento">Data de Nascimento</label>
        <input type="date" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}" required />
        @error('data_nascimento')<span class="error-message">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" value="{{ old('cpf') }}" required />
        @error('cpf')<span class="error-message">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="rg">RG</label>
        <input type="text" id="rg" name="rg" placeholder="Digite seu RG" value="{{ old('rg') }}" required />
        @error('rg')<span class="error-message">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="seu.email@dominio.com" value="{{ old('email') }}" required />
        @error('email')<span class="error-message">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="telefone">Telefone</label>
        <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" value="{{ old('telefone') }}" required />
        @error('telefone')<span class="error-message">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="rco_siape">Registro (SIAPE ou RCO)</label>
        <input type="text" id="rco_siape" name="rco_siape" placeholder="Ex: SIAPE123456" value="{{ old('rco_siape') }}" required />
        @error('rco_siape')<span class="error-message">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="formacao">Formação</label>
        <input type="text" id="formacao" name="formacao" placeholder="Ex: Licenciatura em Física" value="{{ old('formacao') }}" required />
        @error('formacao')<span class="error-message">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="area_formacao">Área de Formação</label>
        <input type="text" id="area_formacao" name="area_formacao" placeholder="Ex: Ciências Exatas" value="{{ old('area_formacao') }}" required />
        @error('area_formacao')<span class="error-message">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="username">Usuário</label>
        <input type="text" id="username" name="username" placeholder="Escolha um nome de usuário" value="{{ old('username') }}" required />
        @error('username')<span class="error-message">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="password">Senha</label>
        <input type="password" id="password" name="password" placeholder="Crie uma senha" required />
        @error('password')<span class="error-message">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label for="password_confirmation">Confirmar Senha</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirme a senha" required />
        @error('password_confirmation')<span class="error-message">{{ $message }}</span>@enderror
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn submit-btn">Cadastrar</button>
    </div>
  </form>
  <p class="login-link">
    Já tem uma conta?
    <a href="{{ route('login') }}">Entrar</a>
  </p>
</div>
@endsection
