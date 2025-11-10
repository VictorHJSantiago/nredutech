@extends('layouts.guest')

@section('title', 'Cadastro')

@push('scripts')
  @vite('resources/js/password-toogle.js')
@endpush

@section('content')
  <div class="box register-box">
    <h2>Cadastrar</h2>

    @if ($errors->any())
      <div class="alert alert-danger">
        <strong>Opa! Algo deu errado:</strong>
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif


    <form method="POST" action="{{ route('register') }}" class="register-form">
      @csrf

      <div class="form-grid">
        <div class="form-group">
          <label for="name">Nome Completo</label>
          <input type="text" id="name" name="name" placeholder="Digite seu nome completo" value="{{ old('name') }}"
            required autofocus />
        </div>

        <div class="form-group">
          <label for="username">Usuário (Login)</label>
          <input type="text" id="username" name="username" placeholder="Escolha um nome de usuário"
            value="{{ old('username') }}" required />
        </div>

        <div class="form-group">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" placeholder="seu.email@dominio.com" value="{{ old('email') }}"
            required />
        </div>

        <div class="form-group">
          <label for="tipo_usuario">Eu sou</label>
          <select id="tipo_usuario" name="tipo_usuario" required>
            <option value="professor" {{ old('tipo_usuario') == 'professor' ? 'selected' : '' }}>Professor(a)</option>
            <option value="diretor" {{ old('tipo_usuario') == 'diretor' ? 'selected' : '' }}>Diretor(a)</option>
          </select>
        </div>

        <div class="form-group">
          <label for="id_escola">Minha Escola / Instituição</label>
          <select id="id_escola" name="id_escola" required>
            <option value="" disabled {{ old('id_escola') ? '' : 'selected' }}>Selecione sua escola</option>
            @foreach($escolas as $escola)
              <option value="{{ $escola->id_escola }}" {{ old('id_escola') == $escola->id_escola ? 'selected' : '' }}>
                {{ $escola->nome }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="data_nascimento">Data de Nascimento</label>
          <input type="date" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}" required />
        </div>

        <div class="form-group">
          <label for="cpf">CPF</label>
          <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" value="{{ old('cpf') }}" required />
        </div>

        <div class="form-group">
          <label for="rg">RG</label>
          <input type="text" id="rg" name="rg" placeholder="Digite seu RG" value="{{ old('rg') }}" required />
        </div>

        <div class="form-group">
          <label for="telefone">Telefone</label>
          <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" value="{{ old('telefone') }}"
            required />
        </div>

        <div class="form-group">
          <label for="rco_siape">Registro (SIAPE ou RCO)</label>
          <input type="text" id="rco_siape" name="rco_siape" placeholder="Ex: SIAPE123456" value="{{ old('rco_siape') }}"
            required />
        </div>

        <div class="form-group">
          <label for="formacao">Formação</label>
          <input type="text" id="formacao" name="formacao" placeholder="Ex: Licenciatura em Física"
            value="{{ old('formacao') }}" required />
        </div>

        <div class="form-group">
          <label for="area_formacao">Área de Formação</label>
          <input type="text" id="area_formacao" name="area_formacao" placeholder="Ex: Ciências Exatas"
            value="{{ old('area_formacao') }}" required />
        </div>

        <div class="form-group">
          <label for="password">Senha</label>
          <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="Crie uma senha (mín. 16 caracteres)"
              required />
            <i class="fas fa-eye toggle-password"></i>
          </div>
        </div>

        <div class="form-group">
          <label for="password_confirmation">Confirmar Senha</label>
          <div class="password-wrapper">
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirme a senha"
              required />
            <i class="fas fa-eye toggle-password"></i>
          </div>
        </div>

      </div>

      <div class="form-actions">
        <button type="submit" class="btn submit-btn">Cadastrar (Aguardar Aprovação)</button>
      </div>
    </form>
    <p class="login-link">
      Já tem uma conta?
      <a href="{{ route('login') }}">Entrar</a>
    </p>
  </div>

@endsection