@extends('layouts.app')

@section('title', 'Cadastro de Usuário – NREduTech')

@push('scripts')
    @vite('resources/js/')
@endpush


@section('content')
<div class="main-content">
    <header class="header-section">
        <h1>Cadastro de Usuário</h1>
        <p class="subtitle">Preencha as informações para criar um novo usuário.</p>
    </header>

    <section class="form-section">
        <form class="usuario-form" method="POST" action="{{ route('usuarios.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="nome_completo">Nome Completo</label>
                    <input type="text" id="nome_completo" name="nome_completo" placeholder="Digite o nome completo" value="{{ old('nome_completo') }}" required />
                    @error('nome_completo')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="username">Usuário</label>
                    <input type="text" id="username" name="username" placeholder="Escolha um nome de usuário" value="{{ old('username') }}" required />
                    @error('username')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="usuario@dominio.com" value="{{ old('email') }}" required />
                    @error('email')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="tipo_usuario">Tipo de Usuário</label>
                    <select id="tipo_usuario" name="tipo_usuario" required>
                        <option value="" disabled selected>Selecione o tipo</option>
                        <option value="administrador" {{ old('tipo_usuario') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                        <option value="diretor" {{ old('tipo_usuario') == 'diretor' ? 'selected' : '' }}>Diretor</option>
                        <option value="professor" {{ old('tipo_usuario') == 'professor' ? 'selected' : '' }}>Professor</option>
                    </select>
                    @error('tipo_usuario')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="status_aprovacao">Status</label>
                    <select id="status_aprovacao" name="status_aprovacao" required>
                        <option value="pendente" {{ old('status_aprovacao', 'pendente') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="ativo" {{ old('status_aprovacao') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="bloqueado" {{ old('status_aprovacao') == 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
                    </select>
                    @error('status_aprovacao')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="data_nascimento">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}" required/>
                    @error('data_nascimento')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" value="{{ old('cpf') }}" required/>
                    @error('cpf')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="rg">RG</label>
                    <input type="text" id="rg" name="rg" placeholder="Digite o RG" value="{{ old('rg') }}" required/>
                    @error('rg')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="rco_siape">Registro (SIAPE ou RCO)</label>
                    <input type="text" id="rco_siape" name="rco_siape" placeholder="Ex: SIAPE123456" value="{{ old('rco_siape') }}" required/>
                    @error('rco_siape')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" value="{{ old('telefone') }}" required/>
                    @error('telefone')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="formacao">Formação</label>
                    <input type="text" id="formacao" name="formacao" placeholder="Ex: Licenciatura em Física" value="{{ old('formacao') }}" required/>
                    @error('formacao')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="area_formacao">Área de Formação</label>
                    <input type="text" id="area_formacao" name="area_formacao" placeholder="Ex: Ciências Exatas" value="{{ old('area_formacao') }}" required/>
                    @error('area_formacao')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Defina uma senha" required autocomplete="new-password" />
                        <i class="fas fa-eye toggle-password" aria-hidden="true"></i>
                    </div>
                    @error('password')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar Senha</label>
                    <div class="password-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirme a senha" required autocomplete="new-password" />
                        <i class="fas fa-eye toggle-password" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('usuarios.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Cadastrar Usuário</button>
            </div>
        </form>
    </section>
</div>
@endsection