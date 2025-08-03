
@extends('layouts.app')

@section('title', 'Cadastro de Usuário – NREduTech')

@section('content')
    <header class="header-section">
        <h1>Cadastro de Usuário</h1>
        <p class="subtitle">Preencha as informações para criar um novo usuário</p>
    </header>

    <section class="form-section">
        <form class="usuario-form" method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Nome Completo</label>
                    <input type="text" id="name" name="name" placeholder="Digite o nome completo" value="{{ old('name') }}" required />
                    @error('name')<span class="error-message">{{ $message }}</span>@enderror
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
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" placeholder="Crie uma senha" required />
                    @error('password')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar Senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirme a senha" required />
                </div>

                <div class="form-group">
                    <label for="role">Permissão (Papel)</label>
                    <select id="role" name="role" required>
                        <option value="" disabled selected>Selecione o papel</option>
                        <option value="professor" {{ old('role') == 'professor' ? 'selected' : '' }}>Professor</option>
                        <option value="gestor" {{ old('role') == 'gestor' ? 'selected' : '' }}>Gestor</option>
                        <option value="administrador" {{ old('role') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                    </select>
                    @error('role')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="birth_date">Data de Nascimento</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required/>
                    @error('birth_date')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                
                <div class="form-group">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" value="{{ old('cpf') }}" required />
                    @error('cpf')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="rg">RG</label>
                    <input type="text" id="rg" name="rg" placeholder="Digite o RG" value="{{ old('rg') }}" required />
                    @error('rg')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="phone">Telefone</label>
                    <input type="tel" id="phone" name="phone" placeholder="(00) 00000-0000" value="{{ old('phone') }}" required/>
                    @error('phone')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                 <div class="form-group">
                    <label for="registration_id">Registro (SIAPE ou RCO)</label>
                    <input type="text" id="registration_id" name="registration_id" placeholder="Ex: SIAPE123456" value="{{ old('registration_id') }}" required />
                    @error('registration_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="degree">Formação</label>
                    <input type="text" id="degree" name="degree" placeholder="Ex: Licenciatura em Física" value="{{ old('degree') }}" required />
                    @error('degree')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="field_of_study">Área de Formação</label>
                    <input type="text" id="field_of_study" name="field_of_study" placeholder="Ex: Ciências Exatas" value="{{ old('field_of_study') }}" required />
                    @error('field_of_study')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Cadastrar Usuário</button>
            </div>
        </form>
    </section>
@endsection