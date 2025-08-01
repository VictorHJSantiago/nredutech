@extends('layouts.guest')

@section('title', 'Cadastro')

@section('content')
<div class="box register-box">
    <h2>Cadastrar</h2>

    <form method="POST" action="{{ route('register.store') }}" class="register-form">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label for="nomeCompleto">Nome Completo</label>
                <input type="text" id="nomeCompleto" name="nomeCompleto" placeholder="Digite seu nome completo" required />
            </div>

            <div class="form-group">
                <label for="dataNascimento">Data de Nascimento</label>
                <input type="date" id="dataNascimento" name="dataNascimento" required />
            </div>

            <div class="form-group">
                <label for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" required />
            </div>

            <div class="form-group">
                <label for="rg">RG</label>
                <input type="text" id="rg" name="rg" placeholder="Digite seu RG" required />
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="seu.email@dominio.com" required />
            </div>

            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" required />
            </div>

            <div class="form-group">
                <label for="registro">Registro (SIAPE ou RCO)</label>
                <input type="text" id="registro" name="registro" placeholder="Ex: SIAPE123456" required />
            </div>

            <div class="form-group">
                <label for="formacao">Formação</label>
                <input type="text" id="formacao" name="formacao" placeholder="Ex: Licenciatura em Física" required />
            </div>

            <div class="form-group">
                <label for="areaFormacao">Área de Formação</label>
                <input type="text" id="areaFormacao" name="areaFormacao" placeholder="Ex: Ciências Exatas" required />
            </div>

            <div class="form-group">
                <label for="regUsername">Usuário</label>
                <input type="text" id="regUsername" name="username" placeholder="Escolha um nome de usuário" required />
            </div>

            <div class="form-group">
                <label for="regPassword">Senha</label>
                <input type="password" id="regPassword" name="password" placeholder="Crie uma senha" required />
            </div>

            <div class="form-group">
                <label for="regConfirmPassword">Confirmar Senha</label>
                <input type="password" id="regConfirmPassword" name="password_confirmation" placeholder="Confirme a senha" required />
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